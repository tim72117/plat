
<div ng-cloak ng-controller="rowsEditorController" style="position:absolute;top:10px;left:10px;right:10px;bottom:10px;overflow-y: auto;padding:1px">
	<div class="ui segment" ng-class="{loading: loading}" ng-repeat="sheet in file.sheets">

	    <a class="ui mini button" href="import">
	        <i class="reply icon"></i>返回
	    </a> 
		<div class="ui mini red button" ng-show="(rows | filter: {selected: true}).length > 0"
			ng-class="{'left attached': status.confrim, loading: status.deleting}"
			ng-click="status.confrim=true">
			<i class="trash icon"></i>刪除名單 ({{ (rows | filter: {selected: true}).length }}筆資料)
		</div>
		<div class="ui mini right attached button" ng-show="(rows | filter: {selected: true}).length > 0 && status.confrim" ng-click="delete()">
			<i class="checkmark icon"></i> 確定
		</div>

		<table class="ui very basic very compact collapsing table" ng-repeat="table in sheet.tables">
			<thead>
				<tr>
					<td width="60"></td>
					<th ng-repeat="column in table.columns">{{ column.title }}</th>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="row in rows">   
					<td>
						<div class="ui checkbox" ng-click="status.confrim = false">
							<input type="checkbox" ng-model="row.selected" />
							<label></label>
						</div>
					</td>
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

    $scope.getStatus = function(input) {
    	$scope.loading = true;
        $http({method: 'POST', url: 'get_file', data:{editor: false} })
        .success(function(data, status, headers, config) {
            $scope.file.sheets = data.sheets;
            $scope.file.comment = data.comment;
            $scope.getMyRows();            
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getStatus();
    
    $scope.getMyRows = function(input) {
    	$scope.loading = true;
        $http({method: 'POST', url: 'get_my_rows', data:{} })
        .success(function(data, status, headers, config) {
            $scope.rows = data.rows;
            $scope.loading = false;
        }).error(function(e){
            console.log(e);
        });
    };    

    $scope.delete = function() {
    	var rows = $filter('filter')($scope.rows, {selected: true});
        $http({method: 'POST', url: 'delete_my_rows', data:{rows: rows.map(function(row){ return row.id; })} })
        .success(function(data, status, headers, config) {
            angular.forEach(rows, function(row, key) {
            	$scope.rows.splice($scope.rows.indexOf(row), 1);    
            });
        }).error(function(e){
            console.log(e);
        });
    };  

});
</script>