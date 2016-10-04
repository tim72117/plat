<div ng-cloak ng-controller="statusController" style="height:100%" layout="row">

            <md-sidenav class="md-sidenav-left" md-component-id="left" md-is-locked-open="true" md-disable-backdrop flex="20" layout="column">
                <md-toolbar class="md-theme-indigo">
                    <h1 class="md-toolbar-tools" style="font-weight: bold;background: #046D8B;color: white">師培機構分析</h1>
                </md-toolbar>
                <md-content layout-padding style="overflow-y: auto">
                    <div class="ui ribbon label" style="background: #309292;color: white">
                        <h4>選擇分析學校</h4>
                    </div>
                    <div class="item" align="center">
                        <md-input-container>
                                <label>學校</label>
                                <md-select ng-model="selectSchool" aria-label="schools" multiple md-selected-text="selectSchool.length+'所學校'">
                                    <md-button layout-fill value="all" ng-click="selectAllSchool()">全選</md-button>
                                    <md-optgroup >
                                        <md-option ng-value="school" ng-repeat="school in schools">{{school.name}}</md-option>
                                    </md-optgroup>
                                </md-select>
                        </md-input-container>
                    </div>

                    <div class="ui ribbon label" align="center" style="background: #309292;color: white">
                        <h4>選擇分析對象</h4>
                    </div>
                    <div layout="row" layout-sm="column" layout-align="space-around" ng-if="loading">
                      <md-progress-circular md-mode="indeterminate"></md-progress-circular>
                    </div>

                    <div>
                        <div class="ui teal styled fluid accordion">
                            <div class="title">
                                <i class="dropdown icon"></i>
                                {{structs[0].title}}
                            </div>
                            <div class="content">
                                <div layout="row" layout-align="space-between center" ng-repeat="row in structs[0].rows">
                                    <span>{{row.title}}</span>
                                    <md-input-container ng-if="!row.disabled">
                                        <label>{{ row.title }}</label>
                                        <md-select ng-model="row.filter" multiple>
                                            <md-optgroup >
                                                <md-option ng-value="item" ng-repeat="item in row.items">{{item}}</md-option>
                                            </md-optgroup>
                                        </md-select>
                                    </md-input-container>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ui ribbon label" align="center" style="background: #309292;color: white">
                        <h4>選擇分析表單</h4>
                    </div>
                    <div layout="row" layout-sm="column" layout-align="space-around" ng-if="loading">
                      <md-progress-circular md-mode="indeterminate"></md-progress-circular>
                    </div>
                    <div>
                        <div class="ui teal styled fluid accordion">
                            <div class="title" ng-repeat-start ="struct in structs" ng-if="struct.id != 1">
                                <i class="dropdown icon"></i>
                                {{struct.title}}
                            </div>
                            <div class="content" ng-repeat-end ng-if="struct.id != 1">
                                <div layout="row" layout-align="space-between center" ng-repeat="row in struct.rows">
                                    <span>{{row.title}}</span>
                                    <md-input-container ng-if="!row.disabled">
                                        <label>{{ row.title }}</label>
                                        <md-select ng-model="row.filter" multiple>
                                            <md-optgroup >
                                                <md-option ng-value="item" ng-repeat="item in row.items">{{item}}</md-option>
                                            </md-optgroup>
                                        </md-select>
                                    </md-input-container>
                                </div>
                                <button class="compact ui olive small icon button" style="background: #93A42A;color: white" ng-click="getList(struct)">
                                    <i class="play icon"></i> 開始分析
                                </button>
                            </div>
                        </div>
                    </div>
                </md-content>
            </md-sidenav>
            <div class="ui basic segment" ng-class="{loading: loading}" flex="80" layout="column" style="overflow-x: auto">
                <div class="ui tabular menu">
                    <a class="item" ng-class="{active: page == 'explan'}" ng-click="page='explan'">資料欄位說明 </a>
                    <a class="item" ng-class="{active: page == 'resoult'}" ng-click="page='resoult'">分析結果 </a>
                    <div class="right menu">
                        <a class="item" ng-class="{active:tableSize==''}" ng-click="tableSize=''"><i class="table icon"></i>中</a>
                        <a class="item" ng-class="{active:tableSize=='large'}" ng-click="tableSize='large'"><i class="table large icon"></i>大</a>
                    </div>
                </div>

                <div style="display: -webkit-flex;display: flex">

                <div style="-webkit-flex: initial;flex: initial;min-width: 300px" ng-show="page == 'explan'">
                <table class="ui teal collapsing celled structured very compact table" ng-class="{small:tableSize=='small', large:tableSize=='large'}" style="background: #F5F5F5">
                    <thead>
                        <tr>
                            <th>表單名稱</th>
                            <th colspan="2">欄位名稱</th>
                            <th>欄位說明</th>
                        </tr>
                    </thead>
                    <tbody>

                    <tr ng-repeat-start="(index, explan) in explans" class="no-animate">

                         <td rowspan="{{ explan.expanded ? explan.explanations.length : 1 }}">{{ explan.title }}</td>

                        <!-- expand button -->
                        <td class="no-animate" rowspan="{{ explan.expanded ? explan.explanations.length : 1 }}">
                            <div class="compact ui icon mini basic vertical buttons" ng-if="!explan.disabled || explan.expanded">
                                <button class="ui button" ng-if="!explan.expanded" ng-click="explan.expanded=true">
                                    <i class="expand icon"></i>
                                </button>
                                <button class="ui button" ng-if="explan.expanded" ng-click="explan.expanded=false">
                                    <i class="compress icon"></i>
                                </button>
                            </div>
                        </td>

                        <!-- first explanation checkbox -->
                        <td class="no-animate" ng-if="explan.expanded">
                            {{ explan.explanations[0].title }}
                        </td>
                        <td class="no-animate" ng-if="explan.expanded">
                            {{ explan.explanations[0].content }}
                        </td>
                        <!-- all explanations title -->
                        <td class="no-animate" ng-if="!explan.expanded">
                            <div style="width:180px;text-overflow: ellipsis;overflow:hidden !important;white-space: nowrap">
                                <span class="item" ng-repeat="explanation in explan.explanations">{{ explanation.title }}{{ $last ? '' : ',' }}</span>
                            </div>
                        </td>
                        <td class="no-animate" ng-if="!explan.expanded">
                        </td>
                    </tr>

                    <tr ng-repeat-end ng-repeat="explanation in explan.explanations" ng-if="!$first && explan.expanded" class="no-animate">

                        <td>
                            {{ explanation.title }}
                        </td>
                        <td>
                            {{ explanation.content }}
                        </td>
                    </tr>

                </tbody>
                </table>
                </div>

                <div ng-show="page == 'explan'" style="-webkit-flex: 1;flex: 1;margin-left:10px">
                    <button class="compact ui olive icon large button" style="background: #93A42A;color: white">
                    <a href="/files/org_explan.xlsx" style="color: white">
                        <i class="download icon"></i> 下載
                    </a>
                    </button>
                </div>

                <table class="ui teal collapsing celled structured very compact bottom attached table" ng-class="{small:tableSize=='small', large:tableSize=='large'}" ng-show="page == 'resoult'">
                    <a id="resultTable"></a>
                    <thead>
                        <tr>
                            <th ng-if="calculation.results.length>0">
                                <md-input-container ng-if="sumButton">
                                    <label>加總</label>
                                    <md-select ng-model="total">
                                        <md-option ng-repeat="tableOption in tableOptions" ng-value="tableOption" ng-click="setTotal(tableOption)">
                                            {{tableOption}}
                                        </md-option>
                                    </md-select>
                                </md-input-container>
                                <button class="basic ui mini icon button" ng-click="exportExcel(structs)">
                                    <i class="download icon"></i> 下載結果
                                </button>
                            </th>
                            <th colspan="{{calculation.columns.length-1}}" ng-if="calculation.results.length>0">
                                <div ng-hide="tableTitle.editing">
                                    <span ng-dblclick="edit()" ng-bind-html="tableTitle.title"></span>
                                </div>
                                <div ng-show="tableTitle.editing">
                                    <div type="text" contenteditable value="{{tableTitle.title}}" ng-model="tableTitle.title" style="margin:0"></div>
                                    <button class="ui mini button" ng-click="save()">儲存</button>
                                </div>
                            </th>
                        </tr>
                        
                        <tr>
                            <th ng-repeat="column in calculation.columns" style="white-space: nowrap;text-align:left">{{ column }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="result in calculation.results">
                            <td ng-repeat="item in result" style="white-space: nowrap;text-align:left">{{ item }}</td>
                        </tr>
                        <tr ng-if="sumButton == 1 && setSum">
                            <td style="white-space: nowrap;text-align:left" colspan="5">總和</td>
                            <td ng-repeat="(key,item) in calculation.results[0]" style="white-space: nowrap;text-align:left" ng-if="key > 4 && key < 12">{{ getTotal(key) }}</td>
                            <td ng-repeat="(key,item) in calculation.results[0]" style="white-space: nowrap;text-align:left" ng-if="key == 12">加總無意義</td>
                            <td ng-repeat="(key,item) in calculation.results[0]" style="white-space: nowrap;text-align:left" ng-if="key > 12">{{ getTotal(key) }}</td>
                        </tr>
                        <tr ng-if="sumButton == 2 && setSum">
                            <td style="white-space: nowrap;text-align:left" colspan="6">總和</td>
                            <td style="white-space: nowrap;text-align:left">{{ 7 }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
</div>

<script src="/js/jquery-ui/1.11.4/jquery-ui.min.js"></script>
<script src="/css/Semantic-UI/2.1.8/semantic.min.js"></script>
<link rel="stylesheet" href="/js/jquery-ui/1.11.4/jquery-ui.min.css" />

<script src="/js/jquery.fileDownload.js"></script>
<script src="/js/angular-file-upload.min.js"></script>

<script>
app.controller('statusController', function($scope, $http, $filter, $timeout, $location, $anchorScroll, $mdDialog) {
    $scope.page = 'resoult';
    $scope.loading = false;
    $scope.structs = [];
    $scope.tableOptions = ['行加總','不加總'];
    $scope.setSum = false;

    $scope.explans = [];
    $http({method: 'POST', url: 'getOrgExplans', data:{}})
    .success(function(data, status, headers, config) {
       console.log(data)
       $scope.explans = data;
    }).error(function(e){
        console.log(e);
    });

    $http({method: 'POST', url: 'organize_structs', data:{}})
    .success(function(data, status, headers, config) {
       $scope.structs = data;
    }).error(function(e){
        console.log(e);
    });

    $http({method: 'POST', url: 'getSchools', data:{}})
    .success(function(data, status, headers, config) {
        $scope.schools = data;
        $scope.selectSchool = $scope.schools;
        $scope.selectSchoolID = [];
        for (var i in $scope.selectSchool) {
            $scope.selectSchoolID.push($scope.selectSchool[i].id);
        };
        $scope.updateItem($scope.selectSchool);
    }).error(function(e){
        console.log(e);
    });

    $scope.selectAllSchool = function(){
        if (JSON.stringify($scope.selectSchool) === JSON.stringify($scope.schools) ){
            $scope.selectSchool = [];
        }else{
            $scope.selectSchool = $scope.schools;
        }
    };

    $scope.$watchCollection('selectSchool', function(selectSchool) {
        $scope.selectSchoolID = [];
        for (var i in selectSchool) {
            $scope.selectSchoolID.push(selectSchool[i].id);
        };
        $scope.updateItem($scope.selectSchoolID);
    });

    $scope.updateItem = function(selectSchoolID){
        $http({method: 'POST', url: 'getItems', data:{schoolID: selectSchoolID}})
        .success(function(data, status, headers, config) {
            for (var i in $scope.structs) {
                var struct = $scope.structs[i];
                var rows = data.tables[struct.title] || {};
                struct.disabled = Object.keys(rows).length == 0;
                for (var j in struct.rows) {
                    struct.rows[j].items = rows[struct.rows[j].title] || [];
                    struct.rows[j].disabled = struct.rows[j].items == 0;
                };
            };
            $scope.loading = false;
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getList = function(selectedStruct){
        $scope.calculation = {structs: [], results: {}};
        $scope.sumButton = 0;
        var rows = [];
        angular.forEach($filter('filter')($scope.structs[0].rows, function(row, index, array) { return row.filter && row.filter!=''; }), function(row, key) {
            this.push({title: row.title, filter: row.filter.toString()});
        }, rows);
        $scope.calculation.structs.push({title: $scope.structs[0].title, name: $scope.structs[0].name, rows: rows});
        var rows = [];
        angular.forEach($filter('filter')(selectedStruct.rows, function(row, index, array) { return row.filter && row.filter!=''; }), function(row, key) {
            this.push({title: row.title, filter: row.filter.toString()});
        }, rows);
        $scope.calculation.structs.push({title: selectedStruct.title, name: selectedStruct.name, rows: rows});
        if (selectedStruct.title == '招生資料') {
            $scope.sumButton = 1;
        }
        if (selectedStruct.title == '具實習資格人數') {
            $scope.sumButton = 2;
        }
        $http({method: 'POST', url: 'get_organize_detail', data:{structs: $scope.calculation.structs, schoolID: $scope.selectSchoolID}})
        .success(function(data, status, headers, config) {
            //console.log(data)
            $scope.calculation.results = data.results;
            $scope.calculation.columns = data.columns;
            $scope.tableTitle = {};
            var titles = [];
            titles.push($scope.calculation.structs[1]['title']);
            $scope.tableTitle.title_text = titles;
            $scope.tableTitle.title = '<div>' + titles.join('</div><div>') + '</div>';
            console.log($scope.calculation)
        }).error(function(e){
            console.log(e);
        });
    };
    
    $scope.edit = function() {
        $scope.tableTitle.editing = true;
    };

    $scope.save = function() {
        delete $scope.tableTitle.editing;
    };

    $scope.setTotal = function(mode) {
        if (mode == '行加總') {
            $scope.setSum = true;
        }
        if (mode == '不加總') {
            $scope.setSum = false;
        }
    };

    $scope.getTotal = function(key) {
        
    };

    $('.ui.accordion')
    .accordion()
    ;
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
.directive('contenteditable', function(){
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function(scope, element, attributes, ngModel){
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
