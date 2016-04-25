
<div ng-cloak ng-controller="rowsEditorController">

    <div class="ui basic segment" ng-repeat="sheet in file.sheets" ng-class="{loading: loading || saving}" style="overflow:auto">

        <md-list layout="row">
            <md-list-item>
                <md-button href="import" aria-label="返回">返回</md-button>
            </md-list-item>
            <md-list-item>
                <div class="ui pagination small menu" ng-if="paginate.last_page<=6 && paginate.last_page>1">
                    <a class="item" ng-repeat="i in generateArray(paginate.last_page)" ng-class="{active: paginate.current_page==i}" ng-click="getRows(i)">{{ i }}</a>
                </div>

                <div class="ui pagination small menu" ng-if="paginate.last_page>6">
                    <a class="icon item" ng-class="{disabled: paginate.current_page==1}" ng-click="prevPage()"><i class="left chevron icon"></i></a>

                    <a class="item" ng-class="{active: paginate.current_page==1}" ng-click="getRows(1)">1</a>
                    <a class="item no-animate disabled" ng-if="paginate.current_page!=2">..</a>
                    <a class="item no-animate" ng-if="paginate.current_page==paginate.last_page || paginate.current_page==paginate.last_page-1" ng-click="getRows(paginate.last_page-2)">{{ paginate.last_page-2 }}</a>
                    <a class="item no-animate active" ng-if="paginate.current_page!=1 && paginate.current_page!=paginate.last_page" ng-click="getRows(paginate.current_page)">{{ paginate.current_page }}</a>
                    <a class="item no-animate" ng-if="paginate.current_page==1 || paginate.current_page==2" ng-click="getRows(3)">3</a>
                    <a class="item no-animate disabled" ng-if="paginate.current_page!=paginate.last_page-1">..</a>
                    <a class="item" ng-class="{active: paginate.current_page==paginate.last_page}" ng-click="getRows(paginate.last_page)">{{ paginate.last_page }}</a>

                    <a class="icon item" ng-class="{disabled: paginate.current_page==paginate.last_page}" ng-click="nextPage()"><i class="right chevron icon"></i></a>
                </div>
            </md-list-item>
            <md-list-item ng-if="parentTables.length > 0">
                <md-menu>
                    <md-button aria-label="匯入歷史表單" ng-click="$mdOpenMenu($event)">
                        <md-icon md-menu-origin md-svg-icon="insert-drive-file"></md-icon>匯入過去資料
                    </md-button>
                    <md-menu-content width="4">
                        <md-menu-item>
                        <md-button ng-repeat="parentTable in parentTables" ng-click="cloneTableData(parentTable.id)">
                            <md-icon md-svg-icon="insert-drive-file" md-menu-align-target></md-icon>
                            {{parentTable.sheet.file.title}}
                        </md-button>
                        </md-menu-item>
                    </md-menu-content>
                </md-menu>
            </md-list-item>
            <md-list-item>
                <md-button md-colors="{background: 'red'}" ng-if="(paginate.data | filter: {selected: true}).length > 0"
                    ng-class="{loading: status.deleting}"
                    ng-click="delete()">
                    <i class="trash icon"></i>刪除名單 ({{ (paginate.data | filter: {selected: true}).length }}筆資料)
                </md-button>

                <md-button md-colors="{background: 'green'}" ng-if="(paginate.data | filter: {updating: true}).length > 0" ng-click="updateRows()">儲存</md-button>
            </md-list-item>
        </md-list>

        <table class="ui very compact table" ng-repeat="table in sheet.tables">
            <thead>
                <tr>
                    <th colspan="{{ table.columns.length+3 }}" class="right aligned">
