<div class="ui basic segment">
    <div class="ui two column grid">
        <div class="fifteen wide column">
            <div class="ui fluid dropdown selection multiple" ng-class="{active: false, visible: false, disabled: loading}">
                <a ng-repeat="column in selected.columns" class="ui label visible" style="max-width:100%;word-wrap:break-all"
                   ng-click="selected.columns.length = 0;clear(column)">
                    <i class="columns icon"></i>
                    {{ column.title }}
                </a>
                <div class="default text"><i class="columns icon"></i>Columns 欄變數</div>
            </div>
            <div class="ui hidden fitted divider"></div>
            <div class="ui fluid dropdown selection multiple" ng-class="{active: false, visible: false, disabled: loading}">
                <a ng-repeat="column in selected.rows" class="ui label visible" style="max-width:100%; word-wrap:break-all"
                   ng-click="selected.rows.length = 0;clear(column)">
                    <i class="columns icon"></i>
                    {{ column.title }}
                </a>
                <div class="default text"><i class="columns icon"></i>Rows 列變數</div>
            </div>

        </div>
        <div class="ui vertical divider"><i class="refresh link icon" ng-click="exchange()"></i></div>
        <div class="one wide column"></div>
    </div>
</div>

            <div class="ui top attached tabular menu">
                <div class="right menu">
                    <div class="item">
                        <div ng-semantic-dropdown-menu ng-model="result" ng-change="changeChart()" class="ui top pointing dropdown">
                            <span class="default text"><i class="wizard icon"></i>{{outputType}}</span>
                            <div class="menu">
                                <div class="item" ng-repeat="chart in charts" ng-class="{disabled: disabledCharts[chart.name]}" data-value="{{ chart.name }}" >
                                    <i class="{{ chart.icon }} icon"></i> {{ chart.title }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<div class="ui basic segment" ng-class="{loading: loading || counting}">

    <div>
        <div ng-if="result == 'pie'">
            <div ng-if="selected.rows.length == 0 && selected.columns.length > 0">
                <h4 class="ui header" ng-repeat="column in selected.columns">{{ column.title }}</h4>
            </div>
            <div ng-if="selected.rows.length > 0 && selected.columns.length == 0">
                <h4 class="ui header" ng-repeat="row in selected.rows">{{ selected.rows[0].title }}</h4>
            </div>
        </div>
        <div ng-if="result == 'bar'" id="bar-container" style="{{ 'min-height:'+getChartHeight()+'px' }}"></div>
        <div ng-if="result == 'pie'" id="pie-container"></div>
    </div>

    <div class="ui bottom attached" style="overflow:auto" ng-if="result == 'table' && selected.rows.length == 0 && selected.columns.length > 0">
        <div style="min-width:500px">
            <table class="ui celled structured table">
                <thead>
                    <tr>
                        <th><button class="ui mini button" ng-click="setMean()"><i class="plus icon"></i>平均數</button></th>
                        <th class="left aligned" colspan="{{ selected.columns[0].answers.length+meanSet+1 }}" ng-repeat="column in selected.columns" >{{ column.title }}</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th class="top aligned right aligned" style="min-width: 80px" ng-repeat="answer in selected.columns[0].answers">{{ answer.title }}</th>
                        <th class="top aligned right aligned" style="min-width: 80px">總和</th>
                        <th class="top aligned right aligned" style="min-width: 80px" ng-if="meanSet">平均</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat-start="(id, target) in targetsSelected()">
                        <td rowspan="2" class="single line">{{ target.name }}</td>
                        <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat="answer in selected.columns[0].answers">
                            {{ frequence[id][answer.value] || 0 }} <br/>
                        </td>
                        <td class="right aligned">{{ getFrequenceTotal(selected.columns[0].answers, id) }}</td>
                        <td ng-if="meanSet" class="right aligned" rowspan="2">{{ getMean(selected.columns[0].answers, id) | number : 2 }}</td>
                    </tr>
                    <tr ng-repeat-end>
                        <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat="answer in selected.columns[0].answers">
                            {{ getTotalPercent(getFrequenceTotal(selected.columns[0].answers, id), frequence[id][answer.value] || 0) | number : 2 }}%
                        </td>
                        <td class="right aligned">100%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div style="overflow:auto" ng-if="result == 'table' && selected.columns.length == 0 && selected.rows.length > 0">
        <div style="min-width:300px">
            <table class="ui celled structured table">
                <tbody>
                    <tr ng-repeat-start="(id, target) in targetsSelected()">
                        <td rowspan="{{ selected.rows[0].answers.length+meanSet+1 }}" style="background: #f9fafb;text-align: inherit;color: rgba(0,0,0,.87);font-weight: 700"><button class="ui mini button" ng-click="setMean()"><i class="plus icon"></i>平均數</button></td>
                        <td rowspan="{{ selected.rows[0].answers.length+meanSet+1 }}" style="background: #f9fafb;text-align: inherit;color: rgba(0,0,0,.87);font-weight: 700">{{ selected.rows[0].title }}</td>
                        <td rowspan="{{ selected.rows[0].answers.length+meanSet+1 }}" style="font-weight: 700; background-color:#f9fafb">{{ target.name }}</td>
                        <td class="left aligned" style="font-weight: 700">{{ selected.rows[0].answers[0].title }}</td>
                        <td class="right aligned" ng-class="{disabled: target.loading}">
                            {{ frequence[id][selected.rows[0].answers[0].value] || 0 }}
                        </td>
                        <td class="right aligned">
                            {{ getTotalPercent(getFrequenceTotal(selected.rows[0].answers, id), frequence[id][selected.rows[0].answers[0].value] || 0) | number : 2 }}%
                        </td>
                    </tr>
                    <tr ng-repeat="(key, answer) in selected.rows[0].answers" ng-if="key!=0">
                        <td class="left aligned" style="font-weight: 700">{{ answer.title }}</td>
                        <td class="right aligned" ng-class="{disabled: target.loading}">
                            {{ frequence[id][answer.value] || 0 }}
                        </td>
                        <td class="right aligned">
                            {{ getTotalPercent(getFrequenceTotal(selected.rows[0].answers, id), frequence[id][answer.value] || 0) | number : 2 }}%
                        </td>
                    </tr>
                    <tr>
                        <td class="left aligned" style="font-weight: 700">總和</td>
                        <td class="right aligned" >{{ getFrequenceTotal(selected.rows[0].answers, id) }}</td>
                        <td class="right aligned" >100%</td>
                    </tr>
                    <tr ng-if="meanSet" ng-repeat-end>
                        <td class="left aligned" style="font-weight: 700">平均</td>
                        <td class="right aligned" colspan="2">{{ getMean(selected.rows[0].answers, id) | number : 2 }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div style="overflow:auto" ng-if="result == 'table' && selected.rows.length > 0 && selected.columns.length > 0">
        <div style="width:{{ selected.columns[0].answers.length*120+120 }}px;min-width:600px">

            <table class="ui celled structured table">
                <thead>
                    <tr>
                        <th style="min-width:150px" colspan="3">
                            <md-input-container>
                                <md-select ng-model="tableOption" ng-change="showPercent(tableOption)" aria-label="加入">
                                    <md-option ng-repeat="option in tableOptions" value="{{option.abbrev}}">{{option.abbrev}}</md-option>
                                </md-select>
                            </md-input-container>
                        </th>
                        <th ng-if="!totalPercent" colspan="{{ colPercent ? column.answers.length*2+2 : column.answers.length*1+1+meanSet }}" ng-repeat="column in selected.columns" >{{ column.title }}</th>
                        <th ng-if="totalPercent" colspan="{{ column.answers.length*2+2}}" ng-repeat="column in selected.columns" >{{ column.title }}</th>
                    </tr>
                    <tr ng-if="!totalPercent">
                        <th colspan="3"></th>
                        <th colspan="{{ colPercent ? 2 : 1 }}" class="top aligned left aligned" ng-repeat="answer in selected.columns[0].answers">{{ answer.title }}</th>
                        <th colspan="{{ colPercent ? 2 : 1 }}">總和</th>
                        <th ng-if="meanSet">平均</th>
                    </tr>
                    <tr ng-if="totalPercent">
                        <th colspan="3"></th>
                        <th colspan="{{ totalPercent ? 2 : 1 }}" class="top aligned left aligned" ng-repeat="answer in selected.columns[0].answers">{{ answer.title }}</th>
                        <th colspan="{{ totalPercent ? 2 : 1 }}">總和</th>
                        <th ng-if="meanSet">平均</th>
                    </tr>
                </thead>
                <tbody ng-if="!totalPercent">
                    <tr ng-repeat-start="(id, target) in targetsSelected()">
                        <td rowspan="{{ selected.rows[0].answers.length*(rowPercent ? 2 : 1)+1+meanSet }}" style="background: #f9fafb;text-align: inherit;color: rgba(0,0,0,.87);font-weight: 700">
                            {{ selected.rows[0].title }}
                        </td>
                        <td class="single line" rowspan="{{ selected.rows[0].answers.length*(rowPercent ? 2 : 1)+1+meanSet }}" style="font-weight: 700">{{ target.name }}</td>
                        <td class="single line" rowspan="{{ rowPercent ? 2 : 1 }}" style="font-weight: 700">{{ selected.rows[0].answers[0].title }}</td>
                        <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat-start="answer in selected.columns[0].answers">
                            {{ crosstable[id][answer.value][selected.rows[0].answers[0].value] || 0 }}
                        </td>
                        <td class="right aligned" ng-repeat-end ng-if="colPercent">
                            {{ getTotalPercent(getCrossColumnTotal(id,answer.value),crosstable[id][answer.value][selected.rows[0].answers[0].value]  || 0) | number : 2 }}%
                        </td>
                        <td class="right aligned" ng-class="{disabled: target.loading}">
                            {{ getCrossRowTotal(id,selected.rows[0].answers[0].value) }}
                        </td>
                        <td class="right aligned" ng-if="colPercent">
                            {{ getTotalPercent(getCrossTotal(id),getCrossRowTotal(id,selected.rows[0].answers[0].value)  || 0) | number : 2 }}%
                        </td>
                        <td class="right aligned" ng-if="meanSet">
                            {{getCrossRowMean(id,selected.rows[0].answers[0].value) | number : 2 }}
                        </td>
                    </tr>

                    <!-- row first all percent -->
                    <tr ng-if="rowPercent">
                        <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat="column_answer in selected.columns[0].answers">
                            {{ getTotalPercent(getCrossRowTotal(id,selected.rows[0].answers[0].value), crosstable[id][column_answer.value][selected.rows[0].answers[0].value] || 0) | number : 2 }}%
                        </td>
                        <td class="right aligned">100%</td>
                    </tr>

                    <tr ng-repeat-start="(key, row_answer) in selected.rows[0].answers" ng-if="key!=0">
                        <td class="single line" rowspan="{{ rowPercent ? 2 : 1 }}" style="font-weight: 700">{{ row_answer.title }}</td>
                        <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat-start="column_answer in selected.columns[0].answers">
                            {{ crosstable[id][column_answer.value][row_answer.value] || 0 }}
                        </td>
                        <!-- columns percent -->
                        <td class="right aligned" ng-repeat-end ng-if="colPercent">
                            {{ getTotalPercent(getCrossColumnTotal(id,column_answer.value),crosstable[id][column_answer.value][row_answer.value] || 0) | number : 2 }}%
                        </td>

                        <td class="right aligned" ng-class="{disabled: target.loading}">{{getCrossRowTotal(id,row_answer.value)}}</td>

                        <!-- columns total percent -->
                        <td class="right aligned" ng-if="colPercent">
                            {{ getTotalPercent(getCrossTotal(id),getCrossRowTotal(id,row_answer.value) || 0) | number : 2 }}%
                        </td>
                        <td class="right aligned" ng-if="meanSet">
                            {{getCrossRowMean(id,row_answer.value) | number : 2 }}
                        </td>
                    </tr>

                    <!-- row all percent -->
                    <tr ng-repeat-end ng-if="!$first && rowPercent">
                        <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat="column_answer in selected.columns[0].answers">
                            {{ getTotalPercent(getCrossRowTotal(id,row_answer.value), crosstable[id][column_answer.value][row_answer.value] || 0) | number : 2 }}%
                        </td>
                        <td class="right aligned">100%</td>
                    </tr>

                    <tr >
                        <td class="single line" style="font-weight: 700">總和</td>
                        <td class="right aligned" ng-repeat-start="answer in selected.columns[0].answers">{{ getCrossColumnTotal(id,answer.value) }}</td>
                        <td class="right aligned" ng-repeat-end ng-if="colPercent">100%</td>
                        <td class="right aligned" colspan="{{1+meanSet}}" rowspan="{{1+meanSet}}">{{ getCrossTotal(id) }} </td>
                        <td class="right aligned" ng-if="colPercent">100%</td>
                    </tr>
                    <tr ng-repeat-end ng-if="meanSet">
                        <td class="single line" style="font-weight: 700">平均</td>
                        <td class="right aligned" ng-repeat="answer in selected.columns[0].answers">{{ getCrossColumnMean(id,answer.value) | number : 2 }}</td>
                    </tr>
                </tbody>
                <!--if total percent been choose-->
                <tbody ng-if="totalPercent">
                    <tr ng-repeat-start="(id, target) in targetsSelected()">
                        <td rowspan="{{ selected.rows[0].answers.length*(rowPercent ? 2 : 1)+1+meanSet }}" style="background: #f9fafb;text-align: inherit;color: rgba(0,0,0,.87);font-weight: 700">
                            {{ selected.rows[0].title }}
                        </td>
                        <td class="single line" rowspan="{{ selected.rows[0].answers.length*(rowPercent ? 2 : 1)+1+meanSet }}" style="font-weight: 700">{{ target.name }}</td>
                        <td class="single line" rowspan="{{ rowPercent ? 2 : 1 }}" style="font-weight: 700">{{ selected.rows[0].answers[0].title }}</td>
                        <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat-start="answer in selected.columns[0].answers">
                            {{ crosstable[id][answer.value][selected.rows[0].answers[0].value] || 0 }}
                        </td>
                        <td class="right aligned" ng-repeat-end ng-if="totalPercent">
                            {{ getTotalPercent(getCrossTotal(id),crosstable[id][answer.value][selected.rows[0].answers[0].value]  || 0) | number : 2 }}%
                        </td>
                        <td class="right aligned" ng-class="{disabled: target.loading}">
                            {{ getCrossRowTotal(id,selected.rows[0].answers[0].value) }}
                        </td>
                        <td class="right aligned" ng-if="totalPercent">
                            {{ getTotalPercent(getCrossTotal(id),getCrossRowTotal(id,selected.rows[0].answers[0].value)  || 0) | number : 2 }}%
                        </td>
                        <td class="right aligned" ng-if="meanSet">
                            {{getCrossRowMean(id,selected.rows[0].answers[0].value) | number : 2 }}
                        </td>
                    </tr>

                    <!-- row first all percent -->
                    <tr ng-if="rowPercent">
                        <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat="column_answer in selected.columns[0].answers">
                            {{ getTotalPercent(getCrossRowTotal(id,selected.rows[0].answers[0].value), crosstable[id][column_answer.value][selected.rows[0].answers[0].value] || 0) | number : 2 }}%
                        </td>
                        <td class="right aligned">100%</td>
                    </tr>

                    <tr ng-repeat-start="(key, row_answer) in selected.rows[0].answers" ng-if="key!=0">
                        <td class="single line" rowspan="{{ rowPercent ? 2 : 1 }}" style="font-weight: 700">{{ row_answer.title }}</td>
                        <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat-start="column_answer in selected.columns[0].answers">
                            {{ crosstable[id][column_answer.value][row_answer.value] || 0 }}
                        </td>
                        <!-- columns percent -->
                        <td class="right aligned" ng-repeat-end ng-if="totalPercent">
                            {{ getTotalPercent(getCrossTotal(id),crosstable[id][column_answer.value][row_answer.value] || 0) | number : 2 }}%
                        </td>

                        <td class="right aligned" ng-class="{disabled: target.loading}">{{getCrossRowTotal(id,row_answer.value)}}</td>

                        <!-- columns total percent -->
                        <td class="right aligned" ng-if="totalPercent">
                            {{ getTotalPercent(getCrossTotal(id),getCrossRowTotal(id,row_answer.value) || 0) | number : 2 }}%
                        </td>
                        <td class="right aligned" ng-if="meanSet">
                            {{getCrossRowMean(id,row_answer.value) | number : 2 }}
                        </td>
                    </tr>

                    <!-- row all percent -->
                    <tr ng-repeat-end ng-if="!$first && rowPercent">
                        <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat="column_answer in selected.columns[0].answers">
                            {{ getTotalPercent(getCrossTotal(id), crosstable[id][column_answer.value][row_answer.value] || 0) | number : 2 }}%
                        </td>
                        <td class="right aligned">100%</td>
                    </tr>

                    <tr >
                        <td class="single line" style="font-weight: 700">總和</td>
                        <td class="right aligned" ng-repeat-start="answer in selected.columns[0].answers">{{ getCrossColumnTotal(id,answer.value) }}</td>
                        <td class="right aligned" ng-repeat-end ng-if="totalPercent"> {{ getTotalPercent(getCrossTotal(id), getCrossColumnTotal(id,answer.value) || 0) | number : 2 }}%</td>
                        <td class="right aligned" colspan="{{1+meanSet}}" rowspan="{{1+meanSet}}">{{ getCrossTotal(id) }} </td>
                        <td class="right aligned" ng-if="totalPercent">100%</td>
                    </tr>
                    <tr ng-repeat-end ng-if="meanSet">
                        <td class="single line" style="font-weight: 700">平均</td>
                        <td class="right aligned" ng-repeat="answer in selected.columns[0].answers">{{ getCrossColumnMean(id,answer.value) | number : 2 }}</td>
                    </tr>
                </tbody>

            </table>

        </div>
    </div>

</div>

<!-- <div class="ui segment">

    <h5 class="ui header">是否加權</h5>

    <div class="ui radio checkbox">
        <input type="radio" id="ext_weight_yes" ng-model="ext_weight" value="1" />
        <label for="ext_weight_yes">是</label>
    </div>

    <div class="ui radio checkbox">
        <input type="radio" id="ext_weight_no" ng-model="ext_weight" value="0" />
        <label for="ext_weight_no">否</label>
    </div>

</div> -->

<!-- <div class="ui segment">
    <h5 class="ui header">勾選要輸出的統計量</h5>
    <div class="ui three column grid">
        <div class="column">
            <div class="ui horizontal segment">
                <h5 class="ui header">集中趨勢</h5>
                <input type="checkbox" name="othervalA" iname="平均數" value="mean" />平均數
                <input type="checkbox" name="othervalA" iname="眾數" value="mode" />眾數<br />
                <input type="checkbox" name="othervalA" iname="中位數" value="median" />中位數
            </div>
        </div>
        <div class="column">
            <div class="ui horizontal segment">
                <h5 class="ui header">分散情形</h5>
                <input type="checkbox" name="othervalA" iname="標準差" value="stdev" />標準差
                <input type="checkbox" name="othervalA" iname="最小值" value="min" />最小值<br />
                <input type="checkbox" name="othervalA" iname="變異數" value="variance" />變異數
                <input type="checkbox" name="othervalA" iname="最大值" value="max" />最大值
            </div>
        </div>
        <div class="column">
            <div class="ui horizontal segment">
                <h5 class="ui header">百分比數值</h5>
                <input type="checkbox" name="othervalA" iname="百分位數值(25%)" value="q1" />25%<br />
                <input type="checkbox" name="othervalA" iname="百分位數值(75%)" value="q3" />75%
            </div>
        </div>
    </div>

    <h5 class="ui header">選擇輸出資料小數點後位數</h5>
    <select name="ext_digit">
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3" selected="selected">3</option>
    </select>
</div> -->
