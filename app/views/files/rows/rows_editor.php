
<div ng-cloak ng-controller="rowsEditorController">
	<div class="ui segment" ng-repeat="sheet in file.sheets">

	    <a class="ui button" href="import">
	        <i class="reply icon"></i>返回
	    </a> 

	    <div class="ui left action icon input" ng-class="{loading: loading}">
	    	<button class="ui labeled icon button" ng-click="searchText='';getRows(1, '')"><i class="unhide icon"></i> 顯示全部 </button>
	    	<input type="text" ng-model="searchText" placeholder="搜尋..." />	    	
	    	<i class="search link icon" ng-click="getRows(1, searchText)"></i>
	    </div>

<!-- <div ng-repeat="max in [1,2,3,4,5,6,7,8]">
	<div ng-repeat="c in [1,2,3,4,5,6,7,8]" ng-if="c<=max">

		<div class="ui pagination small menu">
			<a class="icon item" ng-click="prevPage()">
				<i class="left chevron icon"></i>
			</a>


			<a class="item" ng-repeat="i in [1,2,3]" ng-if="max>=i && max<6" ng-class="{active: c==i}">{{ i }}</a>
	
			<a class="item disabled" ng-if="paginate.last_page>4">...</a>
			<a class="item active" ng-if="paginate.current_page>3 && paginate.current_page!=paginate.last_page">{{ paginate.current_page }}</a>
			<a class="item" ng-if="paginate.last_page>3" ng-class="{active: paginate.current_page==paginate.last_page}">{{ paginate.last_page }}</a>


			<div class="disabled item" ng-if="paginate.last_page>5 && paginate.current_page!=1 && paginate.current_page!=2">... </div>
			<a class="item active">{{ paginate.current_page }}</a>
			<div class="disabled item" ng-if="paginate.last_page>5 && paginate.current_page!=paginate.last_page && paginate.current_page!=paginate.last_page-1">... </div>

			<a class="icon item" ng-click="nextPage()">
				<i class="right chevron icon"></i>
			</a>
		</div>

	</div>
</div> -->

		<div class="ui mini red button" ng-show="(paginate.data | filter: {selected: true}).length > 0"
			ng-class="{'left attached': status.confrim, loading: status.deleting}"
			ng-click="status.confrim=true">
			<i class="trash icon"></i>刪除名單 ({{ (paginate.data | filter: {selected: true}).length }}筆資料)
		</div>
		<div class="ui mini right attached button" ng-show="(paginate.data | filter: {selected: true}).length > 0 && status.confrim" ng-click="delete()">
			<i class="checkmark icon"></i> 確定
		</div>

		<table class="ui very basic very compact collapsing table" ng-repeat="table in sheet.tables">
			<thead>
				<tr>
					<th width="60"></th>
					<th width="50" ng-if="paginate.data[0].created_by">上傳者</th>
					<th ng-repeat="column in table.columns">{{ column.title }}</th>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="row in paginate.data">  
					<td>
						<div class="ui checkbox" ng-click="status.confrim = false">
							<input type="checkbox" ng-model="row.selected" />
							<label></label>
						</div>
					</td>
					<td ng-if="row.created_by">{{ row.created_by }}</td>
					<td ng-repeat="column in table.columns">{{ row['C' + column.id] }}</td>
				</tr>
			</tbody>
		</table>

	</div>
</div>

<script>
app.controller('rowsEditorController', function($scope, $http, $filter) {
    $scope.file = {sheets: [], comment: ''};
    $scope.status = {};
    $scope.loading = true;

    $scope.getStatus = function() {
    	$scope.loading = true;
        $http({method: 'POST', url: 'get_file', data:{editor: false}})
        .success(function(data, status, headers, config) {
            $scope.file.sheets = data.sheets;
            $scope.file.comment = data.comment; 
            $scope.loading = false;          
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getStatus();
    
    $scope.getRows = function(page, searchText) {
    	$scope.loading = true;
        $http({method: 'POST', url: 'get_rows', data:{page: page, searchText: searchText}})
        .success(function(data, status, headers, config) {            
            $scope.paginate = data.paginate;
            $scope.loading = false;
        }).error(function(e){
            console.log(e);
        });
    };    

    $scope.delete = function() {
    	var rows = $filter('filter')($scope.paginate.data, {selected: true});
        $http({method: 'POST', url: 'delete_rows', data:{rows: rows.map(function(row){ return row.id; })}})
        .success(function(data, status, headers, config) {
            angular.forEach(rows, function(row, key) {
            	$scope.paginate.data.splice($scope.paginate.data.indexOf(row), 1);    
            });
        }).error(function(e){
            console.log(e);
        });
    }; 

    $scope.nextPage = function() {
    	$scope.getMyRows($scope.paginate.current_page+1);
    };  

    $scope.prevPage = function() {
    	$scope.getMyRows($scope.paginate.current_page-1);
    };   

});
</script>