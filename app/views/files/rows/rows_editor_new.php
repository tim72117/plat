
<md-content ng-cloak ng-controller="rowsEditorController" layout="row" style="height:100%">

    <md-sidenav class="md-sidenav-right" md-is-open="mdSidenav.editing.opening" layout="column" style="min-width:500px">
        <md-toolbar class="md-theme-indigo">
            <h1 class="md-toolbar-tools">
                <span>編輯資料列</span>
                <span flex></span>
                <md-button ng-click="updateRow()" class="md-raised">儲存</md-button>
            </h1>
        </md-toolbar>
        <md-content layout-padding>
            <div ng-repeat="column in file.sheets[0].tables[0].columns">
                <md-input-container class="md-block" ng-if="column.rules!='bool' && !column.answers">
                    <input ng-model="mdSidenav.editing['C' + column.id]" ng-disabled="column.readonly" placeholder="{{column.title}}" ng-change="setUpdating(mdSidenav.editing)">
                    <div class="md-input-messages-animation" ng-repeat="(id, errors) in mdSidenav.editing.errors" ng-if="id == column.id">
                        <div class="no-animate" ng-repeat="error in errors">
                            {{error}}
                        </div>
                    </div>
                </md-input-container>
                <md-checkbox ng-if="column.rules=='bool'"
                    ng-model="mdSidenav.editing['C' + column.id]"
                    ng-disabled="column.readonly"
                    ng-true-value="'1'"
                    ng-false-value="'0'"
                    ng-change="setUpdating(mdSidenav.editing)"
                    aria-label="column.name">{{column.title}}</md-checkbox>
                <md-select ng-if="column.answers.length > 0"
                    ng-model="mdSidenav.editing['C' + column.id]"
                    ng-disabled="column.readonly"
                    ng-change="setUpdating(mdSidenav.editing)"
                    aria-label="column.name">
                    <md-option ng-repeat="anaser in column.answers" ng-value="anaser.value">
                        {{anaser.title}}
                    </md-option>
                </md-select>
            </div>
        </md-content>
    </md-sidenav>

    <div flex layout="column">
        <md-toolbar md-colors="{background: 'grey-100'}">
            <div class="md-toolbar-tools">
                <span></span>
                <md-button class="md-raised" md-colors="{background: 'pink-400'}" aria-label="刪除" ng-if="(paginate.data | filter: {selected: true}).length > 0" ng-click="delete()" ng-hide="lock">
                    <md-icon md-svg-icon="delete"></md-icon>
                    <md-tooltip><h4>刪除</h4></md-tooltip>
                    刪除{{(paginate.data | filter: {selected: true}).length}}筆資料
                </md-button>
                <span flex></span>
                <md-button class="md-icon-button md-raised md-primary" aria-label="下載已上傳名單" ng-click="exportRows(file.sheets[0])">
                    <md-icon md-svg-icon="file-download"></md-icon>
                    <md-tooltip><h4>下載已上傳名單</h4></md-tooltip>
                </md-button>
                <!--<md-button class="md-icon-button md-raised md-primary" aria-label="點擊這邊新增一筆資料" ng-click="toggleDetail({})">
                    <md-icon md-svg-icon="add"></md-icon>
                    <md-tooltip md-visible="addTooltipVisible"><h4>點擊這邊新增一筆資料</h4></md-tooltip>
                </md-button>-->
            </div>
        </md-toolbar>
        <md-divider></md-divider>
        <md-content style="height:100%">
            <div style="height:100%;overflow:scroll;;background-color:#fff">
                <div ng-repeat="table in file.sheets[0].tables" style="display:table;width:100%;">
                    <div style="display: table-row">
                        <div style="display: table-cell;border-bottom: 1px solid rgba(0,0,0,0.12);padding:20px 0 0 15px">
                            <md-checkbox aria-label="全選" ng-checked="isChecked()" md-indeterminate="isIndeterminate()" ng-click="toggleAll()" ng-disabled="lock"></md-checkbox>
                        </div>
                        <div style="display: table-cell;border-bottom: 1px solid rgba(0,0,0,0.12);padding:20px 0 0 10px;max-width:50px" ng-repeat="column in table.columns">{{ column.title }}</div>
                        <div style="display: table-cell;border-bottom: 1px solid rgba(0,0,0,0.12);padding:20px 0 0 0;width:80px"></div>
                    </div>
                    <div style="display: table-row" ng-repeat="row in paginate.data" class="selectable" md-colors="{background: row.opening ? 'grey' : 'grey-A100'}" ng-click="row.selected=!row.selected && !lock">
                        <div style="display: table-cell;border-bottom: 1px solid rgba(0,0,0,0.12);padding:15px 0 15px 15px">
                            <md-checkbox aria-label="選擇" ng-model="row.selected" ng-click="$event.stopPropagation()" ng-disabled="lock"></md-checkbox>
                        </div>
                        <div style="display: table-cell;border-bottom: 1px solid rgba(0,0,0,0.12);padding:15px 0 0 10px;max-width:50px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis" ng-repeat="column in table.columns">
                            <span ng-if="column.answers.length > 0" ng-repeat="answer in column.answers | filter:{value: row['C' + column.id]}">{{answer.title}}</span>
                            <span ng-if="!column.answers">{{row['C' + column.id]}}</span>
                        </div>
                        <div style="display: table-cell;border-bottom: 1px solid rgba(0,0,0,0.12);padding:15px 0">
                            <md-button aria-label="編輯" ng-click="toggleDetail(row);$event.stopPropagation()">編輯</md-button>
                        </div>
                    </div>
                </div>
                <div layout-padding layout="row" layout-align="center center">
                        <div layout="row">
                            <md-input-container md-no-float flex="none">
                                <label>選擇搜尋欄位</label>
                                <md-select ng-model="search.column_id">
                                    <md-option ng-repeat="column in file.sheets[0].tables[0].columns" value="{{column.id}}">
                                    {{column.title}}
                                    </md-option>
                                </md-select>
                            </md-input-container>
                            <md-input-container>
                                <label>輸入搜尋字串</label>
                                <input type="text" ng-model="search.text">
                            </md-input-container>
                            <md-button class="md-fab md-mini" ng-click="getRows(1)"><md-icon md-svg-icon="find-in-page" aria-label="搜尋"></md-icon></md-button>
                        </div>
                    <span flex></span>
                    <md-select ng-model="paginate.current_page" placeholder="所在頁數" class="md-no-underline" style="margin:0" ng-change="getRows(paginate.current_page)">
                        <md-option ng-repeat="i in generateArray(paginate.last_page) | limitTo:10" value="{{ i }}">{{ i }}</md-option>
                    </md-select>
                    <span>{{(paginate.current_page-1)*paginate.per_page+1}}-{{paginate.current_page*paginate.per_page}} of {{paginate.last_page}}</span>
                    <md-button class="md-icon-button" aria-label="前一頁" ng-click="prevPage()">
                        <md-icon md-svg-icon="keyboard-arrow-left"></md-icon>
                        <md-tooltip><h5>前一頁</h5></md-tooltip>
                    </md-button>
                    <md-button class="md-icon-button" aria-label="下一頁" ng-click="nextPage()">
                        <md-icon md-svg-icon="keyboard-arrow-right"></md-icon>
                        <md-tooltip><h4>下一頁</h4></md-tooltip>
                    </md-button>
                <div>
            </div>
        </md-content>
    </div>

