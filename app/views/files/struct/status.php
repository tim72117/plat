<div ng-cloak ng-controller="statusController">

    <div class="ui basic segment" ng-class="{loading: loading}">

        <div class="ui tabular menu">
            <a class="item" ng-class="{active: page == 'editor'}" ng-click="page='editor'">選擇分析項目 </a>
            <a class="item" ng-class="{active: page == 'resoult'}" ng-click="page='resoult'">分析結果 </a>
            <div class="right menu">
                <a class="item" ng-class="{active:tableSize==''}" ng-click="tableSize=''"><i class="table icon"></i>中</a>
                <a class="item" ng-class="{active:tableSize=='large'}" ng-click="tableSize='large'"><i class="table large icon"></i>大</a>
            </div>
        </div>

        <div style="display: -webkit-flex;display: flex">
        <div style="-webkit-flex: initial;flex: initial;min-width: 300px">
        <table class="ui collapsing celled structured very compact table" ng-class="{small:tableSize=='small', large:tableSize=='large'}" ng-show="page == 'editor'">
            <thead>
                <tr>
                    <th>師資培育與就業歷程</th>
                    <th>資料類別</th>
                    <th colspan="2">選擇欄位</th>
                    <th>選擇分析目標或篩選條件</th>
                    <th class="center aligned"><div class="ui yellow mini button" ng-click="needHelp($event)" ><i class="icon help outline"></i>需要幫忙</div></th>
                </tr>
            </thead>
            <tbody>

                <tr ng-repeat-start="(index, struct) in structs">

                    <td rowspan="{{ getRowSpan(structs.slice(index, index+structClass[struct.title].size)) }}" ng-if="structClass[struct.title]">{{ structClass[struct.title].title }}</td>

                    <td rowspan="{{ struct.expanded ? struct.rows.length : 1 }}">{{ struct.title }}</td>

                    <!-- expand button -->
                    <td rowspan="{{ struct.expanded ? struct.rows.length : 1 }}">
                        <div class="compact ui icon mini basic vertical buttons" ng-if="!struct.disabled || struct.expanded">
                            <button class="ui button" ng-if="!struct.disabled" ng-click="toggleStruct(struct)" >
                                <i name="needHelp2" class="checkmark icon" ng-class="{green: !struct.disabled && struct.selected}"></i>
                            </button>
                            <button class="ui button" ng-if="struct.expanded" ng-click="struct.expanded=false">
                                <i class="compress icon"></i>
                            </button>
                        </div>
                    </td>

                    <!-- first row checkbox -->
                    <td ng-if="struct.expanded" ng-class="{negative: struct.rows[0].disabled, disabled: struct.rows[0].disabled}">
                        <i class="icon close" ng-if="struct.rows[0].disabled"></i>
                        <div class="ui checkbox" >
                            <input id="{{ ::$id }}" type="checkbox" ng-model="struct.rows[0].selected" ng-change="toggleColumn(struct.rows[0], struct)" ng-click="needHelp2()" class="hidden"  name="needHelp5">
                            <label for="{{ ::$id }}" >{{ struct.rows[0].title }}</label>
                        </div>
                    </td>

                    <!-- all rows title -->
                    <td ng-if="!struct.expanded">
                        <a href="javascript:void(0)" ng-click="struct.expanded=true">
                        <div style="width:180px;text-overflow: ellipsis;overflow:hidden !important;white-space: nowrap">
                            <span id="needHelp" class="item" ng-repeat="row in struct.rows">{{ row.title }}{{ $last ? '' : ',' }}</span>
                        </div>
                        </a>
                    </td>

                    <!-- first row filter -->
                    <td ng-class="{disabled: struct.rows[0].disabled}">
                        <div ng-muti-dropdown-menu ng-model="struct.rows[0].filter" ng-change="setFilter(struct)" class="ui multiple search selection dropdown" ng-if="!struct.rows[0].disabled && struct.expanded">
                            <input type="hidden">
                            <i name="needHelp3" class="dropdown icon"></i>
                            <div class="default text">{{ struct.rows[0].title }}</div>
                            <div class="menu">
                                <div class="item" ng-repeat="item in struct.rows[0].items" data-value="{{ item }}" ng-click="needHelp4()">{{ item }}</div>
                            </div>
                        </div>
                    </td>

                    <td rowspan="{{ getRowSpan(structs) }}" ng-if="$first">
                        <button class="compact ui icon large basic button" ng-click="addNewCalStruct(structs);destroyPopup()">
                            <i name="needHelp4" class="play icon"></i> 開始計算
                        </button>
                    </td>

                </tr>

                <tr ng-repeat-end ng-repeat="row in struct.rows" ng-if="!$first && struct.expanded">

                    <!-- rows checkbox -->
                    <td ng-class="{negative: row.disabled, disabled: row.disabled}">
                        <i class="icon close" ng-if="row.disabled"></i>
                        <div class="ui checkbox">
                            <input id="{{ ::$id }}" type="checkbox" ng-model="row.selected" ng-change="toggleColumn(row, struct)" class="hidden" ng-click="needHelp2()">
                            <label for="{{ ::$id }}">{{ row.title }}</label>
                        </div>
                    </td>

                    <!-- rows filter -->
                    <td ng-class="{disabled: row.disabled}">
                        <div ng-muti-dropdown-menu ng-model="row.filter" ng-change="setFilter(struct)" class="ui multiple search selection dropdown" ng-if="!row.disabled && row.type!='slider'">
                            <input type="hidden">
                            <i class="dropdown icon"></i>
                            <div class="default text">{{ row.title }}</div>
                            <div class="menu">
                                <div class="item" ng-repeat="item in row.items" data-value="{{ item }}" ng-click="needHelp4()">{{ item }}</div>
                            </div>
                        </div>
                        <div ng-if="row.type=='slider'">
                            {{ row.filter[0] }}年至{{ row.filter[1] }}
                            <div ng-slider ng-model="row.filter" items="row.items"></div>
                        </div>
                    </td>

                </tr>

            </tbody>
        </table>
        </div>

        <div style="-webkit-flex: 1;flex: 1;margin-left:10px">
        <table class="ui collapsing celled structured very compact bottom attached table" ng-class="{small:tableSize=='small', large:tableSize=='large'}" ng-show="page == 'editor'">
            <thead>
                <tr>
                    <th colspan="{{ preCalculations.length+columns.length }}">預覽</th>
                </tr>
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
        </div>
        </div>

        <table class="ui collapsing celled structured very compact bottom attached table" ng-class="{small:tableSize=='small', large:tableSize=='large'}" ng-show="page == 'resoult'">
            <a id="resultTable"></a>
            <thead>
                <tr>
                    <th colspan="{{columns.length}}" ng-if="calculations.length>0">
                        <div ng-semantic-dropdown-menu ng-model="crossPercent" class="ui top left pointing labeled floating dropdown icon mini button">
                                <span class="text">加入 %</span>
                                <input type="hidden">
                                <i class="add icon"></i>
                                <div class="menu">
                                    <div class="item" data-value="col" ng-click="setColPercent()">行 %</div>
                                    <div class="item" data-value="row" ng-click="setRowPercent()">列 %</div>
                                    <div class="item" data-value="noPercent" ng-click="setNoPercent()">不加 %</div>
                                </div>
                        </div>
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

