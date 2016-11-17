
<md-content ng-cloak ng-controller="statusController" layout="row" style="height:100%">

    <md-sidenav class="md-sidenav-left" md-is-open="mdSidenav.left" layout="column" style="min-width: 500px">
        <md-toolbar md-scroll-shrink>
            <div class="md-toolbar-tools" style="color: white" md-colors="{background: population.color}">
                <h2>{{population.title}}</h2>
                <span flex></span>
                <md-button aria-label="預覽表格" ng-click="toggleSidenavRight()">
                    <md-icon md-svg-icon="icon-eye"></md-icon>
                    預覽表格
                </md-button>
                <md-button aria-label="開始計算" md-colors="{background: 'accent'}" ng-click="calculate()">
                    <md-icon md-svg-icon="icon-eye"></md-icon>
                    開始計算
                </md-button>
            </div>
        </md-toolbar>
        <div style="overflow-y: auto">

            <!--<div layout-padding>
                <div class="ui ribbon label" style="background: #309292;color: white">
                    <h4>{{population.yearTitle}}</h4>
                </div>
                <div class="item" align="center">
                    <p>自
                        <md-input-container>
                            <md-select placeholder="學年度"
                                ng-model="academicYearStart"
                                md-on-open="loadItem(mainTable, mainTable.columns[population.yearColumnIndex])"
                                style="max-width: 100px"
                                ng-change="setAcademicYear(academicYearStart, academicYearEnd, mainTable, mainTable.columns[population.yearColumnIndex])">
                                <md-option ng-value="item" ng-repeat="item in mainTable.columns[population.yearColumnIndex].items">{{item.name}}</md-option>
                            </md-select>
                        </md-input-container>
                    學年度起</p>
                    <p>至
                        <md-input-container>
                            <md-select placeholder="學年度"
                                ng-model="academicYearEnd"
                                md-on-open="loadItem(mainTable, mainTable.columns[population.yearColumnIndex])"
                                style="max-width: 100px"
                                ng-change="setAcademicYear(academicYearStart, academicYearEnd, mainTable, mainTable.columns[population.yearColumnIndex])">
                                <md-option ng-value="item" ng-repeat="item in mainTable.columns[population.yearColumnIndex].items">{{item.name}}</md-option>
                            </md-select>
                        </md-input-container>
                    學年度止</p>
                </div>
            </div>-->

            <!--<div layout-padding>
                <div class="ui ribbon label" align="center" style="background: #309292;color: white"><h4> 加入篩選條件</h4></div>
                <div layout="row" layout-sm="column" layout-align="space-around" ng-if="loading">
                    <md-progress-circular md-mode="indeterminate"></md-progress-circular>
                </div>
                <table class="ui teal collapsing celled table" style="background: #F5F5F5" ng-if="!loading">
                    <thead>
                        <tr><th>選擇篩選條件</th></tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="row in structs[idType].rows" ng-if="structs[idType].expanded">
                            <td ng-class="{negative: row.disabled, disabled: row.disabled}">
                                <i class="icon close" ng-if="row.disabled"></i>
                                <md-checkbox ng-model="row.selected"
                                    aria-label="{{ structs[idType].rows[0].title }}"
                                    ng-change="toggleColumn(row, structs[idType])">{{ row.title }}</md-checkbox>
                            </td>
                            <td ng-class="{disabled: row.disabled}">
                                <div ng-if="row.type=='slider'">
                                    {{ row.filter[0] }}年至{{ row.filter[1] }}
                                    <div ng-slider ng-model="row.filter" items="row.items"></div>
                                </div>
                                <md-input-container ng-if="!row.disabled && row.type!='slider'">
                                    <md-select multiple
                                        placeholder="{{ row.title }}"
                                        ng-model="row.filter"
                                        md-selected-text="item.name"
                                        md-on-open="loadItem(structs[idType], row)"
                                        ng-change="setFilter(structs[idType])">
                                        <md-optgroup >
                                            <md-option ng-value="item" ng-repeat="item in row.items">{{item.name}}</md-option>
                                        </md-optgroup>
                                    </md-select>
                                </md-input-container>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>-->
        </div>
    </md-sidenav>

    <div flex layout="column">
        <md-toolbar>
            <div class="md-toolbar-tools" style="color: white" md-colors="{background: population.color}">
                <!--<md-button aria-label="快速設定" ng-click="toggleSidenav()">
                    <md-icon md-svg-icon="settings"></md-icon>
                    快速設定
                </md-button>-->
                <md-button aria-label="欄位說明" ng-click="showExplain()">
                    <md-icon md-svg-icon="help-outline"></md-icon>
                    欄位說明
                </md-button>
                <md-button aria-label="下載欄位說明" href="/files/explan.xlsx">
                    <md-icon md-svg-icon="file-download"></md-icon>
                    下載欄位說明
                </md-button>
                <md-button aria-label="預覽表格" ng-click="toggleSidenavRight()">
                    <md-icon md-svg-icon="icon-eye"></md-icon>
                    預覽表格
                </md-button>
                <md-button aria-label="開始計算" class="md-raised md-warn" ng-click="calculate()">
                    <md-icon md-svg-icon="icon-eye"></md-icon>
                    開始計算
                </md-button>
                <span flex></span>
                <!--<md-button aria-label="需要幫忙" ng-click="showTabDialog($event)"><md-icon md-svg-icon="help"></md-icon>需要幫忙</md-button>-->
                <md-menu>
                    <md-button aria-label="表單大小" ng-click="$mdOpenMenu($event)">
                        表單大小
                    </md-button>
                    <md-menu-content width="4">
                        <md-menu-item><md-button aria-label="中" ng-click="tableSize=''"><i class="table icon"></i>中</md-button></md-menu-item>
                        <md-menu-item><md-button aria-label="大" ng-click="tableSize='large'"><i class="table large icon"></i>大</md-button></md-menu-item>
                    </md-menu-content>
                </md-menu>
            </div>
        </md-toolbar>
        <div class="ui basic segment" ng-class="{loading: loading}" style="overflow-y: auto">

        <md-tabs md-dynamic-height md-selected="status.page">
            <md-tab label="快速設定">
                <md-content class="md-padding" flex-sm="100" flex-md="60" flex-lg="50" flex-xl="30" layout="column">
                    <md-input-container>
                        <label>選擇分析學校</label>
                        <md-select ng-model="selected.schools" aria-label="選擇分析學校" multiple md-selected-text="selected.schools.length+'所學校'" ng-change="changeSchools()">
                            <md-button layout-fill value="all" ng-click="selectAllSchool()">全選</md-button>
                            <md-optgroup>
                                <md-option ng-value="organization" ng-repeat="organization in organizations">{{organization.now.name}}</md-option>
                            </md-optgroup>
                        </md-select>
                    </md-input-container>
                    <table class="ui teal celled table" ng-if="!loading">
                        <thead>
                            <tr><th>選擇分類變項</th></tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="column in mainTable.columns" ng-if="mainTable.expanded">
                                <td ng-class="{negative: column.disabled}">
                                    <div ng-if="column.type=='slider'">
                                        {{ column.filter[0] }}年至{{ column.filter[1] }}
                                        <div ng-slider ng-model="column.filter" items="column.items"></div>
                                    </div>
                                    <struct-items table="mainTable" column="column" multiple="true"></struct-items>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </md-content>
            </md-tab>
            <md-tab label="串聯其他表單">
                <md-content class="md-padding" flex-sm="100" flex-md="80" flex-lg="70" flex-xl="50" layout="column">
                    <div plan-table tables="tables" categories="categories"></div>
                </md-content>
            </md-tab>
            <md-tab label="分析結果" md-on-select="calculate()">
                <md-content class="md-padding" flex-sm="100" flex-md="80" flex-lg="70" flex-xl="50" layout="column">
                    <div result-table calculations="calculations" levels="levels"></div>
                </md-content>
            </md-tab>
        </md-tabs>
        </div>
    </div>

    <md-sidenav class="md-sidenav-right" md-is-open="status.preview" md-disable-backdrop layout="column" style="min-width:{{getPreviewWidth()}}px">
        <md-toolbar>
            <div class="md-toolbar-tools">
                <h2>預覽</h2>
                <div flex></div>
                <md-button aria-label="關閉" ng-click="status.preview = false">關閉</md-button>
            </div>
        </md-toolbar>
        <md-content flex layout-padding>
            <table class="ui collapsing celled structured table" ng-class="{small:tableSize=='small', large:tableSize=='large'}">
                <thead>
                    <tr>
                        <th ng-repeat="column in selected.columns">{{ column.title }}</th>
                        <th ng-repeat="calculation in preCalculations" class="top aligned" style="max-width:200px">
                            <div ng-repeat="struct in calculation.structs">
                                {{ struct.title }}
                                <div class="ui label" ng-repeat="row in struct.rows">
                                    {{ row.title }} - {{ row.filter }}
                                </div>
                            </div>
                            單位：人
                        </th>
                    </tr>
                    <tr ng-if="selected.columns.length > 1">
                        <th ng-repeat="column in selected.columns">
                            <div layout="row">
                                <md-button class="md-icon-button" aria-label="左移" ng-click="moveColumn($index, -1)" ng-if="!$first">
                                    <md-icon md-svg-icon="keyboard-arrow-left"></md-icon>
                                </md-button>
                                <span flex></span>
                                <md-button class="md-icon-button" aria-label="右移" ng-click="moveColumn($index, 1)" ng-if="!$last">
                                    <md-icon md-svg-icon="keyboard-arrow-right"></md-icon>
                                </md-button>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="level in status.levels">
                        <td ng-repeat="column in level.columns" rowspan="{{ column.rowspan }}">{{ column.name }}</td>
                        <td ng-repeat="calculation in preCalculations"></td>
                    </tr>
                    <tr ng-if="preCalculations.length>0">
                        <td colspan="{{ columns.length }}">總和</td>
                        <td ng-repeat="calculation in preCalculations"></td>
                    </tr>
                </tbody>
            </table>
        </md-content>
    </md-sidenav>
