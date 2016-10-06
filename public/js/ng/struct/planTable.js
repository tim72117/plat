angular.module('ngStruct', []);

angular.module('ngStruct', [])

.directive('planTable', function() {
    return {
        restrict: 'A',
        replace: false,
        transclude: false,
        scope: {
            structClass: '=',
            structClassShow: '=',
            structs: '=',
            idType: '=',
            calculations: '=',
            toggleColumn: '=',
            loadItem: '=',
            callCalculation: '='
        },
        templateUrl: 'templatePlanTable',
        controller: function($scope, $filter) {

            console.log($scope);

            $scope.structInClass = {
                '基本資料':{structs: [{title: '個人資料'}]},
                '就學資訊':{structs: [{title: '就學資訊'}]},
                '修課狀況':{structs: [{title: '完成教育專業課程'}, {title: '完成及認定專門課程'}]},
                '相關活動':{structs: [{title: '卓越師資培育獎學金'}, {title: '五育教材教法設計徵選活動獎'}, {title: '實踐史懷哲精神教育服務計畫'}, {title: '獲選為交換學生至國際友校'}, {title: '卓越儲備教師證明書'}]},
                '教育實習':{structs: [{title: '實際參與實習'}]},
                '師資職前教育':{structs: [{title: '修畢師資職前教育證明書'}]},
                '教檢情形':{structs: [{title: '教師資格檢定'}]},
                '教師專長':{structs: [{title: '教師專長'}]},
                '教師甄試':{structs: [{title: '教甄資料'}]},
                '教師就業狀況':{structs: [{title: '在職教師'},{title: '公立學校代理代課教師'},{title: '儲備教師'},{title: '離退教師'}]},
                '語言檢定':{structs: [{title: '閩南語檢定'},{title: '客語檢定'}]}
            };

            $scope.addNewCalStruct = function(structs) {
                var calculation = {structs: [], results: {}};
                for (var i in structs) {
                    if (structs[i].selected && !structs[i].disabled) {
                        var rows = [];
                        angular.forEach($filter('filter')(structs[i].rows, function(row, index, array) { return row.filter && row.filter!=''; }), function(row, key) {
                            this.push({title: row.title, filter: row.filter.toString()});
                        }, rows);
                        calculation.structs.push({title: $scope.structs[i].title, rows: rows});
                    };
                }
                $scope.calculations.push(calculation);
                $scope.callCalculation();
            };

            $scope.getRowSpan = function(structs) {
                var rowSpan = structs.length - $filter('filter')(structs, {expanded: true}).length;
                for (i in structs) {
                    if (structs[i].expanded) {
                        rowSpan += structs[i].rows.length;
                    };
                }
                return rowSpan;
            };

            $scope.showStruct = function(firstStruct,classTitle) {
                $scope.structClass[firstStruct].expanded = true;
                $scope.structClassShow = true;
                for (var i in $scope.structInClass[classTitle].structs) {
                    for (var j in $scope.structs) {
                        if ($scope.structs[j].title == $scope.structInClass[classTitle].structs[i].title) {
                            $scope.structs[j].classExpanded = true;
                        }
                    }
                }
            };

            $scope.showFilter = function(struct) {
                $scope.structFilterShow = true;
                struct.expanded=true;
            };

            $scope.toggleStruct = function(struct) {
                if (struct.selected) {
                    angular.forEach($filter('filter')(struct.rows, {selected: true}), function(row) {
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
});