<script src="/js/jquery-ui/1.11.4/jquery-ui.min.js"></script>
<script src="/css/Semantic-UI/2.1.8/semantic.min.js"></script>
<link rel="stylesheet" href="/js/jquery-ui/1.11.4/jquery-ui.min.css" />

<script src="/js/jquery.fileDownload.js"></script>
<script src="/js/angular-file-upload.min.js"></script>

<script>
app.controller('statusController', function($scope, $http, $filter, $timeout, $location, $anchorScroll) {
    $scope.page = 'editor';
    $scope.helpChoosen = false;
    $scope.colPercent = false;
    $scope.rowPercent = false;
    //$scope.restrictInvolve = false;
    //$scope.needHelp5Exist = 0;
    $scope.tableSize = '';
    $scope.preCalculations = [];
    $scope.dragBefore = [];
    $scope.changeColumnBefore = [];
    $scope.structClass = {
        '個人資料': {title: '基本資料', size: 1},
        '實際修課': {title: '就學資訊', size: 3},
        '完成教育專業課程': {title: '修課狀況', size: 2},
        '卓越師資培育獎學金': {title: '相關活動', size: 2},
        '實際參與實習': {title: '教育實習', size: 1},
        '修畢師資職前教育證明': {title: '師資職前教育', size: 1},
        '畢業離校師資生教育實習情況': {title: '畢業狀況', size: 1},
        '教師資格檢定': {title: '教檢情形', size: 1},
        '新制教師證書': {title: '教師證書', size: 2},
        '師資身分': {title: '師資身分', size: 1},
        '教甄資料': {title: '教師甄試', size: 1},
        '在職教師': {title: '教師就業狀況', size: 4},
        '教師在職進修資料': {title: '研習', size: 1},
        '閩南語檢定': {title: '語言檢定', size: 2}
    };
    $scope.structs = [
        //基本資料
        {title: '個人資料', rows: [{title: '性別'}, {title: '出生年', type: 'slider'}]},
        //就學資訊
        {title: '實際修課', rows: [{title: '進入教程學年度'}, {title: '師資類科'}, {title: '師資生來源'}, {title: '身分別'}, {title: '原住民族別'}, {title: '是否為公費生'}]},
        {title: '在校', rows: [{title: '取得師資生資格學年度'}, {title: '核定名額學年度'}, {title: '師資類科'}, {title: '師資生來源'}, {title: '身分別'}, {title: '原住民族別'}, {title: '是否為公費生'}]},
        {title: '就讀狀況', rows: [{title: '入學學年度'}, {title: '學校名稱'}, {title: '國私立別'}, {title: '學制'}, {title: '畢業學年度'}]},
        //修課狀況
        {title: '完成教育專業課程', rows: [{title: '師資類科'}, {title: '發證年度'}]},
        {title: '完成及認定專門課程', rows: [{title: '師資類科'}, {title: '專門課程認證類別'}, {title: '發證年度'}]},
        //相關活動
        {title: '卓越師資培育獎學金', rows: [{title: '師資類科'}, {title: '入學學年度'}, {title: '取得師資生資格學年度'}, {title: '甄選當時資格屬性別'}]},
        {title: '活動及獎項', rows: [{title: '學制'}, {title: '師資類科'}, {title: '參加活動與獲得獎項'}, {title: '參與時間'}]},

        {title: '實際參與實習', rows: [{title: '實習學年度'}, {title: '實習學期'}, {title: '申請實習類科'}, {title: '實習科別'}, {title: '實習領域群科'}, {title: '實習縣市'}, {title: '實習學校'}]},

        {title: '修畢師資職前教育證明', rows: [{title: '師資類科'}, {title: '發證年度'}, {title: '教育階段類組名稱'}, {title: '檢定科目'}, {title: '領域專長'}]},

        {title: '畢業離校師資生教育實習情況', rows: [{title: '師資類科'}, {title: '師資生來源'}, {title: '核定名額學年度'}, {title: '實習學年度'}, {title: '上學期教育實習狀況'}, {title: '下學期教育實習狀況'}]},

        {title: '教師資格檢定', rows: [{title: '報考年度'}, {title: '報考類科'}, {title: '通過情形'}, {title: '師培學校屬性別'}]},

        {title: '新制教師證書', rows: [{title: '師培大學名稱'}, {title: '師資培育來源'}, {title: '教育階段類組名稱'}, {title: '檢定科目名稱'}, {title: '群科名稱'}, {title: '領域專長科目名稱'}, {title: '發證年度'}, {title: '是否為加科登記'}]},
        {title: '教師專長', rows: [{title: '領域群科'}, {title: '科細項'}, {title: '科別'}, {title: '是否為首登專長'}]},

        {title: '師資身分', rows: [{title: '內政部最高學歷資料'}, {title: '原住民身份代碼'}, {title: '族別'}]},

        {title: '教甄資料', rows: [{title: '甄選教育階段'}, {title: '甄選領域群科'}, {title: '甄選科別'}, {title: '師培學校屬性別'}, {title: '教師證發證年度'}, {title: '甄選學校所屬縣市'}, {title: '錄取情形'}, {title: '甄試年度'}]},

        {title: '在職教師', rows: [{title: '教育階段'}, {title: '公私立別'}, {title: '任教學校所屬縣市'}, {title: '任教首登專長領域群科'}, {title: '任教首登專長科細項'}, {title: '任教首登專長科別'}]},
        {title: '代理代課教師', rows: [{title: '教育階段'}, {title: '任教學校所屬縣市'}, {title: '任教首登專長領域群科'}, {title: '任教首登專長科細項'}, {title: '任教首登專長科別'}]},
        {title: '儲備教師', rows: [{title: '發證年度'}, {title: '職業狀況'}, {title: '職業狀況類別'}, {title: '職業狀況細項'}, {title: '師資培育來源'}, {title: '領域專長'}, {title: '師培學校'}, {title: '任教首登專長領域群科'}, {title: '任教首登專長科細項'}, {title: '任教首登專長科別'}]},
        {title: '離退教師', rows: [{title: '教育階段'}, {title: '任教學校所屬縣市'}, {title: '任教首登專長領域群科'}, {title: '任教首登專長科細項'}, {title: '任教首登專長科別'}]},

        {title: '教師在職進修資料', rows: [{title: '教育階段'}, {title: '研習時數'}]},

        {title: '閩南語檢定', rows: [{title: '通過等級'}, {title: '通過年度'}]},
        {title: '客語檢定', rows: [{title: '等級'}, {title: '腔調'}, {title: '通過年度'}]},
    ];
    $scope.loading = true;

    $http({method: 'POST', url: 'getItems', data:{}})
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
        $scope.loading = false;
    }).error(function(e){
        console.log(e);
    });

    $scope.getRowSpan = function(structs) {
        var rowSpan = structs.length - $filter('filter')(structs, {expanded: true}).length;
        for (i in structs) {
            if (structs[i].expanded) {
                rowSpan += structs[i].rows.length;
            };
        }
        return rowSpan;
    };

    $scope.$watchCollection('columns', function(columns) {
        $scope.levels = $scope.getLevels(columns, 0);
        $scope.preCalculations.length = 0;
        if (columns.length > 0) {
            $scope.addPreCalculation();
        };
        $scope.colPercent = false;
        $scope.rowPercent = false;
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
        if ($scope.calculations.length>0){
            for (var i in $scope.calculations){
                for (var j in $scope.calculations[i].structs){
                    if ($scope.calculations[i].structs[j].title == struct.title ){
                        inStructs = 1;
                    }
                }
                if (inStructs==0 && isadd){
                    $scope.calculations[i].structs.push({title: struct.title, rows: {}});
                }
            }
        }
        if ($scope.calculations.length>0){
            for (var i in $scope.calculations){
                $scope.calculations[i].results = {};
            }
        }
        $scope.needHelp3();
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

    $scope.columns = [];
    $scope.calculations = [];
    $scope.results = [];

    $scope.addPreCalculation = function() {
        var calculation = {structs: [], results: {}};
        for (i in $scope.structs) {
            if ($scope.structs[i].selected && !$scope.structs[i].disabled) {
                var rows = [];
                angular.forEach($filter('filter')($scope.structs[i].rows, function(row, index, array) { return row.filter && row.filter!=''; }), function(row, key) {
                    this.push({title: row.title, filter: row.filter});
                }, rows);
                calculation.structs.push({title: $scope.structs[i].title, rows: rows});
            };
        }
        $scope.preCalculations=[];
        $scope.preCalculations.push(calculation);
    }

    $scope.addNewCalStruct = function(structs){
        var calculation = {structs: [], results: {}};
        for (var i in structs) {
            if (structs[i].selected && !structs[i].disabled) {
                var rows = [];
                angular.forEach($filter('filter')(structs[i].rows, function(row, index, array) { return row.filter && row.filter!=''; }), function(row, key) {
                    this.push({title: row.title, filter: row.filter});
                }, rows);
                calculation.structs.push({title: $scope.structs[i].title, rows: rows});
            };
        }
        $scope.calculations.push(calculation);
        $scope.callCalculation();
    };

    $scope.callCalculation = function(){
        for (var i in $scope.calculations){
            if ($.isEmptyObject($scope.calculations[i].results)){
                $scope.addCalculation($scope.calculations[i].structs,$scope.calculations[i]);
            }
        }
        $scope.getTitle();
        $scope.gotoResultTable();
    };

    $scope.addCalculation = function(calculateStructs,calculation) {
        $http({method: 'POST', url: 'calculate', data:{structs: calculateStructs, columns: $scope.columns}})
        .success(function(data, status, headers, config) {
            calculation.results = data.results;
        }).error(function(e){
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

    $scope.destroyPopup = function() {
        $('#needHelp').popup('destroy');
        $('[name=needHelp2]').popup('destroy');
        $('[name=needHelp3]').popup('destroy');
        $('[name=needHelp4]').popup('destroy');
        $scope.helpChoosen = false;
    };

    $scope.getCrossColumnTotal = function(calculation,levels) {
        var crossColumnTotal = 0;
        for (var i in levels){
            crossColumnTotal = crossColumnTotal+ 1*$scope.getResults(calculation, levels[i]);
        }
        return crossColumnTotal;
    };

    $scope.getNearestColumnTotal = function(calculation,level){
        var nearestColumnTotal = 0;
        var  nearestColumn = $scope.getParentResult(calculation, level.parents);
        for (var i in nearestColumn){
            nearestColumnTotal = nearestColumnTotal+ 1*nearestColumn[i];
        }
        return nearestColumnTotal;
    };

    $scope.getTotalPercent = function(value,total) {
         return total == 0 ? 0 : value*100/total;
    };

    $scope.gotoResultTable = function() {
        $scope.page = 'resoult';
        //$location.hash('resultTable');
        //$anchorScroll();
    };

    $scope.getTitle = function(){
        $scope.tableTitle = {};
        var titles = [];
        for (i in $scope.calculations){
            var title = '';
            for (j in $scope.calculations[i].structs){
                title = title+$scope.calculations[i].structs[j].title+' ';
                for (k in $scope.calculations[i].structs[j].rows){
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

    $scope.getStructsTitile = function(key){
        var title = [];
        for (i in $scope.calculations[key].structs){
            title.push($scope.calculations[key].structs[i].title);
            for (j in $scope.calculations[key].structs[i].rows){
                    title.push($scope.calculations[key].structs[i].rows[j].title);
                    title.push($scope.calculations[key].structs[i].rows[j].title+'-'+$scope.calculations[key].structs[i].rows[j].filter);
            }
        }
        return title;
    };

    $scope.checkInArray = function(value){
        if (this.indexOf(value)>-1){
            return true;
        }else{
            return false;
        }
    };

    $scope.restrictInvolve = function(key){
        if (key>0) {
            var denominator = $scope.getStructsTitile (key-1);
            var molecular = $scope.getStructsTitile (key);
            return denominator.every($scope.checkInArray,molecular);
        }else{
            return false;
        }
    };

    $scope.getRowPercent = function(key,level){
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

        if ($scope.dragBefore > dragAfter){
            $scope.calculations.splice($scope.dragBefore,1);
            $scope.calculations.splice(dragAfter+1,0,moveCalculation);
        }
        if ($scope.dragBefore < dragAfter){
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

        if ($scope.calculations.length>0){
            for (var i in $scope.calculations){
                $scope.calculations[i].results = {};
            }
        }

        if ($scope.changeColumnBefore > dragAfter){
            $scope.columns.splice($scope.changeColumnBefore,1);
            $scope.columns.splice(dragAfter+1,0,moveColumn);
            $scope.callCalculation();
        }
        if ($scope.changeColumnBefore < dragAfter){
            $scope.columns.splice(dragAfter+1,0,moveColumn);
            $scope.columns.splice($scope.changeColumnBefore,1);
            $scope.callCalculation();
        }
        $scope.changeColumnBefore = [];
    };

    $(".unselectable").css( {
       'mozUserSelect': 'mozNone',
       'khtmlUserSelect': 'none',
       'webkitUserSelect': 'none',
       'msUserSelect': 'none',
       'userSelect': 'none'
    });

})
.directive('ngMutiDropdownMenu', function($timeout, $window) {
    return {
        restrict: 'A',
        scope: {},
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            element.dropdown({
                allowAdditions: true,
                onChange: function(value, text, $choice) {
                    ngModelCtrl.$setViewValue(value);
                }
            });
        }
    };
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
                values: scope.items,
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
})
.directive('ngSemanticDropdownMenu', function($timeout, $window) {
    return {
        restrict: 'A',
        scope: {
            ngChange: '&'
        },
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            element.dropdown({
                transition: 'drop',
                onChange: function(value, text, $choice) {
                    if (value != scope.ngModel) {
                        scope.$apply(function() {
                            ngModelCtrl.$setViewValue(value);
                        });
                        scope.ngChange();
                    };
                }
            });
        },

    };

});
</script>
