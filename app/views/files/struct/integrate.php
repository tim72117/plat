<div ng-cloak ng-controller="statusController">

    <div class="ui basic segment" style="width: 100%">
        <section layout="row" flex style="width: 22%;float: left">
            <md-sidenav class="md-sidenav-left" md-component-id="left" md-is-locked-open="$mdMedia('gt-md')" md-disable-backdrop md-whiteframe="4" style="width: 100%">
                <md-toolbar class="md-theme-indigo">
                    <h1 class="md-toolbar-tools" style="font-weight: bold;background: #046D8B;color: white" ng-if="academicYearType==1">{{progress[academicYearType]}}</h1>
                    <h1 class="md-toolbar-tools" style="font-weight: bold;background: #CB0051;color: white" ng-if="academicYearType==2">{{progress[academicYearType]}}</h1>
                    <h1 class="md-toolbar-tools" style="font-weight: bold;background: #4A970A;color: white" ng-if="academicYearType==3">{{progress[academicYearType]}}</h1>
                </md-toolbar>
                <md-content layout-padding style="overflow-y: auto;max-height:10%;max-height: 300px">
                    <div class="ui ribbon label" style="background: #309292;color: white">
                        <h4>選擇分析學校</h4>
                    </div>
                    <div class="item" align="center">
                    <md-input-container>
                            <label>學校</label>
                            <md-select ng-model="selectSchool" aria-label="schools" multiple >
                                <md-button layout-fill value="all" ng-click="selectAllSchool()">全選</md-button>
                                <md-optgroup >
                                    <md-option ng-value="school" ng-repeat="school in schools">{{school.name}}</md-option>
                                </md-optgroup>
                            </md-select>
                    </md-input-container>
                    </div>
                </md-content>
                <md-content layout-padding>
                    <div class="ui ribbon label" style="background: #309292;color: white">
                        <h4>選擇學年度</h4>
                    </div>
                    <div class="item" align="center" ng-if="academicYearType==1">
                        <p>占教育部核定名額學年度</p>
                        <p>自
                            <md-input-container>
                                <md-select placeholder="學年度" ng-model="academicYearStart" md-on-open="loadItem(structs[idType].title,structs[idType].rows[academicYearType].title)" style="max-width: 100px" ng-change="setAcademicYear(academicYearStart,academicYearEnd,structs[idType],structs[idType].rows[academicYearType])">
                                    <md-progress-circular ng-if="!structs[idType].rows[academicYearType].items" md-diameter="20px"></md-progress-circular>
                                    <md-option ng-value="{{academicYear}}" ng-repeat="academicYear in structs[idType].rows[academicYearType].items">{{academicYear}}</md-option>
                                </md-select>
                            </md-input-container>
                        學年度起</p>
                        <p>至
                            <md-input-container>
                                <md-select placeholder="學年度" ng-model="academicYearEnd" md-on-open="loadItem(structs[idType].title,structs[idType].rows[academicYearType].title)" style="max-width: 100px" ng-change="setAcademicYear(academicYearStart,academicYearEnd,structs[idType],structs[idType].rows[academicYearType])">
                                    <md-progress-circular ng-if="!structs[idType].rows[academicYearType].items" md-diameter="20px"></md-progress-circular>
                                    <md-option ng-value="{{academicYear}}" ng-repeat="academicYear in structs[idType].rows[academicYearType].items">{{academicYear}}</md-option>
                                </md-select>
                            </md-input-container>
                        學年度止</p>
                    </div>
                    <div class="item" align="center" ng-if="academicYearType==2">
                        <p>發證年度</p>
                        <p>自
                            <md-input-container>
                                <md-select placeholder="年" ng-model="academicYearStart" md-on-open="loadItem(structs[idType].title,structs[idType].rows[academicYearType].title)" style="max-width: 100px" ng-change="setAcademicYear(academicYearStart,academicYearEnd,structs[idType],structs[idType].rows[academicYearType])">
                                    <md-progress-circular ng-if="!structs[idType].rows[academicYearType].items" md-diameter="20px"></md-progress-circular>
                                    <md-option ng-value="{{academicYear}}" ng-repeat="academicYear in structs[idType].rows[academicYearType].items">{{academicYear}}</md-option>
                                </md-select>
                            </md-input-container>
                        年起</p>
                        <p>至
                            <md-input-container>
                                <md-select placeholder="年" ng-model="academicYearEnd" md-on-open="loadItem(structs[idType].title,structs[idType].rows[academicYearType].title)" style="max-width: 100px" ng-change="setAcademicYear(academicYearStart,academicYearEnd,structs[idType],structs[idType].rows[academicYearType])">
                                    <md-progress-circular ng-if="!structs[idType].rows[academicYearType].items" md-diameter="20px"></md-progress-circular>
                                    <md-option ng-value="{{academicYear}}" ng-repeat="academicYear in structs[idType].rows[academicYearType].items">{{academicYear}}</md-option>
                                </md-select>
                            </md-input-container>
                        年止</p>
                    </div>
                    <div class="item" align="center" ng-if="academicYearType==3">
                        <p>參與教育實習學年度</p>
                        <p>自
                            <md-input-container>
                                <md-select placeholder="學年度" ng-model="academicYearStart" md-on-open="loadItem(structs[idType].title,structs[idType].rows[academicYearType].title)" style="max-width: 100px" ng-change="setAcademicYear(academicYearStart,academicYearEnd,structs[idType],structs[idType].rows[academicYearType])">
                                    <md-progress-circular ng-if="!structs[idType].rows[academicYearType].items" md-diameter="20px"></md-progress-circular>
                                    <md-option ng-value="{{academicYear}}" ng-repeat="academicYear in structs[idType].rows[academicYearType].items">{{academicYear}}</md-option>
                                </md-select>
                            </md-input-container>
                        學年度起</p>
                        <p>至
                            <md-input-container>
                                <md-select placeholder="學年度" ng-model="academicYearEnd" md-on-open="loadItem(structs[idType].title,structs[idType].rows[academicYearType].title)" style="max-width: 100px" ng-change="setAcademicYear(academicYearStart,academicYearEnd,structs[idType],structs[idType].rows[academicYearType])">
                                    <md-progress-circular ng-if="!structs[idType].rows[academicYearType].items" md-diameter="20px"></md-progress-circular>
                                    <md-option ng-value="{{academicYear}}" ng-repeat="academicYear in structs[idType].rows[academicYearType].items">{{academicYear}}</md-option>
                                </md-select>
                            </md-input-container>
                        學年度止</p>
                    </div>
                </md-content>
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
                                <!-- first row checkbox -->
                                <td ng-class="{negative: structs[idType].rows[0].disabled, disabled: structs[idType].rows[0].disabled}">
                                    <i class="icon close" ng-if="structs[idType].rows[0].disabled"></i>
                                    <div class="ui checkbox" >
                                        <input id="{{ ::$id }}" type="checkbox" ng-model="structs[idType].rows[0].selected" ng-change="toggleColumn(structs[idType].rows[0], structs[idType])">
                                        <label for="{{ ::$id }}" >{{ structs[idType].rows[0].title }}</label>
                                    </div>
                                </td>

                                <!-- all rows title -->
                                <td ng-if="!structs[idType].expanded">
                                    <a href="javascript:void(0)" ng-click="structs[idType].expanded=true">
                                    <div style="width:180px;text-overflow: ellipsis;overflow:hidden !important;white-space: nowrap">
                                        <span id="needHelp" class="item" ng-repeat="row in structs[idType].rows">{{ row.title }}{{ $last ? '' : ',' }}</span>
                                    </div>
                                    </a>
                                </td>

                                <!-- first row filter -->
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

                                <!-- rows checkbox -->
                                <td ng-class="{negative: row.disabled, disabled: row.disabled}">
                                    <i class="icon close" ng-if="row.disabled"></i>
                                    <div class="ui checkbox">
                                        <input id="{{ ::$id }}" type="checkbox" ng-model="row.selected" ng-change="toggleColumn(row, structs[idType])" class="hidden">
                                        <label for="{{ ::$id }}">{{ row.title }}</label>
                                    </div>
                                </td>

                                <!-- rows filter -->
                                <td ng-class="{disabled: row.disabled}">
                                    <div ng-if="row.type=='slider'">
                                        {{ row.filter[0] }}年至{{ row.filter[1] }}
                                        <div ng-slider ng-model="row.filter" items="row.items"></div>
                                    </div>
                                    <md-input-container ng-if="!row.disabled && row.type!='slider'">
                                        <md-select placeholder="{{ row.title }}" ng-model="row.filter" md-on-open="loadItem(structs[idType].title,row.title)" multiple ng-change="setFilter(structs[idType])">
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
        </section>
        <!--</div>-->
        <div class="ui basic segment" ng-class="{loading: loading}" style="width: 75%;float: left">
            <div class="ui tabular menu">
                <a class="item" ng-class="{active: page == 'explan'}" ng-click="page='explan'">資料欄位說明 </a>
                <a class="item" ng-class="{active: page == 'editor'}" ng-click="page='editor'">串聯其他表單 </a>
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
                        <th>師培及就業歷程</th>
                        <th>表單名稱</th>
                        <th colspan="2">欄位名稱</th>
                        <th>欄位說明</th>
                    </tr>
                </thead>
                <tbody>

                <tr ng-repeat-start="(index, explan) in explans" class="no-animate">

                    <td rowspan="{{ getExplanSpan(explans.slice(index, index+structClass[explan.title].size)) }}" ng-if="structClass[explan.title]">{{ structClass[explan.title].title }}</td>

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
                        {{ explanation.content }}
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

                    <!-- explanations checkbox -->
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
                <a href="/files/explan.xlsx" style="color: white">
                    <i class="download icon"></i> 下載
                </a>
                </button>
            </div>

            <div style="-webkit-flex: initial;flex: initial;min-width: 300px" ng-show="page == 'editor'">
            <table class="ui teal collapsing celled structured very compact table" ng-class="{small:tableSize=='small', large:tableSize=='large'}" style="background: #F5F5F5">
                <thead>
                    <tr>
                        <th class="center aligned"><!--<div class="ui mini button" ng-click="needHelp($event)" style="background: #ECBE13;color: white"><i class="icon help outline"></i>需要幫忙</div>--></th>
                        <th>發展歷程</th>
                        <th>表單名稱</th>
                        <th colspan="2" ng-if="structClassShow">選擇欄位</th>
                        <th ng-if="structFilterShow">選擇分析目標或篩選條件</th>
                    </tr>
                </thead>
                <tbody>

                    <tr ng-repeat-start="(index, struct) in structs" ng-hide="index==idType" class="no-animate">
                        <td rowspan="{{ getRowSpan(structs) }}" ng-if="$first" class="no-animate">
                            <button class="compact ui olive icon large button" ng-click="addNewCalStruct(structs);destroyPopup()" style="background: #93A42A;color: white">
                                <i name="needHelp4" class="play icon"></i> 開始計算
                            </button>
                        </td>
                        <td rowspan="{{ structClass[struct.title].expanded ? getRowSpan(structs.slice(index, index+structClass[struct.title].size)) : 1 }}" ng-if="structClass[struct.title]" class="no-animate">
                            <a href="javascript:void(0)" ng-click="showStruct(struct.title,structClass[struct.title].title)">
                                {{ structClass[struct.title].title }}
                            </a>
                        </td>

                        <td ng-if="!struct.classExpanded && structClass[struct.title]" class="no-animate">
                            <div style="width:180px;text-overflow: ellipsis;overflow:hidden !important;white-space: nowrap">
                                <span class="item" ng-repeat="struct in structInClass[structClass[struct.title].title].structs">{{ struct.title }}{{ $last ? '' : ',' }}</span>
                            </div>
                        </td>

                        <td ng-if="struct.classExpanded" rowspan="{{ struct.expanded ? struct.rows.length : 1 }}" class="no-animate">
                            <a href="javascript:void(0)" ng-click="showFilter(struct)">
                                {{ struct.title }}
                            </a>
                            <div class="compact ui icon mini basic vertical buttons">
                                <button class="ui button" ng-if="!struct.disabled" ng-click="toggleStruct(struct)" >
                                    <i name="needHelp2" class="checkmark icon" ng-class="{green: !struct.disabled && struct.selected}"></i>
                                </button>
                            </div>
                        </td>

                        <!-- cell if not expand -->
                        <td colspan="2" ng-if="structClassShow && !struct.classExpanded && structClass[struct.title]" class="no-animate">
                        </td>
                        <td ng-if="structFilterShow && !struct.classExpanded && structClass[struct.title]" class="no-animate">
                        </td>

                        <!-- expand button -->
                        <td rowspan="{{ struct.expanded ? struct.rows.length : 1 }}" ng-if="struct.expanded" class="no-animate">
                            <div class="compact ui icon mini basic vertical buttons" ng-if="!struct.disabled || struct.expanded">
                                <button class="ui button" ng-click="struct.expanded=false">
                                    <i class="compress icon"></i>
                                </button>
                            </div>
                        </td>

                        <!-- first row checkbox -->
                        <td ng-if="struct.expanded && struct.classExpanded" ng-class="{negative: struct.rows[0].disabled, disabled: struct.rows[0].disabled}" class="no-animate">
                            <i class="icon close" ng-if="struct.rows[0].disabled"></i>
                            <div class="ui checkbox" >
                                <input id="{{ ::$id }}" type="checkbox" ng-model="struct.rows[0].selected" ng-change="toggleColumn(struct.rows[0], struct)" ng-click="needHelp2()" class="hidden"  name="needHelp5">
                                <label for="{{ ::$id }}" >{{ struct.rows[0].title }}</label>
                            </div>
                        </td>

                        <!-- all rows title -->
                        <td ng-if="!struct.expanded && struct.classExpanded" colspan="{{struct.expanded ? 1 : 2}}" class="no-animate">
                            <div style="width:180px;text-overflow: ellipsis;overflow:hidden !important;white-space: nowrap">
                                <span id="needHelp" class="item" ng-repeat="row in struct.rows">{{ row.title }}{{ $last ? '' : ',' }}</span>
                            </div>
                        </td>

                        <!-- first row filter -->
                        <td ng-class="{disabled: struct.rows[0].disabled}" ng-if="struct.classExpanded && structFilterShow" class="no-animate">
                            <md-input-container ng-if="!struct.rows[0].disabled && struct.expanded">
                                <md-select placeholder="{{ struct.rows[0].title }}" ng-model="struct.rows[0].filter" md-on-open="loadItem(struct.title,struct.rows[0].title)" multiple ng-change="setFilter(struct)">
                                    <md-progress-circular ng-if="!struct.rows[0].items" md-diameter="20px"></md-progress-circular>
                                    <md-optgroup >
                                        <md-option ng-value="item" ng-repeat="item in struct.rows[0].items" ng-click="needHelp4()">{{item}}</md-option>
                                    </md-optgroup>
                                </md-select>
                            </md-input-container>
                        </td>
                    </tr>

                    <tr ng-repeat-end ng-repeat="row in struct.rows" ng-if="!$first && struct.expanded" ng-hide="index==idType">

                        <!-- rows checkbox -->
                        <td ng-class="{negative: row.disabled, disabled: row.disabled}" class="no-animate">
                            <i class="icon close" ng-if="row.disabled"></i>
                            <div class="ui checkbox">
                                <input id="{{ ::$id }}" type="checkbox" ng-model="row.selected" ng-change="toggleColumn(row, struct)" class="hidden" ng-click="needHelp2()">
                                <label for="{{ ::$id }}">{{ row.title }}</label>
                            </div>
                        </td>

                        <!-- rows filter -->
                        <td ng-class="{disabled: row.disabled}" ng-if="structFilterShow" >
                            <div ng-if="row.type=='slider'">
                                {{ row.filter[0] }}年至{{ row.filter[1] }}
                                <div ng-slider ng-model="row.filter" items="row.items"></div>
                            </div>
                            <md-input-container ng-if="!row.disabled && row.type!='slider'">
                                <md-select placeholder="{{ row.title }}" ng-model="row.filter" md-on-open="loadItem(struct.title,row.title)" multiple ng-change="setFilter(struct)">
                                    <md-progress-circular ng-if="!row.items" md-diameter="20px"></md-progress-circular>
                                    <md-optgroup >
                                        <md-option ng-value="item" ng-repeat="item in row.items" >{{item}}</md-option>
                                    </md-optgroup>
                                </md-select>
                            </md-input-container>
                        </td>

                    </tr>

                </tbody>
            </table>
            </div>

            <div style="-webkit-flex: 1;flex: 1;margin-left:10px" ng-show="page == 'editor'">
            <table class="ui collapsing celled structured very compact bottom attached table" ng-class="{small:tableSize=='small', large:tableSize=='large'}">
                <thead>
                    <tr>
                        <th colspan="{{ preCalculations.length+columns.length }}" style="background: #2FB8AC;color: white;text-align: center">預覽</th>
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

            <table class="ui teal collapsing celled structured very compact bottom attached table" ng-class="{small:tableSize=='small', large:tableSize=='large'}" ng-show="page == 'resoult'">
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
    $scope.tableSize = '';
    $scope.preCalculations = [];
    $scope.dragBefore = [];
    $scope.changeColumnBefore = [];
    $scope.progress = {1:'新進師資生', 2:'完成職前教育師資生', 3:'實習師資生'};
    $scope.idType = 9;//1:新進師資生;9:實習師資生;10:完成職前教育師資生
    $scope.academicYearType = 3;//1:新進師資生(核定名額學年度);3:實習師資生(參與教育實習學年度);2:完成職前教育師資生(發證年度)
    $scope.structClassShow = false;
    $scope.structFilterShow = false;
    $scope.structClass = {
        '個人資料': {title: '基本資料', size: 1},
        '就學資訊': {title: '就學資訊', size: 1},
        '完成教育專業課程': {title: '修課狀況', size: 2},
        '卓越師資培育獎學金': {title: '相關活動', size: 5},
        '實際參與實習': {title: '教育實習', size: 1},
        '修畢師資職前教育證明書': {title: '師資職前教育', size: 1},
        '教師資格檢定': {title: '教檢情形', size: 1},
        '教師專長': {title: '教師專長', size: 1},
        '教甄資料': {title: '教師甄試', size: 1},
        '在職教師': {title: '教師就業狀況', size: 4},
        '閩南語檢定': {title: '語言檢定', size: 2}
    };

    $scope.loading = true;
    $scope.structs = [];

    $http({method: 'POST', url: 'getPopulation', value:{}})
    .success(function(value, status, headers, config) {
        if (value == 1){
            $scope.idType = 1;//新進師資生
            $scope.academicYearType = 1;//新進師資生
        } else if (value == 2){
            $scope.idType = 9;//實習師資生
            $scope.academicYearType = 3;//實習師資生(參與教育實習學年度)
        } else{
            $scope.idType = 10;//完成職前教育師資生
            $scope.academicYearType = 2;//完成職前教育師資生(發證年度)
        }
    }).error(function(e){
        console.log(e);
    });

    $http({method: 'POST', url: 'getStructs', data:{}})
    .success(function(data, status, headers, config) {
        $scope.structs = data;
        $scope.structs[$scope.idType].expanded = true;
        $scope.loading = false;
    }).error(function(e){
        console.log(e);
    });

    $scope.explans = [];
    $http({method: 'POST', url: 'getExplans', data:{}})
    .success(function(data, status, headers, config) {
        $scope.explans = data;
    }).error(function(e){
        console.log(e);
    });



    /*$scope.updateItem = function(selectSchoolID){
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
        }).error(function(e){
            console.log(e);
        });
    };*/

    $scope.loadItem = function(structTitle,rowTitle, callback){
        $http({method: 'POST', url: 'getEachItems', data:{schoolID: $scope.selectSchoolID,structTitle: structTitle,rowTitle: rowTitle}})
        .success(function(data, status, headers, config) {
            var struct = $scope.structs[data.key];
            var rows = data.tables[struct.title] || {};
            struct.disabled = Object.keys(rows).length == 0;
            for (var j in struct.rows) {
                if (struct.rows[j].title == rowTitle){
                    struct.rows[j].items = rows[struct.rows[j].title] || [];
                    struct.rows[j].disabled = struct.rows[j].items == 0;
                    if (struct.rows[j].title == '年齡') struct.rows[j].disabled = true;
                }
            };
            $scope.structs[0].rows[1].type = 'slider';
            if (callback){
                callback();
            }
        }).error(function(e){
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
    }).error(function(e){
        console.log(e);
    });

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
        }
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
                    this.push({title: row.title, filter: row.filter.toString()});
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
                    this.push({title: row.title, filter: row.filter.toString()});
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
        $http({method: 'POST', url: 'calculate', data:{structs: calculateStructs, columns: $scope.columns, schoolID: $scope.selectSchoolID}})
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

    $scope.setAcademicYear = function(startYear,endYear,struct,column){
        column.filter = [];
        if (typeof(startYear)!='undefined'){
            if (typeof(endYear)!='undefined'){
                if (endYear*1 >= startYear*1){
                    for (var i = startYear*1; i < endYear*1+1; i++){
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

    $scope.showStruct = function(firstStruct,classTitle){
        $scope.structClass[firstStruct].expanded = true;
        $scope.structClassShow = true;
        for (var i in $scope.structInClass[classTitle].structs){
            for (var j in $scope.structs){
                if ($scope.structs[j].title == $scope.structInClass[classTitle].structs[i].title){
                     $scope.structs[j].classExpanded = true;
                }
            }
        }
    };

    $scope.showFilter = function(struct){
        $scope.structFilterShow = true;
        struct.expanded=true;
    };

    $scope.getExplanSpan = function(explans) {
        var explanSpan = explans.length - $filter('filter')(explans, {expanded: true}).length;
        for (i in explans) {
            if (explans[i].expanded) {
                explanSpan += explans[i].explanations.length;
            };
        }
        return explanSpan;
    };

    $scope.selectAllSchool = function(){
        if (JSON.stringify($scope.selectSchool) === JSON.stringify($scope.schools) ){
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
