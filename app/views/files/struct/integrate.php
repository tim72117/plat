
<md-content ng-cloak ng-controller="statusController" layout="row" style="height:100%">

    <md-sidenav class="md-sidenav-left" md-is-open="mdSidenav.left" layout="column" style="min-width: 500px">
        <md-toolbar class="md-theme-indigo">
            <div class="md-toolbar-tools" style="font-weight: bold;background: #046D8B;color: white" ng-if="academicYearType==1">
                <h2>{{progress[academicYearType]}}</h2>
                <span flex></span>
                <md-button aria-label="預覽表格" ng-click="toggleSidenavRight()">
                    <md-icon md-svg-icon="icon-eye"></md-icon>
                    預覽表格
                </md-button>
            </div>
             
             <div class="md-toolbar-tools" style="font-weight: bold;background: #4A970A;color: white" ng-if="academicYearType==3">
                <h2>{{progress[academicYearType]}}</h2>
                <span flex></span>
                <md-button aria-label="預覽表格" ng-click="toggleSidenavRight()">
                    <md-icon md-svg-icon="icon-eye"></md-icon>
                    預覽表格
                </md-button>
            </div>
            <!--<h1 class="md-toolbar-tools" style="font-weight: bold;background: #CB0051;color: white" ng-if="academicYearType==2">{{progress[academicYearType]}}</h1>
            <h1 class="md-toolbar-tools" style="font-weight: bold;background: #4A970A;color: white" ng-if="academicYearType==3">{{progress[academicYearType]}}</h1>-->
        </md-toolbar>
        <div layout-padding>
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
        </div>
        <div layout-padding>
            <div class="ui ribbon label" style="background: #309292;color: white">
                <h4>選擇學年度</h4>
            </div>
            <div class="item" align="center" ng-if="academicYearType==1">
                <p>占教育部核定名額學年度</p>
                <p>自
                    <md-input-container>
                        <md-select placeholder="學年度"
                            ng-model="academicYearStart"
                            md-on-open="loadItem(structs[idType].title,structs[idType].rows[academicYearType].title)"
                            style="max-width: 100px"
                            ng-change="setAcademicYear(academicYearStart,academicYearEnd,structs[idType],structs[idType].rows[academicYearType])">
                            <md-progress-circular ng-if="!structs[idType].rows[academicYearType].items" md-diameter="20px"></md-progress-circular>
                            <md-option ng-value="{{academicYear}}" ng-repeat="academicYear in structs[idType].rows[academicYearType].items">{{academicYear}}</md-option>
                        </md-select>
                    </md-input-container>
                學年度起</p>
                <p>至
                    <md-input-container>
                        <md-select placeholder="學年度"
                            ng-model="academicYearEnd"
                            md-on-open="loadItem(structs[idType].title,structs[idType].rows[academicYearType].title)"
                            style="max-width: 100px"
                            ng-change="setAcademicYear(academicYearStart,academicYearEnd,structs[idType],structs[idType].rows[academicYearType])">
                            <md-progress-circular ng-if="!structs[idType].rows[academicYearType].items" md-diameter="20px"></md-progress-circular>
                            <md-option ng-value="{{academicYear}}" ng-repeat="academicYear in structs[idType].rows[academicYearType].items">{{academicYear}}</md-option>
                        </md-select>
                    </md-input-container>
                學年度止</p>
            </div>
           
            <div class="item" align="center" ng-if="academicYearType==2">
                <p>參與教育實習學年度</p>
                <p>自
                    <md-input-container>
                        <md-select placeholder="學年度"
                            ng-model="academicYearStart"
                            md-on-open="loadItem(structs[idType].title,structs[idType].rows[academicYearType].title)"
                            style="max-width: 100px"
                            ng-change="setAcademicYear(academicYearStart,academicYearEnd,structs[idType],structs[idType].rows[academicYearType])">
                            <md-progress-circular ng-if="!structs[idType].rows[academicYearType].items" md-diameter="20px"></md-progress-circular>
                            <md-option ng-value="{{academicYear}}" ng-repeat="academicYear in structs[idType].rows[academicYearType].items">{{academicYear}}</md-option>
                        </md-select>
                    </md-input-container>
                學年度起</p>
                <p>至
                    <md-input-container>
                        <md-select placeholder="學年度"
                            ng-model="academicYearEnd"
                            md-on-open="loadItem(structs[idType].title,structs[idType].rows[academicYearType].title)"
                            style="max-width: 100px"
                            ng-change="setAcademicYear(academicYearStart,academicYearEnd,structs[idType],structs[idType].rows[academicYearType])">
                            <md-progress-circular ng-if="!structs[idType].rows[academicYearType].items" md-diameter="20px"></md-progress-circular>
                            <md-option ng-value="{{academicYear}}" ng-repeat="academicYear in structs[idType].rows[academicYearType].items">{{academicYear}}</md-option>
                        </md-select>
                    </md-input-container>
                學年度止</p>
            </div>
        </div>

        <div class="ui ribbon label" align="center" style="background: #309292;color: white">
                <h4> 加入分類變項與篩選條件</h4>
        </div>

        <md-content layout-padding>
            <div layout="row" layout-sm="column" layout-align="space-around" ng-if="loading">
                <md-progress-circular md-mode="indeterminate"></md-progress-circular>
            </div>
            <table class="ui teal collapsing celled table" style="background: #F5F5F5" ng-if="!loading">
                <thead>
                    <tr>
                        <th>選擇欄位</th>
                        <th>選擇分析目標或篩選條件</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td ng-class="{negative: structs[idType].rows[0].disabled, disabled: structs[idType].rows[0].disabled}">
                            <i class="icon close" ng-if="structs[idType].rows[0].disabled"></i>
                            <md-checkbox
                                ng-model="structs[idType].rows[0].selected"
                                aria-label="{{ structs[idType].rows[0].title }}"
                                ng-change="toggleColumn(structs[idType].rows[0], structs[idType])">{{ structs[idType].rows[0].title }}</md-checkbox>
                        </td>

                        <td ng-if="!structs[idType].expanded">
                            <a href="javascript:void(0)" ng-click="structs[idType].expanded=true">
                            <div style="width:180px;text-overflow: ellipsis;overflow:hidden !important;white-space: nowrap">
                                <span id="needHelp" class="item" ng-repeat="row in structs[idType].rows">{{ row.title }}{{ $last ? '' : ',' }}</span>
                            </div>
                            </a>
                        </td>

                        <td ng-class="{disabled: structs[idType].rows[0].disabled}">
                            <md-input-container ng-if="!row.disabled && row.type!='slider'">
                                <md-select placeholder="{{ structs[idType].rows[0].title }}" ng-model="structs[idType].rows[0].filter" md-on-open="loadItem(structs[idType].title,structs[idType].rows[0].title)" multiple ng-change="setFilter(structs[idType])">
                                    <md-progress-circular ng-if="!structs[idType].rows[0].items" md-diameter="20px"></md-progress-circular>
                                    <md-optgroup >
                                        <md-option ng-value="item" ng-repeat="item in structs[idType].rows[0].items">{{item}}</md-option>
                                    </md-optgroup>
                                </md-select>
                            </md-input-container>
                        </td>
                    </tr>

                    <tr ng-repeat-end ng-repeat="row in structs[idType].rows" ng-if="!$first && structs[idType].expanded">

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
                                    md-on-open="loadItem(structs[idType].title,row.title)"
                                    ng-change="setFilter(structs[idType])">
                                    <md-progress-circular ng-if="!row.items" md-diameter="20px"></md-progress-circular>
                                    <md-optgroup >
                                        <md-option ng-value="item" ng-repeat="item in row.items">{{item}}</md-option>
                                    </md-optgroup>
                                </md-select>
                            </md-input-container>
                        </td>

                    </tr>

                </tbody>
            </table>
        </md-content>
    </md-sidenav>

    <div flex layout="column">
        <md-toolbar class="md-theme-indigo">
            <div class="md-toolbar-tools" style="background: #046D8B;color: white">
                <md-button aria-label="快速設定" ng-click="toggleSidenav()">
                    <md-icon md-svg-icon="settings"></md-icon>
                    快速設定
                </md-button>
                <md-button aria-label="資料欄位說明" ng-click="showExplain()">
                    <md-icon md-svg-icon="help-outline"></md-icon>
                    資料欄位說明
                </md-button>
                <md-button aria-label="資料欄位說明" href="/files/explan.xlsx">
                    <md-icon md-svg-icon="file-download"></md-icon>
                    資料欄位說明
                </md-button>
                <md-button aria-label="預覽表格" ng-click="toggleSidenavRight()">
                    <md-icon md-svg-icon="icon-eye"></md-icon>
                    預覽表格
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

        <md-tabs md-dynamic-height md-selected="page">
            <md-tab label="串聯其他表單">
                <md-content class="md-padding" layout="row" style="height:100%">
                <div flex="50">
                    <div plan-table
                        structs="structs"
                        id-type="idType"
                        struct-class="structClass"
                        struct-class-show="structClassShow"
                        calculations="calculations"
                        toggle-column="toggleColumn"
                        load-item="loadItem"
                        call-calculation="callCalculation"></div>
                </div>
                <div flex="50" layout="column" style="height:100%">

                </div>
                </md-content>
            </md-tab>
            <md-tab label="分析結果">
                <md-content class="md-padding">

                </md-content>
            </md-tab>
        </md-tabs>

            <table class="ui teal collapsing celled structured very compact bottom attached table" ng-class="{small:tableSize=='small', large:tableSize=='large'}" ng-show="page == 1">
                <a id="resultTable"></a>
                <thead>
                    <tr>
                        <th colspan="{{columns.length}}" ng-if="calculations.length>0">
                            <md-input-container>
                                <label>加入 %</label>
                                <md-select ng-model="crossPercent">
                                    <md-option ng-repeat="tableOption in tableOptions" ng-value="tableOption" ng-click="setPercent(tableOption)">
                                        {{tableOption}}
                                    </md-option>
                                </md-select>
                            </md-input-container>
                            <button class="basic ui icon button" ng-click="exportExcel(structs)">
                                <i class="download icon"></i> 下載結果
                            </button>
                        </th>
                        <th colspan="{{calculations.length}}" ng-if="calculations.length>0">
                            <div ng-hide="tableTitle.editing">
                                <span ng-dblclick="edit()" ng-bind-html="tableTitle.title"></span>
                            </div>
                            <div ng-show="tableTitle.editing">
                                <div type="text" contenteditable value="{{tableTitle.title}}" ng-model="tableTitle.title" style="margin:0"></div>
                                <button class="ui mini button" ng-click="save()">儲存</button>
                            </div>
                        </th>
                    </tr>
                    <tr class="unselectable" >
                        <th ng-if="columns.length == 0"></th>
                        <th ng-repeat="(order,column) in columns" ng-mousedown="changeColumnFrom(order)" ng-mouseup="changeColumnTo(order)" ng-style="{cursor:changeColumnBefore.length==0 ? 'default' : 'move'}">
                            <label class="compact ui icon mini button">
                                <i class="move icon"></i>
                            </label>
                        </th>
                        <th ng-repeat="(key,calculation) in calculations" class="top aligned" ng-mousedown="dragFrom(key)" ng-mouseup="dragTo(key)" ng-style="{cursor:dragBefore.length==0 ? 'default' : 'move'}">
                            <label class="compact ui icon mini button">
                                <i class="move icon"></i>
                            </label>
                            <button class="compact ui icon mini button" ng-click="removeCalculation(calculation)">
                                <i class="close icon"></i>
                            </button>
                        </th>
                    </tr>
                    <tr>
                        <th ng-if="columns.length == 0"></th>
                        <th ng-repeat="column in columns">{{ column.title }}</th>
                        <th ng-repeat="calculation in calculations" class="top aligned" style="max-width:200px">
                            <div ng-repeat="struct in calculation.structs">
                                {{ struct.title }}
                                <div class="ui label" ng-repeat="row in struct.rows">
                                    {{ row.title }} - {{ row.filter }}
                                </div>
                            </div>
                            單位：人
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="level in levels">
                        <td ng-repeat="parent in level.parents" rowspan="{{ parent.size }}" ng-if="parent.head">{{ parent.title }}</td>
                        <td>{{ level.title }}</td>
                        <td ng-repeat="calculation in calculations" ng-if="colPercent==rowPercent">{{ getResults(calculation.results, level) }}</td>
                        <td ng-repeat="calculation in calculations" ng-if="colPercent">{{ getResults(calculation.results, level) }} ({{getTotalPercent(getResults(calculation.results, level),getNearestColumnTotal(calculation.results, level))| number : 2}} %)</td>
                        <td ng-repeat="(key,calculation) in calculations" ng-if="rowPercent">{{ getResults(calculation.results, level) }} <span ng-if="restrictInvolve(key)">({{getRowPercent(key,level)| number : 2}} %)</span></td>
                    </tr>
                    <tr>
                        <td ng-if="columns.length == 0">總和</td>
                        <td ng-if="columns.length == 0" ng-repeat="calculation in calculations">{{ calculation.results[''] }}</td>
                    </tr>
                    <tr ng-if="calculations.length > 0 && columns.length > 0">
                        <td colspan="{{ columns.length }}">總和</td>
                        <td ng-repeat="calculation in calculations" ng-if="colPercent==rowPercent">{{getCrossColumnTotal(calculation.results, levels)}}</td>
                        <td ng-repeat="calculation in calculations" ng-if="colPercent">{{getCrossColumnTotal(calculation.results, levels)}} </td>
                        <td ng-repeat="calculation in calculations" ng-if="rowPercent">{{ getCrossColumnTotal(calculation.results, levels)}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <md-sidenav class="md-sidenav-right" md-is-open="mdSidenav.right" layout="column" style="min-width: 800px">
        <md-toolbar class="md-theme-indigo">
            <h1 class="md-toolbar-tools">預覽</h1>
        </md-toolbar>
        <table flex="50" class="ui collapsing celled structured very compact bottom attached table" ng-class="{small:tableSize=='small', large:tableSize=='large'}">
            <thead>
                <tr>
                    <th ng-repeat="column in columns">{{ column.title }}</th>
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
            </thead>
            <tbody>
                <tr ng-repeat="level in levels">
                    <td ng-repeat="parent in level.parents" rowspan="{{ parent.size }}" ng-if="parent.head">{{ parent.title }}</td>
                    <td>{{ level.title }}</td>
                    <td ng-repeat="calculation in preCalculations"></td>
                </tr>
                <tr ng-if="preCalculations.length>0">
                    <td colspan="{{ columns.length }}">總和</td>
                    <td ng-repeat="calculation in preCalculations"></td>
                </tr>
            </tbody>
        </table>
    </md-sidenav>
</md-content>

<script src="/js/jquery-ui/1.11.4/jquery-ui.min.js"></script>
<link rel="stylesheet" href="/js/jquery-ui/1.11.4/jquery-ui.min.css" />

<script src="/js/jquery.fileDownload.js"></script>

<script src="/js/ng/struct/planTable.js"></script>

<script>
app.requires.push('ngStruct');
app.controller('statusController', function($scope, $http, $filter, $timeout, $location, $anchorScroll, $mdDialog) {
    $scope.page = 0;
    $scope.helpChoosen = false;
    $scope.colPercent = false;
    $scope.rowPercent = false;
    $scope.tableSize = '';
    $scope.preCalculations = [];
    $scope.dragBefore = [];
    $scope.changeColumnBefore = [];
    $scope.progress = {1:'新進師資生', 2:'實習師資生'};
    $scope.idType = 9;//1:新進師資生;9:實習師資生(第幾個表單)
    $scope.academicYearType = 2;//1:新進師資生(核定名額學年度);2:實習師資生(參與教育實習學年度)(表單中的第幾欄)
    $scope.structClassShow = false;
    $scope.structFilterShow = false;
    $scope.structClass = {
        '個人資料': {title: '基本資料', size: 1},
        '就學資訊': {title: '就學資訊', size: 1},
        '完成教育專業課程': {title: '修課狀況', size: 2},
        '卓越師資培育獎學金': {title: '相關活動', size: 5},
        '實際參與實習': {title: '教育實習', size: 1},
        '教師資格檢定': {title: '教檢情形', size: 1},
        '教師專長': {title: '教師專長', size: 1},
        '教甄資料': {title: '教師甄試', size: 1},
        '在職教師': {title: '教師就業狀況', size: 4},
        '閩南語檢定': {title: '語言檢定', size: 2}
    };

    $scope.tableOptions = ['行%', '列%', '不加%'];

    $scope.loading = true;
    $scope.structs = [];

    $http({method: 'POST', url: 'getPopulation', value:{}})
    .success(function(value, status, headers, config) {
        if (value == 1) {
            $scope.idType = 1;//新進師資生
            $scope.academicYearType = 1;//新進師資生
        } 
        if (value == 2) {
            $scope.idType = 9;//實習師資生
            $scope.academicYearType = 2;//實習師資生(參與教育實習學年度)
        }
    }).error(function(e) {
        console.log(e);
    });

    $scope.$parent.main.loading = true;
    $http({method: 'POST', url: 'getStructs', data:{}})
    .success(function(data, status, headers, config) {
        $scope.structs = data;
        $scope.structs[$scope.idType].expanded = true;
        $scope.loading = false;
        $scope.$parent.main.loading = false;
    }).error(function(e) {
        console.log(e);
    });

    $scope.mdSidenav = {left:false, right: false};
    $scope.toggleSidenav = function() {
        $scope.mdSidenav.left = !$scope.mdSidenav.left;
    };

    $scope.toggleSidenavRight = function() {
        $scope.mdSidenav.right = !$scope.mdSidenav.right;
    };
    $timeout(function() {
        $scope.mdSidenav.left = true;
    }, 1000);

    /*$scope.updateItem = function(selectSchoolID) {
        $http({method: 'POST', url: 'getItems', data:{schoolID: selectSchoolID}})
        .success(function(data, status, headers, config) {
            for (var i in $scope.structs) {
                var struct = $scope.structs[i];
                var rows = data.tables[struct.title] || {};
                struct.disabled = Object.keys(rows).length == 0;
                for (var j in struct.rows) {
                    struct.rows[j].items = rows[struct.rows[j].title] || [];
                    struct.rows[j].disabled = struct.rows[j].items == 0;
                    if (struct.rows[j].title == '年齡') struct.rows[j].disabled = true;
                };
            };
            $scope.structs[0].rows[1].type = 'slider';
            $scope.structs[$scope.idType].expanded = true;
            $scope.loading = false;
        }).error(function(e) {
            console.log(e);
        });
    };*/

    $scope.loadItem = function(structTitle,rowTitle, callback) {
        $http({method: 'POST', url: 'getEachItems', data:{schoolID: $scope.selectSchoolID,structTitle: structTitle,rowTitle: rowTitle}})
        .success(function(data, status, headers, config) {
            var struct = $scope.structs[data.key];
            var rows = data.tables[struct.title] || {};
            struct.disabled = Object.keys(rows).length == 0;
            for (var j in struct.rows) {
                if (struct.rows[j].title == rowTitle) {
                    struct.rows[j].items = rows[struct.rows[j].title] || [];
                    struct.rows[j].disabled = struct.rows[j].items == 0;
                    if (struct.rows[j].title == '年齡') struct.rows[j].disabled = true;
                }
            };
            $scope.structs[0].rows[1].type = 'slider';
            if (callback) {
                callback();
            }
        }).error(function(e) {
            console.log(e);
        });
    };

    $http({method: 'POST', url: 'getSchools', data:{}})
    .success(function(data, status, headers, config) {
        $scope.schools = data;
        $scope.selectSchool = $scope.schools;
        $scope.selectSchoolID = [];
        for (var i in $scope.selectSchool) {
            $scope.selectSchoolID.push($scope.selectSchool[i].id);
        };
    }).error(function(e) {
        console.log(e);
    });

    $scope.$watchCollection('columns', function(columns) {
        $scope.levels = $scope.getLevels(columns, 0);
        $scope.preCalculations.length = 0;
        if (columns.length > 0) {
            $scope.addPreCalculation();
        };
        $scope.colPercent = false;
        $scope.rowPercent = false;
    });

    $scope.$watchCollection('selectSchool', function(selectSchool) {
        $scope.selectSchoolID = [];
        for (var i in selectSchool) {
            $scope.selectSchoolID.push(selectSchool[i].id);
        };
        $scope.columns = [];
        $scope.calculations = [];
        $scope.levels = [];
        for (var i in $scope.structs) {
            $scope.structs[i].selected = false;
            for (var j in $scope.structs[i].rows) {
                $scope.structs[i].rows[j].selected = false;
                $scope.structs[i].rows[j].filter = [];
                $scope.structs[i].rows[j].items = null;
                $scope.structs[i].rows[j].disabled = false;
            }
        }
    });

    $scope.getLevels = function(columns, index) {
        var items = [];
        if (!columns[index] || columns[index].items.length == 0) return items;

        for (var i = 0; i < columns[index].items.length; i++) {
            if (columns.length > index+1) {
                var childrens = $scope.getLevels(columns, index+1);

                for(j in childrens) {
                    if (!childrens[j].parents)
                        childrens[j].parents = [];
                    childrens[j].parents[index] = {title: columns[index].items[i], size: childrens.length, head: j==0}
                }

                items = items.concat(childrens);
            } else {
                items.push({title: columns[index].items[i]});
            }

        };

        return items;
    };

    $scope.getResults = function(result, level) {
        var result = $scope.getParentResult(result, level.parents);
        return result[level.title] || 0;
    };

    $scope.getParentResult = function(result, parents) {
        for (var i in parents) {
            result = result[parents[i].title] || {};
        };
        return result;
    };

    $scope.toggleColumn = function(column, struct) {
        if (column.items == null) {
            $scope.loadItem(struct.title,column.title, function() {
                var index = $scope.columns.indexOf(column);
                if (index == -1) {
                    column.struct = struct.title;
                    $scope.columns.push(column);
                    struct.selected = true;
                    var isadd = true;
                } else {
                    $scope.columns.splice(index, 1);
                    var isadd = false;
                };

                var inStructs = 0;
                if ($scope.calculations.length>0) {
                    for (var i in $scope.calculations) {
                        for (var j in $scope.calculations[i].structs) {
                            if ($scope.calculations[i].structs[j].title == struct.title ) {
                                inStructs = 1;
                            }
                        }
                        if (inStructs==0 && isadd) {
                            $scope.calculations[i].structs.push({title: struct.title, rows: {}});
                        }
                    }
                }
                if ($scope.calculations.length>0) {
                    for (var i in $scope.calculations) {
                        $scope.calculations[i].results = {};
                    }
                }
                $scope.needHelp3();
            });
        } else {
            var index = $scope.columns.indexOf(column);
            if (index == -1) {
                column.struct = struct.title;
                $scope.columns.push(column);
                struct.selected = true;
                var isadd = true;
            } else {
                $scope.columns.splice(index, 1);
                var isadd = false;
            };

            var inStructs = 0;
            if ($scope.calculations.length>0) {
                for (var i in $scope.calculations) {
                    for (var j in $scope.calculations[i].structs) {
                        if ($scope.calculations[i].structs[j].title == struct.title ) {
                            inStructs = 1;
                        }
                    }
                    if (inStructs==0 && isadd) {
                        $scope.calculations[i].structs.push({title: struct.title, rows: {}});
                    }
                }
            }
            if ($scope.calculations.length>0) {
                for (var i in $scope.calculations) {
                    $scope.calculations[i].results = {};
                }
            }
            $scope.needHelp3();
        }
    };


    $scope.columns = [];
    $scope.calculations = [];
    $scope.results = [];

    $scope.addPreCalculation = function() {
        var calculation = {structs: [], results: {}};
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

    $scope.callCalculation = function() {
        for (var i in $scope.calculations) {
            if ($.isEmptyObject($scope.calculations[i].results)) {
                $scope.addCalculation($scope.calculations[i].structs,$scope.calculations[i]);
            }
        }
        $scope.getTitle();
        $scope.gotoResultTable();
    };

    $scope.addCalculation = function(calculateStructs,calculation) {
        $http({method: 'POST', url: 'calculate', data:{structs: calculateStructs, columns: $scope.columns, schoolID: $scope.selectSchoolID}})
        .success(function(data, status, headers, config) {
            calculation.results = data.results;
        }).error(function(e) {
            console.log(e);
        });
    };

    $scope.removeCalculation = function(calculation) {
        var index = $scope.calculations.indexOf(calculation);
        if (index > -1) {
            $scope.calculations.splice(index, 1);
        }
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

    $scope.getCrossColumnTotal = function(calculation,levels) {
        var crossColumnTotal = 0;
        for (var i in levels) {
            crossColumnTotal = crossColumnTotal+ 1*$scope.getResults(calculation, levels[i]);
        }
        return crossColumnTotal;
    };

    $scope.getNearestColumnTotal = function(calculation,level) {
        var nearestColumnTotal = 0;
        var  nearestColumn = $scope.getParentResult(calculation, level.parents);
        for (var i in nearestColumn) {
            nearestColumnTotal = nearestColumnTotal+ 1*nearestColumn[i];
        }
        return nearestColumnTotal;
    };

    $scope.getTotalPercent = function(value,total) {
         return total == 0 ? 0 : value*100/total;
    };

    $scope.gotoResultTable = function() {
        $scope.page = 1;
        //$location.hash('resultTable');
        //$anchorScroll();
    };

    $scope.getTitle = function() {
        $scope.tableTitle = {};
        var titles = [];
        for (i in $scope.calculations) {
            var title = '';
            for (j in $scope.calculations[i].structs) {
                title = title+$scope.calculations[i].structs[j].title+' ';
                for (k in $scope.calculations[i].structs[j].rows) {
                    title = title+$scope.calculations[i].structs[j].rows[k].title+'-'+$scope.calculations[i].structs[j].rows[k].filter;
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

    $scope.setPercent = function(mode) {
        if (mode == '行%')
            $scope.setColPercent();
        if (mode == '列%')
            $scope.setRowPercent();
        if (mode == '不加%')
            $scope.setNoPercent();
    };

    $scope.setRowPercent = function() {
        $scope.colPercent = false;
        $scope.rowPercent = true;
    };

    $scope.setColPercent = function() {
        $scope.colPercent = true;
        $scope.rowPercent = false;
    };

    $scope.setNoPercent = function() {
        $scope.colPercent = false;
        $scope.rowPercent = false;
    };

    $scope.getStructsTitile = function(key) {
        var title = [];
        for (i in $scope.calculations[key].structs) {
            title.push($scope.calculations[key].structs[i].title);
            for (j in $scope.calculations[key].structs[i].rows) {
                    title.push($scope.calculations[key].structs[i].rows[j].title);
                    title.push($scope.calculations[key].structs[i].rows[j].title+'-'+$scope.calculations[key].structs[i].rows[j].filter);
            }
        }
        return title;
    };

    $scope.checkInArray = function(value) {
        if (this.indexOf(value)>-1) {
            return true;
        }else{
            return false;
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

    $scope.getRowPercent = function(key,level) {
        if (key>0) {
            var denominator = $scope.getResults($scope.calculations[key-1].results,level);
            var molecular = $scope.getResults($scope.calculations[key].results,level);
            return $scope.getTotalPercent(molecular,denominator)
        }
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
            data:{tableTitle: tableTitle, columns: $scope.columns,levels:$scope.levels,calculations: $scope.calculations},
            failCallback: function (responseHtml, url) { console.log(responseHtml); }
        });
    };

    $scope.setFilter = function(struct) {
        struct.selected = true;
        $scope.addPreCalculation();
    };

    $scope.dragFrom = function(key) {
        $scope.dragBefore = key;
    };

    $scope.dragTo = function(key) {
        var dragAfter = key;
        var moveCalculation = {structs: [], results: {}};
        moveCalculation.results = $scope.calculations[$scope.dragBefore]['results'];
        moveCalculation.structs = $scope.calculations[$scope.dragBefore]['structs'];

        if ($scope.dragBefore > dragAfter) {
            $scope.calculations.splice($scope.dragBefore,1);
            $scope.calculations.splice(dragAfter+1,0,moveCalculation);
        }
        if ($scope.dragBefore < dragAfter) {
            $scope.calculations.splice(dragAfter+1,0,moveCalculation);
            $scope.calculations.splice($scope.dragBefore,1);
        }
        $scope.dragBefore = [];
    };

    $scope.changeColumnFrom = function(key) {
        $scope.changeColumnBefore = key;
    };
    $scope.changeColumnTo = function(key) {
        var dragAfter = key;
        var moveColumn = {struct: '', title: '',items: {}};
        moveColumn.title = $scope.columns[$scope.changeColumnBefore]['title'];
        moveColumn.struct = $scope.columns[$scope.changeColumnBefore]['struct'];
        moveColumn.items = $scope.columns[$scope.changeColumnBefore]['items'];

        if ($scope.calculations.length>0) {
            for (var i in $scope.calculations) {
                $scope.calculations[i].results = {};
            }
        }

        if ($scope.changeColumnBefore > dragAfter) {
            $scope.columns.splice($scope.changeColumnBefore,1);
            $scope.columns.splice(dragAfter+1,0,moveColumn);
            $scope.callCalculation();
        }
        if ($scope.changeColumnBefore < dragAfter) {
            $scope.columns.splice(dragAfter+1,0,moveColumn);
            $scope.columns.splice($scope.changeColumnBefore,1);
            $scope.callCalculation();
        }
        $scope.changeColumnBefore = [];
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

    $scope.selectAllSchool = function() {
        if (JSON.stringify($scope.selectSchool) === JSON.stringify($scope.schools) ) {
            $scope.selectSchool = [];
        }else{
            $scope.selectSchool = $scope.schools;
        }
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

    function explainController(scope) {
        scope.explans = [];
        scope.structClass = $scope.structClass;

        $http({method: 'POST', url: 'getExplans', data:{}})
        .success(function(data, status, headers, config) {
            console.log(data);
            scope.explans = data;
        }).error(function(e) {
            console.log(e);
        });

        scope.getExplanSpan = function(explans) {
            var explanSpan = explans.length - $filter('filter')(explans, {expanded: true}).length;
            for (i in explans) {
                if (explans[i].expanded) {
                    explanSpan += explans[i].explanations.length;
                };
            }
            return explanSpan;
        };
    }

    $scope.showExplain = function(ev) {
        $mdDialog.show({
            controller: explainController,
            templateUrl: 'templateExplain',
            clickOutsideToClose: true
        })
        .then(function(answer) {
            $scope.status = 'You said the information was "' + answer + '".';
        }, function() {
            $scope.status = 'You cancelled the dialog.';
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