</md-content>

<script src="/js/jquery.fileDownload.js"></script>
<script src="/js/ng/struct/planTable.js"></script>

<script>
app.requires.push('ngStruct');
app.controller('statusController', function($scope, $http, $filter, $timeout, $location, $anchorScroll, $mdDialog, $q, structService, $mdMedia) {
    $scope.helpChoosen = false;
    $scope.tableSize = '';
    $scope.preCalculations = [];
    $scope.loading = true;
    $scope.selected = structService.selected;
    $scope.status = structService.status;
    $scope.$parent.main.loading = true;
    $scope.mdSidenav = {left: false, right: false};
    $scope.population = {color: 'blue'};
    $scope.calculate = structService.calculate;

    $http({method: 'POST', url: 'getTables', data:{}})
    .success(function(data, status, headers, config) {
        $scope.tables = data.tables;
        $scope.categories = data.categories;
        $scope.mainTable = $filter('filter')($scope.tables, {id: data.population.id+''}, true)[0];
        $scope.population = data.population;
        $scope.mainTable.hide = true;
        $scope.mainTable.expanded = true;
        $scope.loading = false;
        $scope.$parent.main.loading = false;
    }).error(function(e) {
        console.log(e);
    });

    $scope.toggleSidenav = function() {
        $scope.mdSidenav.left = !$scope.mdSidenav.left;
    };

    $scope.toggleSidenavRight = function() {
        structService.status.preview = !structService.status.preview;
    };

    $http({method: 'POST', url: 'getSchools', data:{}})
    .success(function(data, status, headers, config) {
        $scope.organizations = data.organizations;
        structService.selected.schools = $scope.organizations;
    }).error(function(e) {
        console.log(e);
    });

    $scope.changeSchools = function() {
        structService.clean();
    };

    $scope.selectAllSchool = function() {
        if (JSON.stringify($scope.selected.schools) === JSON.stringify($scope.organizations) ) {
            $scope.selected.schools = [];
        }else{
            $scope.selected.schools = $scope.organizations;
        }
    };

    $scope.addPreCalculation = function() {
        console.log(123)
        var calculation = {structs: [], results: {}};
        //console.log($scope.structs)
        for (i in $scope.structs) {
            if ($scope.structs[i].selected && !$scope.structs[i].disabled) {
                var rows = [];
                angular.forEach($filter('filter')($scope.structs[i].rows, function(row, index, array) { return row.filter && row.filter!=''; }), function(row, key) {
                    this.push({title: row.title, filter: row.filter.toString()});
                }, rows);
                calculation.structs.push({title: $scope.structs[i].title, rows: rows});
            };
        }
        $scope.preCalculations=[];
        $scope.preCalculations.push(calculation);
    };

    $scope.needHelp = function() {
        $scope.helpChoosen = true;
        if (event.type=='click') {
            $('#needHelp').popup({
                target:   $(needHelp),
                //position: 'right center',
                on:       'click',
                html:  '<h2 class="ui  header">點擊藍色字體可展開欄位，勾選分析面向。</h2>' +
                       '<i class="info icon"></i>紅色叉號表示表示尚無資料'

            });
            $('#needHelp').popup('show');
        } else {
            $('#needHelp').popup('destroy');
        }
    };

    $scope.needHelp2 = function() {
        if ($scope.helpChoosen) {
            $('[name=needHelp2]').popup({
                target:    $('[name=needHelp2]'),
                position: 'left center',
                on:       'click',
                html:  '<h2 class="ui  header">勾選欄位右方的勾勾可選擇分析對象。</h2>' +
                       '<i class="info icon"></i>右方無勾號表示表示尚無資料'
            });
            $('[name=needHelp2]').popup('show');
        }
    };

    $scope.needHelp3 = function() {
        if ($scope.helpChoosen) {
            $('[name=needHelp3]').popup({
                target:    $('[name=needHelp3]'),
                position: 'right center',
                on:       'click',
                html:  '<h2 class="ui  header">展開左方選擇欄位後，透過下拉式選單選擇篩選條件。</h2>' +
                       '<i class="info icon"></i>若不選擇則表示全選'
            });
            $('[name=needHelp3]').popup('show');
        }
    };

    $scope.needHelp4 = function() {
        if ($scope.helpChoosen)  {
            $('[name=needHelp4]').popup({
                target:    $('[name=needHelp4]'),
                //position: 'right center',
                on:       'click',
                html:  '<h2 class="ui  header">點擊計算機圖示產生分析表格。</h2>'
            });
            $('[name=needHelp4]').popup('show');
        }
    };

    $scope.setFilter = function(struct) {
        struct.selected = true;
        $scope.addPreCalculation();
    };

    $scope.setAcademicYear = function(startYear,endYear,struct,column) {
        column.filter = [];
        if (typeof(startYear)!='undefined') {
            if (typeof(endYear)!='undefined') {
                if (endYear*1 >= startYear*1) {
                    for (var i = startYear*1; i < endYear*1+1; i++) {
                        column.filter.push(i);
                    }
                    column.selected = true;
                    var index = $scope.columns.indexOf(column);
                    if (index == -1) {
                        $scope.toggleColumn(column,struct);
                    }
                    $scope.setFilter(struct);
                }
            }
        }
    };

    $scope.getPreviewWidth = function() {
        var width = 0;
        if ($mdMedia('sm')) {
            width = 100;
        } else if ($mdMedia('md')) {
            width = 400;
        } else if ($mdMedia('lg')) {
            width = 700;
        } else if ($mdMedia('gt-lg')) {
            width = 1000;
        }
        return width;
    };

    $scope.moveColumn = function(index, offect) {
        $timeout(function() {
            var column = $scope.selected.columns[index+offect];
            $scope.selected.columns.splice(index+offect, 1);
            $scope.selected.columns.splice(index, 0, column);
            structService.getLevels();
        }, 300);
    };

    $(".unselectable").css( {
       'mozUserSelect': 'mozNone',
       'khtmlUserSelect': 'none',
       'webkitUserSelect': 'none',
       'msUserSelect': 'none',
       'userSelect': 'none'
    });

    $scope.showTabDialog = function(ev) {
        $mdDialog.show({
          controller: DialogController,
          templateUrl: 'templateHelp',
          parent: angular.element(document.body),
          targetEvent: ev,
          clickOutsideToClose:true
        })
    };

    $scope.showExplain = function(ev) {
        $mdDialog.show({
            template: '<struct-explain></struct-explain>',
            clickOutsideToClose: true
        })
        .then(function(answer) {
            $scope.explainStatus = 'You said the information was "' + answer + '".';
        }, function() {
            $scope.explainStatus = 'You cancelled the dialog.';
        });
    };

    function DialogController($scope, $mdDialog) {
        $scope.hide = function() {
            $mdDialog.hide();
        };

        $scope.cancel = function() {
            $mdDialog.cancel();
        };

        $scope.answer = function(answer) {
            $mdDialog.hide(answer);
        };
    }
})

.directive('ngSlider', function($timeout, $window) {
    return {
        restrict: 'A',
        scope: {
            items: '='
        },
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            element.slider({
                range: true,
                min: 1911,
                max: 2015,
                values: scope.items*1,
                step: 1,
                slide: function(event, ui) {
                    ngModelCtrl.$setViewValue(ui.values);
                },
                create: function(event, ui) {
                    ngModelCtrl.$setViewValue(ui.values);
                }
            });
        }
    };

})

.directive('contenteditable', function() {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function(scope, element, attributes, ngModel) {
            ngModel.$render = function() {
                element.html(ngModel.$viewValue || '');
            };
            element.bind('blur keyup change', function() {
                scope.$apply(function() {
                   ngModel.$setViewValue(element.html());
                });
            });
        }
    };
});
</script>
