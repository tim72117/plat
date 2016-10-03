
<div ng-cloak ng-controller="newTableController">

    <md-toolbar class="md-menu-toolbar">
        <div layout="row">
            <md-toolbar-filler layout layout-align="center center">
                <md-icon md-svg-icon="insert-drive-file"></md-icon>
            </md-toolbar-filler>
            <div>
            <h2 class="md-toolbar-tools">{{file.sheets[0].title}}<div class="ui red label"><div class="detail">{{ file.sheets[0].tables[0].count }}筆</div></div></h2>
            <md-menu-bar>
                <md-menu>
                    <button ng-click="$mdOpenMenu()">
                        檔案
                    </button>
                    <md-menu-content>
                        <md-menu-item ng-if="tool=='columns'">
                            <md-button ng-disabled="tool=='columns' && !table.lock && file.sheets[0].editable" ng-click="updateSheet(true)">修改</md-button>
                        </md-menu-item>
                        <md-menu-item ng-if="tool=='columns'">
                            <md-button ng-disabled="tool=='columns' && !table.lock && !file.sheets[0].editable" ng-click="updateSheet(false)">完成</md-button>
                        </md-menu-item>
                        <md-menu-item ng-if="tool=='comment'">
                            <md-button ng-click="saveComment()">儲存</md-button>
                        </md-menu-item>
                    </md-menu-content>
                </md-menu>
                <md-menu>
                    <button ng-click="$mdOpenMenu()">
                        設計
                    </button>
                    <md-menu-content>
                        <md-menu-item>
                            <md-button ng-click="changeTool('columns')">欄位定義</md-button>
                        </md-menu-item>
                        <md-menu-item>
                            <md-button ng-click="changeTool('comment')">說明文件</md-button>
                        </md-menu-item>
                    </md-menu-content>
                </md-menu>
                <md-menu>
                    <button ng-click="$mdOpenMenu()">
                        資料
                    </button>
                    <md-menu-content>
                        <md-menu-item>
                            <md-button href="rows">資料列</md-button>
                        </md-menu-item>
                        <md-menu-item>
                            <md-button href="import">匯入</md-button>
                        </md-menu-item>
                        <md-menu-divider></md-menu-divider>
                        <md-menu-item type="checkbox" ng-model="file.sheets[0].fillable">可匯入</md-menu-item>
                    </md-menu-content>
                </md-menu>
            </md-menu-bar>
            </div>
        </div>
    </md-toolbar>

    <div>
        <div ng-include="'subs?tool=columns'" ng-if="tool=='columns'"></div>
        <div ng-include="'subs?tool=comment'" ng-if="tool=='comment'"></div>
    </div>

</div>

<script>
app.controller('newTableController', function($scope, $http, $filter) {
    $scope.file = {title: ''};
    $scope.tool = 'columns';
    $scope.limit = 100;
    $scope.action = {};
    $scope.sheetsPage = 1;
    $scope.loading = true;
    $scope.column = {};

    $scope.getFile = function() {
        $scope.$parent.main.loading = true;
        $http({method: 'POST', url: 'get_file', data:{editor: true} })
        .success(function(data, status, headers, config) {
            $scope.file.rules = data.rules;
            $scope.file.title = data.title;
            $scope.file.sheets = data.sheets;
            $scope.file.comment = data.comment;
            if ($scope.file.sheets[0].editable && !$scope.file.sheets[0].tables[0].lock && $scope.file.sheets[0].tables[0].columns == 0) {
                $scope.addColumn($scope.file.sheets[0].tables[0]);
            };
            $scope.$parent.main.loading = false;
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getFile();

    $scope.addSheet = function() {
        var sheet = {name:'', tables: [{columns:[]}]};
        $scope.file.sheets.push(sheet);
        $scope.action.toSelect(sheet);
    };

    $scope.addColumn = function(table) {
        table.columns.push(angular.copy($scope.column));
    };

    $scope.updateSheet = function(editable) {
        $scope.file.sheets[0].editable = editable;
        $scope.saving = true;
        $http({method: 'POST', url: 'update_sheet', data:{sheet: $scope.file.sheets[0]} })
        .success(function(data, status, headers, config) {
            angular.extend($scope.file.sheets[0], data.sheet);
            $scope.saving = false;
        }).error(function(e){
            console.log(e);
        });
    }

    $scope.updateColumn = function(sheet, table, column, rebuild) {console.log($scope.checkColumn(column));
        if ($scope.checkColumn(column)) return;

        if (column.encrypt) {
            column.readonly = true;
        };

        column.updating = true;
        $http({method: 'POST', url: 'update_column', data:{sheet_id: sheet.id, table_id: table.id, column: column, rebuild: rebuild} })
        .success(function(data, status, headers, config) {
            angular.extend(column, data.column);
            column.updating = false;
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.removeColumn = function(sheet, table, column) {
        if (!column.id) {
            table.columns.splice(table.columns.indexOf(column), 1);
            return true;
        };
        $scope.saving = true;
        $http({method: 'POST', url: 'remove_column', data:{sheet_id: sheet.id, table_id: table.id, column: column} })
        .success(function(data, status, headers, config) {
            if (data.deleted) {
                table.columns.splice(table.columns.indexOf(column), 1);
            };
            $scope.saving = false;
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.saveComment = function() {
        $scope.saving = true;
        $http({method: 'POST', url: 'update_comment', data:{comment: $scope.btoa($scope.file.comment)} })
        .success(function(data, status, headers, config) {
            angular.extend($scope.file.comment, data.comment);
            $scope.saving = false;
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.btoa = function(text) {
        return btoa(encodeURIComponent(text));
    };

    $scope.action.toSelect = function(sheet) {
        angular.forEach($filter('filter')($scope.file.sheets, {selected: true}), function(sheet) {
            sheet.selected = false;
        });
        sheet.selected = true;
    };

    $scope.checkColumn = function(column) {
        column.error = !column.name || !column.title || !column.rules || !/^\w{1,50}$/.test(column.name) || !/^[a-z0-9_]+$/.test(column.rules);

        return column.error;
    };

    $scope.isEmpty = function(sheets) {
        var emptyColumns = 0;
        angular.forEach(sheets, function(sheet, index) {
            if( !sheet.title || sheet.title.length === 0 ) {
                emptyColumns += 1 ;
            } else {
                emptyColumns += $filter('filter')(sheet.tables[0].columns, function(column, index) {
                    if( !$scope.notNew(column) )
                        return false;

                    column.error = !/^\w{1,50}$/.test(column.name ) || !column.rules || !/^[a-z0-9_]+$/.test(column.rules);

                    return column.error;

                }).length;
            }
        });
        return emptyColumns > 0;
    };

    $scope.changeTool = function(tool) {
        $scope.tool = tool;
    };

})

.directive('contenteditable', ['$sce', function($sce) {
    return {
        restrict: 'A',
        require: '?ngModel',
        link: function(scope, element, attrs, ngModel) {
            if (!ngModel) return;

            // Specify how UI should be updated
            ngModel.$render = function() {
                element.html($sce.getTrustedHtml(ngModel.$viewValue || ''));
            };

            // Listen for change events to enable binding
            element.on('blur keyup change', function() {
                scope.$evalAsync(read);
            });

            // Write data to the model
            function read() {
                var html = element.html();

                ngModel.$setViewValue(html);
            }
        }
    };
}]);
String.prototype.Blength = function() {
    var arr = this.match(/[^\x00-\xff]/ig);
    return  arr === null ? this.length : this.length + arr.length;
};
</script>