</md-content>
<style>
.selectable:focus {
    outline: none;
}
.selectable:hover {
    cursor: pointer;
}
md-tooltip .md-content {
    height: auto;
}
.box {
  -webkit-box-sizing: border-box;
     -moz-box-sizing: border-box;
          box-sizing: border-box;
}
</style>
<script src="/js/jquery.fileDownload.js"></script>
<script>
app.controller('rowsEditorController', function($scope, $http, $filter, $mdToast, $mdSidenav, $timeout, $mdColors) {
    $scope.file = {sheets: [], comment: ''};
    $scope.paginate = {data: []};
    $scope.status = {};
    $scope.saving = false;
    $scope.parentTables = false;
    $scope.search = {};
    $scope.mdSidenav = {};
    $scope.addTooltipVisible = false;
    $scope.lock = false;

    $scope.getStatus = function() {
        $scope.$parent.main.loading = true;
        $http({method: 'POST', url: 'get_file', data:{editor: false}})
        .success(function(data, status, headers, config) {
            $scope.file.sheets = data.sheets;
            $scope.file.comment = data.comment;
            $scope.getRows(1);
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getStatus();

    $scope.getRows = function(page) {
        $scope.$parent.main.loading = true;
        $http({method: 'POST', url: 'get_rows', data:{page: page, search: $scope.search}})
        .success(function(data, status, headers, config) {
            $scope.lock = data.lock;
            $scope.paginate = data.paginate;
            $scope.$parent.main.loading = false;
        }).error(function(e){
            $scope.$parent.main.loading = false;
            $mdToast.show($mdToast.simple().content('錯誤，請聯絡系統人員 ' + angular.element(e).children().text()));
        });
    };

    $scope.delete = function() {
        var rows = $filter('filter')($scope.paginate.data, {selected: true});
        $http({method: 'POST', url: 'delete_rows', data:{rows: rows.map(function(row){ return row.id; })}})
        .success(function(data, status, headers, config) {
            angular.forEach(rows, function(row, key) {
                $scope.paginate.data.splice($scope.paginate.data.indexOf(row), 1);
            });
            $scope.getRows(1);
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
        var data = {table_id:table_id};
        $http({method: 'POST', url: 'cloneTableData', data:data})
        .success(function(data, status, headers, config) {
            $scope.getRows();
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.exportRows = function(sheet) {
        jQuery.fileDownload('export_my_rows', {
            httpMethod: "POST",
            data: {sheet_id: sheet.id},
            failCallback: function (responseHtml, url) { console.log(responseHtml); }
        });
    };

    $scope.toggleDetail = function(row) {
        $scope.mdSidenav.editing = row;
        $scope.mdSidenav.editing.opening = true;
    };

    $scope.updateRow = function() {
        $http({method: 'POST', url: 'updateRows', data:{rows: [$scope.mdSidenav.editing]}})
        .success(function(data, status, headers, config) {
            $scope.mdSidenav.editing.errors = data.updated[0].errors;
            $scope.mdSidenav.editing.opening = !data.updated[0].updated;
        }).error(function(e) {
            console.log(e);
        });
    };

    $scope.getRGBA = function(pattern) {
        return $mdColors.getThemeColor(pattern);
    };

    $timeout(function() {
        $scope.addTooltipVisible = true;
        $timeout(function() {
            $scope.addTooltipVisible = false;
        }, 2000);
    }, 2000);

});
</script>