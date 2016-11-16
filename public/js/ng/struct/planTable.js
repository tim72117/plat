angular.module('ngStruct', [])

.factory('structService', function($http, $filter, $timeout) {
    var selected = {schools: [], columns: []};
    var status = {levels: [], page: 0, calculations: []};
    var calculate = function() {
        if (status.calculations.length > 0){
            if (JSON.stringify(status.calculations[0].selectedColumns) != JSON.stringify(selected.columns.map( function(column) { return column.id} ))) {
                var selectedColumns = selected.columns.map( function(column) { return column.id} );
                status.calculations = [];
                var calculation = {selectedColumns: [], results: {}};
                calculation.selectedColumns = selectedColumns;
                status.calculations.push(calculation);
                callCalculation();
            }else{
                status.page = 2;
            }
        }else{
            var selectedColumns = selected.columns.map( function(column) { return column.id} );
            var calculation = {selectedColumns: [], results: {}};
            calculation.selectedColumns = selectedColumns;
            status.calculations.push(calculation);
            callCalculation();
        }
        //status.page = 2;
    };
    var callCalculation = function() {
        for (var i in status.calculations) {
            if ($.isEmptyObject(status.calculations[i].results)) {
                addCalculation(status.calculations[i]);
            }
        }
        //$scope.getTitle(); in integrate.php
        status.page = 2;
    };

    var addCalculation = function(calculation) {
        //$http({method: 'POST', url: 'calculate', data:{structs: calculation.structs, columns: $scope.columns, schoolID: $scope.selected.schools}})
        $http({method: 'POST', url: 'calculate', data:{columns: calculation.selectedColumns, schoolID: selected.schools}})
        .success(function(data, status, headers, config) {
            console.log(data);
            calculation.results = data.results;
        }).error(function(e) {
            console.log(e);
        });
    };
    var getLevels = function () {
        var amount = 1;
        var levels = [];
        var rows = [];
        for (i in selected.columns) {
            var items = selected.columns[i].selectedItems;
            amount *= items.length;
            levels[i] = {amount: amount, items: items};
        }
        for (var j = 0; j < amount; j++) {
            rows[j] = {columns: [], parents: []};
            for (i in levels) {
                var step = amount / levels[i].amount;
                var part = parseInt(j / step);
                var item = levels[i].items[part % levels[i].items.length];
                if (part * step == j) {
                    item.rowspan = step;
                    rows[j].columns.push(item);
                } else {
                    rows[j].parents.push(item);
                }
            }
        }
        status.levels = rows;
    }
    var clean = function() {
        selected.columns.forEach(function(column) {
            column.selectedItems = [];
        });
        selected.columns = [];
        status.calculations = [];
        status.levels = [];
    };
    return {
        selected: selected,
        status: status,
        calculate: calculate,
        clean: clean,
        getLevels: getLevels
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
            multiple: '='
        },
        template: `
            <md-input-container>
                <label>{{column.title}}</label>
                <md-select multiple ng-if="multiple && column.type!='slider'" style="margin:0"
                    placeholder="{{column.title}}"
                    ng-model="column.selectedItems"
                    md-on-open="loadItem(table, column)"
                    ng-change="toggleItems(column)">
                    <md-option ng-value="item" ng-repeat="item in column.items">{{item.name}}</md-option>
                </md-select>

            </md-input-container>
        `,
                //         <md-select ng-if="!multiple && column.type!='slider'" style="margin:0"
                //     placeholder="{{column.title}}"
                //     ng-model="selected.columns[column.id].items"
                //     md-on-open="loadItem(table, column)"
                //     ng-change="toggleItems(column)">
                //     <md-option ng-value="item" ng-repeat="item in column.items">{{item.name}}</md-option>
                // </md-select>
        link: function(scope, element) {
            scope.selected = structService.selected;
        },
        controller: function($scope, $http, $filter, $q) {

            $scope.toggleItems = function() {
                var index = structService.selected.columns.indexOf($scope.column);
                if (index == -1) {
                    structService.selected.columns.push($scope.column);
                } else {
                    if ($scope.column.selectedItems.length == 0) {
                        structService.selected.columns.splice(index, 1);
                    }
                }
                structService.getLevels();
            }

            $scope.loadItem = function(table, column) {
                if (column.items && column.itemsLoadBy == structService.selected.schools) {
                    return column.items;
                }

                deferred = $q.defer();
                $http({method: 'POST', url: 'getEachItems', data:{organizations: structService.selected.schools, table_id: table.id, rowTitle: column.title}})
                .success(function(data, status, headers, config) {
                    column.itemsLoadBy = structService.selected.schools;
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

.directive('planTable', function(structService) {
    return {
        restrict: 'A',
        replace: false,
        transclude: false,
        scope: {
            categories: '=',
            tables: '=',
            toggleColumn: '=',
            toggleItems: '='
        },
        templateUrl: 'templatePlanTable',
        controller: function($scope, $http, $filter) {

            $scope.structClassShow = false;
            $scope.structFilterShow = false;
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
                //console.log(structService.selected.columns)
                //console.log(Object.keys(structService.selected.columns))



                /*var structs = $filter('filter')($scope.tables, function(table) {
                    return structService.selected.columns && Object.keys(structService.selected.columns).length > 0;
                });*/
                // for (var i in structs) {
                //     var columns = [];
                //     angular.forEach($filter('filter')(structs[i].columns, function(column, index, array) { return column.filter && column.filter!=''; }), function(column, key) {
                //         this.push({title: column.title, filter: column.filter.toString()});
                //     }, columns);
                //     calculation.structs.push({title: $scope.structs[i].title, columns: columns});
                // }

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

            $scope.goToResultTable = function() {
                structService.status.page = 1;
                //$location.hash('resultTable');
                //$anchorScroll();
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

.directive('resultTable', function(structService) {
    return {
        restrict: 'A',
        replace: false,
        transclude: false,
        scope: {},
        templateUrl: 'templateResultTable',
        link: function(scope) {
            scope.selected = structService.selected;
            scope.status = structService.status;
        },
        controller: function($scope, $filter, $timeout) {

            $scope.dragBefore = [];
            $scope.tableOptions = [
                {id: 'no', title: '不加%'},
                {id:'row', title: '行%'}
                // {key: 'col', title: '列%'}
            ];
            $scope.percentType = 'no';

            $scope.getResults = function(calculation, level) {
                var results = calculation.results;
                for (var i in level.parents) {
                    results = results[level.parents[i].name] || 0;
                }
                for (var i in level.columns) {
                    results = results[level.columns[i].name] || 0;
                }
                return results;
            };

            $scope.getParentResult = function(calculation, level) {
                var results = calculation.results;
                for (var i in level.parents) {
                    results = results[level.parents[i].name] || {};
                };
                return results;
            };

            $scope.getPercent = function(calculation, level, percentType) {
                switch (percentType) {
                    case 'row':
                        var percent = $scope.getTotalPercent($scope.getResults(calculation, level), $scope.getCrossColumnTotal(calculation));
                        break;
                    case 'col':
                        var percent = $scope.getNearestColumnTotal(calculation, level);
                        break;
                    default:
                        var percent = 0;
                        break;
                }
                return percent;
            }
            $scope.getCrossColumnTotal = function(calculation) {
                var crossColumnTotal = 0;
                for (var i in $scope.status.levels) {
                    crossColumnTotal = crossColumnTotal + 1*$scope.getResults(calculation, $scope.status.levels[i]);
                }
                return crossColumnTotal;
            };

            $scope.getTotalPercent = function(value, total) {
                return total == 0 ? 0 : value*100/total;
            };

            $scope.getRowPercent = function(key,level) {
                if (key>0) {
                    var denominator = $scope.getResults($scope.status.calculations[key-1].results,level);
                    var molecular = $scope.getResults($scope.status.calculations[key].results,level);
                    return $scope.getTotalPercent(molecular,denominator)
                }
            };

            $scope.restrictInvolve = function(key) {
                if (key>0) {
                    var denominator = $scope.getStructsTitile (key-1);
                    var molecular = $scope.getStructsTitile (key);
                    return denominator.every($scope.checkInArray,molecular);
                }else{
                    return false;
                }
            };

            $scope.checkInArray = function(value) {
                if (this.indexOf(value)>-1) {
                    return true;
                }else{
                    return false;
                }
            };

            $scope.getNearestColumnTotal = function(calculation, level) {
                var nearestColumnTotal = 0;
                var nearestColumn = $scope.getParentResult(calculation, level);
                for (var i in nearestColumn) {
                    nearestColumnTotal = nearestColumnTotal + 1*nearestColumn[i];
                }
                return nearestColumnTotal;
            };

            $scope.removeCalculation = function(index) {
                $scope.status.calculations.splice(index, 1);
            };

            $scope.getTitle = function() {
                $scope.tableTitle = {};
                var titles = [];
                for (i in $scope.status.calculations) {
                    var title = '';
                    for (j in $scope.status.calculations[i].structs) {
                        title = title+$scope.status.calculations[i].structs[j].title+' ';
                        for (k in $scope.status.calculations[i].structs[j].rows) {
                            title = title+$scope.status.calculations[i].structs[j].rows[k].title+'-'+$scope.status.calculations[i].structs[j].rows[k].filter;
                        }
                    }
                    titles.push(title);
                }
                $scope.tableTitle.title_text = titles;
                $scope.tableTitle.title = '<div>' + titles.join('</div><div>') + '</div>';

            }

            $scope.edit = function() {
                $scope.tableTitle.editing = true;
            };

            $scope.save = function() {
                delete $scope.tableTitle.editing;
            };

            $scope.getStructsTitile = function(key) {
                var title = [];
                for (i in $scope.status.calculations[key].structs) {
                    title.push($scope.status.calculations[key].structs[i].title);
                    for (j in $scope.status.calculations[key].structs[i].rows) {
                            title.push($scope.status.calculations[key].structs[i].rows[j].title);
                            title.push($scope.status.calculations[key].structs[i].rows[j].title+'-'+$scope.status.calculations[key].structs[i].rows[j].filter);
                    }
                }
                return title;
            };

            $scope.exportExcel = function(structs) {
                var tableTitle = '';
                if ($scope.columns == '') {
                    alert('請勾選欲顯示欄位');
                    return false;
                };

                if ($scope.tableTitle !== undefined) {
                    tableTitle = $scope.tableTitle.title_text;
                }
                jQuery.fileDownload('export_excel', {
                    httpMethod: "POST",
                    data:{tableTitle: tableTitle, columns: $scope.columns,levels:$scope.levels,calculations: $scope.status.calculations},
                    failCallback: function (responseHtml, url) { console.log(responseHtml); }
                });
            };

            $scope.moveColumn = function(index, offect) {
                $timeout(function() {
                    var column = $scope.selected.columns[index+offect];
                    $scope.selected.columns.splice(index+offect, 1);
                    $scope.selected.columns.splice(index, 0, column);
                    structService.getLevels();
                }, 300);
            };

            $scope.dragFrom = function(key) {
                $scope.dragBefore = key;
            };

            $scope.dragTo = function(key) {
                var dragAfter = key;
                var moveCalculation = {structs: [], results: {}};
                moveCalculation.results = $scope.status.calculations[$scope.dragBefore]['results'];
                moveCalculation.structs = $scope.status.calculations[$scope.dragBefore]['structs'];

                if ($scope.dragBefore > dragAfter) {
                    $scope.status.calculations.splice($scope.dragBefore,1);
                    $scope.status.calculations.splice(dragAfter+1,0,moveCalculation);
                }
                if ($scope.dragBefore < dragAfter) {
                    $scope.status.calculations.splice(dragAfter+1,0,moveCalculation);
                    $scope.status.calculations.splice($scope.dragBefore,1);
                }
                $scope.dragBefore = [];
            };

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