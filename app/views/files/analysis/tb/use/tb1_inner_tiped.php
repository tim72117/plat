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

<div class="ui basic segment" ng-class="{loading: loading || counting}">

    <div>
        <div ng-if="result == 'bar' || result == 'pie'">
            <div ng-if="selected.rows.length == 0 && selected.columns.length > 0">
                <h4 class="ui header" ng-repeat="column in selected.columns">{{ column.title }}</h4>
            </div>
            <div ng-if="selected.rows.length > 0 && selected.columns.length == 0">
                <h4 class="ui header" ng-repeat="row in selected.rows">{{ selected.rows[0].title }}</h4>
            </div>

        </div>
        <div ng-if="result == 'bar'" id="bar-container"></div>
        <div ng-if="result == 'pie'" id="pie-container"></div>
    </div>

    <div style="overflow:auto" ng-if="result == 'table' && selected.rows.length == 0 && selected.columns.length > 0">
        <div style="width:{{ selected.columns[0].answers.length*120+120 }}px;min-width:500px">
            <table class="ui table">
                <thead>
                    <tr>
                        <th style="min-width:150px"></th>
                        <th class="left aligned" colspan="{{ selected.columns[0].answers.length+1 }}" ng-repeat="column in selected.columns" >{{ column.title }}</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th class="top aligned left aligned" style="min-width:250px" ng-repeat="answer in selected.columns[0].answers">{{ answer.value }}({{ answer.title }})</th>
                        <th class="top aligned left aligned" style="min-width:250px" ng-show="selected.columns.length > 0">總和</th>
                    </tr>

                </thead>
                <tbody ng-repeat="(id, target) in targetsSelected()">
                    <tr>
                        <td>{{ target.name }}</td>
                        <td class="left aligned collapsing" ng-class="{disabled: target.loading}" ng-repeat="answer in selected.columns[0].answers">
                            {{ frequence[id][answer.value] ? frequence[id][answer.value] : 0 }}  ({{ getTotalPercent(frequence[id][answer.value],id) | number : 2 }}%)

                        </td>
                        <td ng-show="selected.columns.length > 0">{{ getRowTotal(id) }}(100%)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div style="overflow:auto" ng-if="result == 'table' && selected.columns.length == 0 && selected.rows.length > 0">
        <div style="min-width:600px">
            <table class="ui table">
                <thead >
                    <tr>
                        <th class="top aligned center aligned" colspan="3" ng-repeat="row in selected.rows" >{{ selected.rows[0].title }}</th>

                    </tr>
                </thead>
                <tbody ng-repeat="(id, target) in targetsSelected()">
                    <tr>
                        <td rowspan="{{ selected.rows[0].answers.length+2 }}" style="background: #f9fafb;text-align: inherit;color: rgba(0,0,0,.87);font-weight: 700;width:50px">{{ target.name }}</td>
                    </tr>
                    <tr ng-repeat="answer in selected.rows[0].answers">

                        <td class="right aligned collapsing" style="background: #f9fafb;text-align: inherit;color: rgba(0,0,0,.87);font-weight: 700">{{ answer.title }}</td>
                        <td class="left aligned collapsing" ng-class="{disabled: target.loading}">
                            {{ frequence[id][answer.value] ? frequence[id][answer.value] : 0 }}  ({{ getTotalPercent(frequence[id][answer.value],id) | number : 2 }}%)

                        </td>

                    </tr>
                    <tr ng-show="selected.rows.length > 0">

                        <td class="right aligned collapsing" style="background: #f9fafb;text-align: inherit;color: rgba(0,0,0,.87);font-weight: 700">總和</td>
                        <td class="left aligned collapsing" >{{ getColumnTotal(id) }}(100%)</td>

                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div style="overflow:auto" ng-if="result == 'table' && selected.rows.length > 0 && selected.columns.length > 0">
        <div style="width:{{ selected.columns[0].answers.length*120+120 }}px;min-width:600px">

            <table class="ui table">
                <thead>
                    <tr>
                        <th style="min-width:150px"></th>
                        <th ng-if="selected.rows.length > 0"></th>
                        <th colspan="{{ column.answers.length+1 }}" ng-repeat="column in selected.columns" >{{ column.title }}</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th ng-if="selected.rows.length > 0"></th>
                        <th class="left aligned" ng-repeat="answer in selected.columns[0].answers">{{ answer.title }}({{ answer.value }})</th>

                    </tr>
                </thead>
                <tbody ng-repeat="(id, target) in targetsSelected()">
                    <tr>
                        <td rowspan="{{ selected.rows[0].answers.length+1 }}" style="background: #f9fafb;text-align: inherit;color: rgba(0,0,0,.87);font-weight: 700">{{ target.name }}</td>
                        <td class="left aligned collapsing" style="background: #f9fafb;text-align: inherit;color: rgba(0,0,0,.87);font-weight: 700">{{ selected.rows[0].answers[0].title }}</td>
                        <td class="left aligned" ng-class="{disabled: target.loading}" ng-repeat="answer in selected.columns[0].answers">
                            {{ crosstable[id][answer.value][selected.rows[0].answers[0].value] ? crosstable[id][answer.value][selected.rows[0].answers[0].value] : 0 }}
                        </td>

                    </tr>
                    <tr ng-repeat="(key, row_answer) in selected.rows[0].answers" ng-if="key != 0">
                        <td class="left aligned collapsing" style="background: #f9fafb;text-align: inherit;color: rgba(0,0,0,.87);font-weight: 700">{{ row_answer.title }}</td>
                        <td class="left aligned" ng-class="{disabled: target.loading}" ng-repeat="column_answer in selected.columns[0].answers">
                            {{ crosstable[id][column_answer.value][row_answer.value] ? crosstable[id][column_answer.value][row_answer.value] : 0 }}
                        </td>

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
