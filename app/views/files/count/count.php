<div ng-cloak ng-controller="CountController">

    <div class="ui segment" ng-class="{loading: loading}" style="position:absolute;top:10px;left:10px;right:10px;bottom:10px">

		<div class="ui compact left floated segment">

			<h4>請選擇要計算次數的項目</h4>

            <div class="ui list" style="overflow-y: auto;max-height:600px">

                <div class="item" ng-repeat="column in columns">
                    <div class="content">
                        <div class="ui checkbox">
                            <input type="checkbox" id="column-{{ $index }}" ng-model="column.selected" ng-click="getCount()" />
                            <label for="column-{{ $index }}">{{ column.COLUMN_NAME }}</label>
                        </div>  

                    </div>
                </div> 
                
            </div>

            <h4>增加過濾條件</h4>

            <div class="ui list" style="overflow-y: auto;max-height:600px">

                <div class="item" ng-repeat="column in columns">
                    <div class="content">

                        <div class="ui checkbox">
                            <input type="checkbox" id="filter-{{ $index }}" ng-model="column.isFilter" ng-click="getVariable(column)" />
                            <label for="filter-{{ $index }}">{{ column.COLUMN_NAME }}</label>
                        </div>  

                    </div>

                    <div class="ui list" ng-if="column.variables.length > 0">
                    	<div class="item" ng-repeat="variable in column.variables">
		                    <div class="content">

		                        <div class="ui checkbox">
		                            <input type="checkbox" id="variable-{{ column.COLUMN_NAME }}-{{ $index }}" ng-model="variable.selected" ng-click="getCount()" />
		                            <label for="variable-{{ column.COLUMN_NAME }}-{{ $index }}">{{ variable.name }}</label>
		                        </div>  

		                    </div>
                    	</div>
                    </div> 	
                </div> 
                
            </div>

        </div>


	        <table class="ui collapsing table" ng-if="table=='frequence'">
	        	<thead>
	        		<tr>
	        			<th class="right aligned">選項</th>
	        			<th class="right aligned">數量</th>
	                    <th class="right aligned">百分比</th>
	        		</tr>
	        	</thead>
		        <tbody>
		            <tr ng-repeat="frequence in frequences | orderBy:'name'">
		            	<td class="right aligned" style="max-width:800px">{{ frequence.name }}</td>
		                <td class="right aligned">{{ frequence.total || 0 }}</td>
	                    <td class="right aligned">{{ total ? ((100*frequence.total/total) | number:1) : 0 }}%</td>
		            </tr>
	                <tr ng-if="frequence['n']">
	                    <td class="right aligned">無須填答</td>
	                    <td class="right aligned">{{ frequence['n'] }}</td>
	                    <td class="right aligned">{{ (100*frequence['n']/total) | number:1 }}%</td>
	                </tr> 
	                <tr>
	                    <td class="right aligned">總數</td>
	                    <td class="right aligned">{{ total }}</td>
	                    <td class="right aligned"></td>
	                </tr>    
		        </tbody>
	    	</table>

	    	<table class="ui collapsing definition table" ng-if="table=='crosstable'">
	    		<thead>
		    		<tr>
		    			<th class="right aligned"></th>
		        		<th class="right aligned" ng-repeat="horizontal_column in horizontal_columns">{{ horizontal_column }}</th>
		        	</tr>
	    		</thead>	
	    		<tbody>
		    		<tr ng-repeat="vertical_column in vertical_columns">
		    			<td class="right aligned">{{ vertical_column }}</td>
		        		<td class="right aligned" ng-repeat="horizontal_column in horizontal_columns">{{ crosstable[horizontal_column][vertical_column] || 0 }}</td>
		        	</tr>
	        	</tbody>
	    	</table>



	

    </div>

</div>

<script>
app.controller('CountController', function($scope, $filter, $interval, $http) {
	$scope.columns = [];
	$scope.loading = false;

    $scope.getColumns = function() {
        $http({method: 'POST', url: 'get_columns', data:{} })
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.columns = data.columns;
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getVariable = function(column) { 
        if( !column.isFilter ) {
            column.variables = [];
            return true;
        }
        $http({method: 'POST', url: 'get_variable', data:{name: column.COLUMN_NAME} })
        .success(function(data, status, headers, config) {
            //console.log(data);
            column.variables = data.variables;
        }).error(function(e){
            console.log(e);
        });
    }

    $scope.getCount = function() {
    	var columns = $filter("filter")($scope.columns, {selected: true});
    	if( columns.length == 0 ) {
    		$scope.frequences = [];
    		$scope.total = 0;
    	}
    	else
    	if(columns.length == 1) {
    		$scope.table = 'frequence';
    		$scope.getFrequence(columns[0]);
    	}
    	if(columns.length == 2) {
    		$scope.table = 'crosstable';
    		$scope.getCrossTable(columns[0], columns[1]);
    	}
    }

    $scope.reCount = function(column) {
		var variable = $filter("filter")(column.variables, {selected: true}); 	
    }

    $scope.getFrequence = function(column) {  
    	$scope.loading = true;
        $http({method: 'POST', url: 'get_frequence', data:{name: column.COLUMN_NAME, columns: btoa(encodeURIComponent(angular.toJson($scope.columns)))} })
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.frequences = data.frequences;
            $scope.total = 0 ;
            angular.forEach($scope.frequences, function(value) {
                $scope.total += (value.total || 0)*1;
            });
            $scope.loading = false;
        }).error(function(data, status){
            console.log(status);
        });
    };

    $scope.getCrossTable = function(column1, column2) {    	
        $http({method: 'POST', url: 'get_crosstable', data:{name1: column1.COLUMN_NAME, name2: column2.COLUMN_NAME} })
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.crosstable = data.crosstable;
            $scope.horizontal_columns = data.columns_horizontal;
            $scope.vertical_columns = data.columns_vertical;
            $scope.total = 0 ;
            angular.forEach($scope.frequences, function(value) {
                $scope.total += (value.total || 0)*1;
            });
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getColumns();
});

</script>