angular.module('ngStruct', []);

angular.module('ngStruct', [])

.factory('structService', function($http, $timeout) {
    var selected = {schools: [], columns: {}};
    return {
        selected: selected
    }
})

.directive('structItems', function(structService) {
    return {
        restrict: 'E',
        replace: false,
        transclude: false,
        scope: {
            table: '=',
            column: '=',
            multiple: '=',
            toggleItems: '='
        },
        template: `
            <md-input-container>
                <label>{{column.title}}</label>
                <md-select multiple ng-if="multiple && column.type!='slider'" style="margin:0"
                    placeholder="{{column.title}}"
                    ng-model="selected.columns[column.id].items"
                    md-on-open="loadItem(table, column)"
                    ng-change="toggleItems(column);column.selected = true">
                    <md-option ng-value="item" ng-repeat="item in column.items">{{item.name}}</md-option>
                </md-select>
                <md-select ng-if="!multiple && column.type!='slider'" style="margin:0"
                    placeholder="{{column.title}}"
                    ng-model="selected.columns[column.id].items"
                    md-on-open="loadItem(table, column)"
                    ng-change="toggleItems(column);column.selected = true">
                    <md-option ng-value="item" ng-repeat="item in column.items">{{item.name}}</md-option>
                </md-select>
            </md-input-container>
        `,
        link: function(scope, element) {
            scope.selected = structService.selected;
        },
        controller: function($scope, $http, $q) {

            $scope.loadItem = function(table, column) {
                if (column.items) {
                    return column.items;
                }

                deferred = $q.defer();
                $http({method: 'POST', url: 'getEachItems', data:{organizations: structService.selected.schools, table_id: table.id, rowTitle: column.title}})
                .success(function(data, status, headers, config) {
                    table.disabled = data.items.length == 0;
                    column.items = data.items || [];
                    column.disabled = column.items.length == 0;
                    if (column.title == '年齡') column.disabled = true;
                    //$scope.population.columns[1].type = 'slider';
                    deferred.resolve(data.items);
                })
                .error(function(e) {
                    console.log(e);
                });

                return deferred.promise;
            };

        }
    };
})

.directive('planTable', function() {
    return {
        restrict: 'A',
        replace: false,
        transclude: false,
        scope: {
            categories: '=',
            structClassShow: '=',
            tables: '=',
            calculations: '=',
            toggleColumn: '=',
            loadItem: '=',
            callCalculation: '=',
            toggleItems: '='
        },
        templateUrl: 'templatePlanTable',
        controller: function($scope, $filter, structService) {

            $scope.filterItems = {};

            $scope.structInClass = {
                '基本資料':{structs: [{title: '個人資料'}]},
                '就學資訊':{structs: [{title: '就學資訊'}]},
                '修課狀況':{structs: [{title: '完成教育專業課程'}, {title: '完成及認定專門課程'}]},
                '相關活動':{structs: [{title: '卓越師資培育獎學金'}, {title: '五育教材教法設計徵選活動獎'}, {title: '實踐史懷哲精神教育服務計畫'}, {title: '獲選為交換學生至國際友校'}, {title: '卓越儲備教師證明書'}]},
                '教育實習':{structs: [{title: '實際參與實習'}]},
                '教檢情形':{structs: [{title: '教師資格檢定'}]},
                '教師專長':{structs: [{title: '教師專長'}]},
                '教師甄試':{structs: [{title: '教甄資料'}]},
                '教師就業狀況':{structs: [{title: '在職教師'},{title: '公立學校代理代課教師'},{title: '儲備教師'},{title: '離退教師'}]},
                '語言檢定':{structs: [{title: '閩南語檢定'},{title: '客語檢定'}]}
            };

            $scope.addNewCalStruct = function() {
                console.log(structService)
                var calculation = {structs: [], results: {}};
                var structs = $filter('filter')($scope.tables, function(table) {
                    return structService.selected.columns && Object.keys(structService.selected.columns).length > 0;
                });
                console.log(structs);
                // for (var i in structs) {
                //     var columns = [];
                //     angular.forEach($filter('filter')(structs[i].columns, function(column, index, array) { return column.filter && column.filter!=''; }), function(column, key) {
                //         this.push({title: column.title, filter: column.filter.toString()});
                //     }, columns);
                //     calculation.structs.push({title: $scope.structs[i].title, columns: columns});
                // }
                calculation.structs = structs;

                $scope.calculations.push(calculation);
                $scope.callCalculation();
            };

            $scope.getRowSpan = function(structs) {
                var rowSpan = structs.length - $filter('filter')(structs, {expanded: true}).length;
                for (i in structs) {
                    if (structs[i].expanded) {
                        rowSpan += structs[i].columns.length;
                    };
                }
                return rowSpan;
            };

            $scope.showStruct = function(table, category) {
                var classTitle = category.title;
                category.expanded = true;
                $scope.structClassShow = true;
                for (var i in $scope.structInClass[classTitle].structs) {
                    for (var j in $scope.tables) {
                        if ($scope.tables[j].title == $scope.structInClass[classTitle].structs[i].title) {
                            $scope.tables[j].classExpanded = true;
                        }
                    }
                }
            };

            $scope.showFilter = function(struct) {
                $scope.structFilterShow = true;
                struct.expanded = !struct.expanded;
            };

            $scope.toggleStruct = function(struct) {
                if (struct.selected) {
                    angular.forEach($filter('filter')(struct.columns, {selected: true}), function(row) {

                        $scope.toggleColumn(row, struct);
                        row.selected = false;
                    });
                };
                struct.selected = !struct.selected;
            };

            $scope.destroyPopup = function() {
                $('#needHelp').popup('destroy');
                $('[name=needHelp2]').popup('destroy');
                $('[name=needHelp3]').popup('destroy');
                $('[name=needHelp4]').popup('destroy');
                $scope.helpChoosen = false;
            };

        }
    };
})

.directive('resultTable', function() {
    return {
        restrict: 'A',
        replace: false,
        transclude: false,
        scope: {
            calculations: '=',
            levels: '='
        },
        templateUrl: 'templateResultTable',
        controller: function($scope, $filter) {
        }
    };
})

.directive('structExplain', function() {
    return {
        restrict: 'E',
        replace: false,
        transclude: false,
        scope: {},
        templateUrl: 'templateExplain',
        controller: function($scope, $http, $filter) {
            $scope.explans = [];

            $http({method: 'POST', url: 'getExplans', data:{}})
            .success(function(data, status, headers, config) {
                console.log(data);
                $scope.tables = data.tables;
                $scope.categories = data.categories;
            }).error(function(e) {
                console.log(e);
            });

            $scope.getExplanSpan = function(table) {
                var explans = $scope.tables.slice(index, index+categories[table.title].size)
                var explanSpan = table.explans.length - $filter('filter')(table.explans, {expanded: true}).length;
                for (i in explans) {
                    if (explans[i].expanded) {
                        explanSpan += explans[i].explanations.length;
                    };
                }
                return explanSpan;
            };
        }
    };
});