<!--                         <div class="ui left action icon input" ng-class="{loading: loading}">
                            <button class="ui labeled icon basic button" ng-click="searchText='';getRows(1, '')"><i class="unhide icon"></i> 顯示全部 </button>
                            <input type="text" ng-model="searchText" placeholder="搜尋..." />
                            <i class="search link icon" ng-click="getRows(1, searchText)"></i>
                        </div> -->
                        第{{ paginate.from }}-{{ paginate.to }}列(共有{{ paginate.total }}列) </th>
                </tr>
                <tr>
                    <th class="collapsing" ng-if="paginate.data[0].created_by">上傳者</th>
                    <th class="collapsing">
                        <md-checkbox aria-label="全選" ng-checked="isChecked()" md-indeterminate="isIndeterminate()" ng-click="toggleAll()"></md-checkbox>
                    </th>
                    <th ng-repeat="column in table.columns">{{ column.title }}</th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="row in paginate.data" ng-class="{warning: row.writing, disabled: row.saving}">
                    <td ng-if="row.created_by"><md-button>{{ row.created_by }}</md-button></td>
                    <td>
                        <md-checkbox ng-model="row.selected" aria-label="刪除"></md-checkbox>
                    </td>
                    <td ng-repeat="column in table.columns" ng-if="true||row.writing">
                        <md-input-container md-no-float class="md-block" ng-if="column.rules!='bool' && column.rules!='menu'">
                            <input ng-model="row['C' + column.id]" ng-disabled="column.readonly" placeholder="{{column.title}}" ng-change="setUpdating(row)">
                            <div class="md-input-messages-animation" ng-repeat="(id, errors) in row.errors" ng-if="id == column.id">
                                <div class="no-animate" ng-repeat="error in errors">
                                    {{error}}
                                </div>
                            </div>
                        </md-input-container>
                        <md-checkbox
                            ng-model="row['C' + column.id]"
                            ng-disabled="column.readonly"
                            ng-true-value="'1'"
                            ng-false-value="'0'"
                            ng-if="column.rules=='bool'"
                            ng-change="setUpdating(row)"
                            aria-label="column.name"></md-checkbox>
                        <md-select ng-model="row['C' + column.id]" ng-change="setUpdating(row)" ng-if="column.rules=='menu'" aria-label="column.name">
                            <md-option ng-repeat="anaser in column.answers" ng-value="anaser.value">
                                {{anaser.title}}
                            </md-option>
                        </md-select>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>

</div>

<script>
app.controller('rowsEditorController', function($scope, $http, $filter) {
    $scope.file = {sheets: [], comment: ''};
    $scope.paginate = {data: []};
    $scope.status = {};
    $scope.loading = false;
    $scope.saving = false;
    $scope.parentTables = false;

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

    $scope.generateArray = function(max) {
        var array = [];
        for (var i = 0; i < max; i++) {
            array[i] = i+1;
        };
        return array;
    };

    $scope.isChecked = function() {
        var selected = $filter('filter')($scope.paginate.data, {selected: true});
        return $scope.paginate.data.length > 0 && selected.length === $scope.paginate.data.length;
    };

    $scope.isIndeterminate = function() {
        var selected = $filter('filter')($scope.paginate.data, {selected: true});
        return (selected.length !== 0 && selected.length !== $scope.paginate.data.length);
    };

    $scope.toggleAll = function() {
        var selected = $filter('filter')($scope.paginate.data, {selected: true});
        if (selected.length === $scope.paginate.data.length) {
            for (var i in selected) {
                selected[i].selected = false;
            };
        } else if (selected.length === 0 || selected.length > 0) {
            for (var i in $scope.paginate.data) {
                $scope.paginate.data[i].selected = true;
            };
        }
    };

    $scope.setUpdating = function(row) {
        row.updating = true;
    };

    $scope.updateRows = function() {
        $scope.saving = true;
        var rows = $filter('filter')($scope.paginate.data, {updating: true});
        $http({method: 'POST', url: 'updateRows', data:{rows: rows}})
        .success(function(data, status, headers, config) {
            for (var i in rows) {
                var row = $filter('filter')(data.updated, {id: rows[i].id})[0];
                rows[i].updating = !row.updated;
                rows[i].errors = row.errors;
                $scope.saving = false;
            };
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getParentTable = function() {
        $scope.parentTables = [];
        $http({method: 'POST', url: 'getParentTable', data:{}})
        .success(function(data, status, headers, config) {
           $scope.parentTables = data;
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getParentTable();

    $scope.cloneTableData = function(table_id) {
        $scope.loading = true;
        var data = {table_id:table_id};
        $http({method: 'POST', url: 'cloneTableData', data:data})
        .success(function(data, status, headers, config) {
            $scope.getRows();
            $scope.loading = false;
        }).error(function(e){
            console.log(e);
        });
    };

});
</script>