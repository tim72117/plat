<div ng-cloak ng-controller="rowsEditorController">

    <div class="ui basic segment" ng-repeat="sheet in file.sheets" ng-class="{loading: loading}" style="overflow:auto">

        <a class="ui button" href="import">
            <i class="reply icon"></i>返回
        </a>


        <div class="ui pagination small menu" ng-if="paginate.last_page<=6 && paginate.last_page>1">
            <a class="item" ng-repeat="i in array(paginate.last_page)" ng-class="{active: paginate.current_page==i}" ng-click="getRows(i)">{{ i }}</a>
        </div>

        <div class="ui pagination small menu" ng-if="paginate.last_page>6">
            <a class="icon item" ng-class="{disabled: paginate.current_page==1}" ng-click="prevPage()"><i class="left chevron icon"></i></a>

            <a class="item" ng-class="{active: paginate.current_page==1}" ng-click="getRows(1)">1</a>
            <a class="item disabled" ng-if="paginate.current_page!=2">..</a>
            <a class="item" ng-if="paginate.current_page==paginate.last_page || paginate.current_page==paginate.last_page-1" ng-click="getRows(paginate.last_page-2)">{{ paginate.last_page-2 }}</a>
            <a class="item active" ng-if="paginate.current_page!=1 && paginate.current_page!=paginate.last_page" ng-click="getRows(paginate.current_page)">{{ paginate.current_page }}</a>
            <a class="item" ng-if="paginate.current_page==1 || paginate.current_page==2" ng-click="getRows(3)">3</a>
            <a class="item disabled" ng-if="paginate.current_page!=paginate.last_page-1">..</a>
            <a class="item" ng-class="{active: paginate.current_page==paginate.last_page}" ng-click="getRows(paginate.last_page)">{{ paginate.last_page }}</a>

            <a class="icon item" ng-class="{disabled: paginate.current_page==paginate.last_page}" ng-click="nextPage()"><i class="right chevron icon"></i></a>
        </div>
        第{{ paginate.from }}-{{ paginate.to }}列(共有{{ paginate.total }}列)


        <div class="ui left action icon input" ng-class="{loading: loading}">
            <button class="ui labeled icon button" ng-click="searchText='';getRows(1, '')"><i class="unhide icon"></i> 顯示全部 </button>
            <input type="text" ng-model="searchText" placeholder="搜尋..." />
            <i class="search link icon" ng-click="getRows(1, searchText)"></i>
        </div>

        <div class="ui buttons">
            <div class="ui red button" ng-if="(paginate.data | filter: {selected: true}).length > 0"
                ng-class="{loading: status.deleting}"
                ng-click="status.confrim=true">
                <i class="trash icon"></i>刪除名單 ({{ (paginate.data | filter: {selected: true}).length }}筆資料)
            </div>
            <div class="ui button" ng-if="(paginate.data | filter: {selected: true}).length > 0 && status.confrim" ng-click="delete()">
                <i class="checkmark icon"></i>確定
            </div>
        </div>

        <table class="ui very compact collapsing table" ng-repeat="table in sheet.tables">
            <thead>
                <tr>
                    <th class="collapsing">
                        <div class="ui checkbox" ng-click="selectAll()">
                            <input type="checkbox" ng-model="paginate.allSelected" />
                            <label></label>
                        </div>
                    </th>
                    <th class="collapsing" ng-if="paginate.data[0].created_by">上傳者</th>
                    <th class="collapsing">編輯</th>
                    <th ng-repeat="column in table.columns">{{ column.title }}</th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="row in paginate.data" ng-class="{warning: row.writing, disabled: row.saving}">
                    <td>
                        <div class="ui checkbox">
                            <input type="checkbox" ng-model="row.selected" />
                            <label></label>
                        </div>
                    </td>
                    <td ng-if="row.created_by">{{ row.created_by }}</td>
                    <td>
                        <div class="ui icon basic button" ng-if="!row.writing" ng-click="row.writing=true"><i class="icon edit"></i></div>
                        <div class="ui icon positive button" ng-if="row.writing" ng-click="saveRow(row)" ng-class="{loading: row.saving}"><i class="icon save"></i></div>
                    </td>
                    <td ng-repeat="column in table.columns" ng-if="!row.writing">{{ row['C' + column.id] }}</td>
                    <td ng-repeat="column in table.columns" ng-if="row.writing">
                        <div class="ui input" ng-class="{error: row.errors[column.id]}">
                            <input type="text" ng-model="row['C' + column.id]" placeholder="...">
                        </div>
                    </td>
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
        $http({method: 'POST', url: 'get_file', data:{editor: false}})
        .success(function(data, status, headers, config) {
            $scope.file.sheets = data.sheets;
            $scope.file.comment = data.comment;
            $scope.getRows();
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
            $scope.getRows();
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.nextPage = function() {
        if ($scope.paginate.current_page!=$scope.paginate.last_page) {
            $scope.getRows($scope.paginate.current_page+1);
        }
    };

    $scope.prevPage = function() {
        if ($scope.paginate.current_page!=1) {
            $scope.getRows($scope.paginate.current_page-1);
        }
    };

    $scope.array = function(max) {
        var array = [];
        for (var i = 0; i < max; i++) {
            array[i] = i+1;
        };
        return array;
    };

    $scope.selectAll = function() {
        for (var i in $scope.paginate.data) {
            $scope.paginate.data[i].selected = $scope.paginate.allSelected;
        };
    };

    $scope.saveRow = function(row) {
        row.saving = true;
        $http({method: 'POST', url: 'saveRow', data:{row: row}})
        .success(function(data, status, headers, config) {
            row.saving = false;
            row.errors = data.status.errors;
            if (row.errors.length==0 && data.status.updated) {
                row.writing = false;
            }
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.$watch('paginate.data', function(p) {
        $scope.status.confrim = false;
    }, true);
});
</script>