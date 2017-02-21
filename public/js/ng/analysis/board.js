'use strict';

angular.module('analysis.result', ['analysis.chart'])
.directive('ngBoard', function($http) {
    return {
        restrict: 'E',
        replace: true,
        transclude: false,
        scope: {
            selected: '=',
            targets: '=',
            frequence: '=',
            crosstable: '='
        },
        template: `
<div>

<md-input-container flex>
    <label>{{outputType}}</label>
    <md-select ng-model="reportOutput" ng-change="drawChart()">
        <md-option ng-value="chart.name" ng-disabled="disabledCharts[chart.name]" ng-repeat="chart in charts"><i class="{{ chart.icon }} icon"></i>{{chart.title}}</md-option>
    </md-select>
</md-input-container>

<div class="ui basic segment" ng-class="{loading: loading || counting}">

    <div>
        <div ng-if="report.output=='pie'">
            <div ng-if="!selected.row && selected.column">
                <h4 class="ui header">{{ selected.column.title }}</h4>
            </div>
            <div ng-if="selected.row && !selected.column">
                <h4 class="ui header">{{ selected.row.title }}</h4>
            </div>
        </div>
        <ng-bar-chart ng-if="report.output=='bar' && report.type==''" targets="targets" selected="selected" frequence="frequence"></ng-bar-chart>
        <ng-pie-chart ng-if="report.output=='pie' && report.type==''" targets="targets" selected="selected" frequence="frequence"></ng-pie-chart>
        <ng-cross-bar-chart ng-if="report.output=='bar' && report.type=='cross'" targets="targets" selected="selected" crosstable="crosstable" ></ng-cross-bar-chart>
        <ng-cross-pie-chart ng-if="report.output=='pie' && report.type=='cross'" targets="targets" selected="selected" crosstable="crosstable" ></ng-cross-pie-chart>
    </div>

    <div class="ui bottom attached" style="overflow:auto" ng-if="report.output == 'table' && report.type == 'column'">
        <div style="min-width:500px">
            <table class="ui celled structured table">
                <thead>
                    <tr>
                        <th><button class="ui mini button" ng-click="setMean()"><i class="plus icon"></i>平均數</button></th>
                        <th class="left aligned" colspan="{{ selected.column.answers.length+meanSet+1 }}">{{ selected.column.title }}</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th class="top aligned right aligned" style="min-width: 80px" ng-repeat="answer in selected.column.answers">{{ answer.title }}</th>
                        <th class="top aligned right aligned" style="min-width: 80px">總和</th>
                        <th class="top aligned right aligned" style="min-width: 80px" ng-if="meanSet">平均</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat-start="(id, target) in targets">
                        <td rowspan="2" class="single line">{{ target.name }}</td>
                        <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat="answer in selected.column.answers">
                            {{ frequence[id][answer.value] || 0 }} <br/>
                        </td>
                        <td class="right aligned">{{ getFrequenceTotal(selected.column.answers, id) }}</td>
                        <td ng-if="meanSet" class="right aligned" rowspan="2">{{ getMean(selected.column.answers, id) | number : 2 }}</td>
                    </tr>
                    <tr ng-repeat-end>
                        <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat="answer in selected.column.answers">
                            {{ getTotalPercent(getFrequenceTotal(selected.column.answers, id), frequence[id][answer.value] || 0) | number : 2 }}%
                        </td>
                        <td class="right aligned">100%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div style="overflow:auto" ng-if="report.output == 'table' && report.type == 'row'">
        <div style="min-width:300px">
            <table class="ui celled structured table">
                <tbody>
                    <tr ng-repeat-start="(id, target) in targets">
                        <td rowspan="{{ selected.row.answers.length+meanSet+1 }}" style="background: #f9fafb;text-align: inherit;color: rgba(0,0,0,.87);font-weight: 700"><button class="ui mini button" ng-click="setMean()"><i class="plus icon"></i>平均數</button></td>
                        <td rowspan="{{ selected.row.answers.length+meanSet+1 }}" style="background: #f9fafb;text-align: inherit;color: rgba(0,0,0,.87);font-weight: 700">{{ selected.row.title }}</td>
                        <td rowspan="{{ selected.row.answers.length+meanSet+1 }}" style="font-weight: 700; background-color:#f9fafb">{{ target.name }}</td>
                        <td class="left aligned" style="font-weight: 700">{{ selected.row.answers[0].title }}</td>
                        <td class="right aligned" ng-class="{disabled: target.loading}">
                            {{ frequence[id][selected.row.answers[0].value] || 0 }}
                        </td>
                        <td class="right aligned">
                            {{ getTotalPercent(getFrequenceTotal(selected.row.answers, id), frequence[id][selected.row.answers[0].value] || 0) | number : 2 }}%
                        </td>
                    </tr>
                    <tr ng-repeat="(key, answer) in selected.row.answers" ng-if="key!=0">
                        <td class="left aligned" style="font-weight: 700">{{ answer.title }}</td>
                        <td class="right aligned" ng-class="{disabled: target.loading}">
                            {{ frequence[id][answer.value] || 0 }}
                        </td>
                        <td class="right aligned">
                            {{ getTotalPercent(getFrequenceTotal(selected.row.answers, id), frequence[id][answer.value] || 0) | number : 2 }}%
                        </td>
                    </tr>
                    <tr>
                        <td class="left aligned" style="font-weight: 700">總和</td>
                        <td class="right aligned" >{{ getFrequenceTotal(selected.row.answers, id) }}</td>
                        <td class="right aligned" >100%</td>
                    </tr>
                    <tr ng-if="meanSet" ng-repeat-end>
                        <td class="left aligned" style="font-weight: 700">平均</td>
                        <td class="right aligned" colspan="2">{{ getMean(selected.row.answers, id) | number : 2 }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div style="overflow:auto" ng-if="report.output == 'table' && report.type == 'cross'">
        <div style="width:{{ selected.column.answers.length*120+120 }}px;min-width:600px">

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
                        <th ng-if="!totalPercent" colspan="{{ colPercent ? selected.column.answers.length*2+2 : selected.column.answers.length*1+1+meanSet }}">{{ selected.column.title }}</th>
                        <th ng-if="totalPercent" colspan="{{ selected.column.answers.length*2+2}}">{{ selected.column.title }}</th>
                    </tr>
                    <tr ng-if="!totalPercent">
                        <th colspan="3"></th>
                        <th colspan="{{ colPercent ? 2 : 1 }}" class="top aligned left aligned" ng-repeat="answer in selected.column.answers">{{ answer.title }}</th>
                        <th colspan="{{ colPercent ? 2 : 1 }}">總和</th>
                        <th ng-if="meanSet">平均</th>
                    </tr>
                    <tr ng-if="totalPercent">
                        <th colspan="3"></th>
                        <th colspan="{{ totalPercent ? 2 : 1 }}" class="top aligned left aligned" ng-repeat="answer in selected.column.answers">{{ answer.title }}</th>
                        <th colspan="{{ totalPercent ? 2 : 1 }}">總和</th>
                        <th ng-if="meanSet">平均</th>
                    </tr>
                </thead>
                <tbody ng-if="!totalPercent">
                    <tr ng-repeat-start="(id, target) in targets">
                        <td rowspan="{{ selected.row.answers.length*(rowPercent ? 2 : 1)+1+meanSet }}" style="background: #f9fafb;text-align: inherit;color: rgba(0,0,0,.87);font-weight: 700">
                            {{ selected.row.title }}
                        </td>
                        <td class="single line" rowspan="{{ selected.row.answers.length*(rowPercent ? 2 : 1)+1+meanSet }}" style="font-weight: 700">{{ target.name }}</td>
                        <td class="single line" rowspan="{{ rowPercent ? 2 : 1 }}" style="font-weight: 700">{{ selected.row.answers[0].title }}</td>
                        <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat-start="answer in selected.column.answers">
                            {{ crosstable[id][answer.value][selected.row.answers[0].value] || 0 }}
                        </td>
                        <td class="right aligned" ng-repeat-end ng-if="colPercent">
                            {{ getTotalPercent(getCrossColumnTotal(id,answer.value),crosstable[id][answer.value][selected.row.answers[0].value]  || 0) | number : 2 }}%
                        </td>
                        <td class="right aligned" ng-class="{disabled: target.loading}">
                            {{ getCrossRowTotal(id,selected.row.answers[0].value) }}
                        </td>
                        <td class="right aligned" ng-if="colPercent">
                            {{ getTotalPercent(getCrossTotal(id),getCrossRowTotal(id,selected.row.answers[0].value)  || 0) | number : 2 }}%
                        </td>
                        <td class="right aligned" ng-if="meanSet">
                            {{getCrossRowMean(id,selected.row.answers[0].value) | number : 2 }}
                        </td>
                    </tr>


                    <tr ng-if="rowPercent">
                        <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat="column_answer in selected.column.answers">
                            {{ getTotalPercent(getCrossRowTotal(id,selected.row.answers[0].value), crosstable[id][column_answer.value][selected.row.answers[0].value] || 0) | number : 2 }}%
                        </td>
                        <td class="right aligned">100%</td>
                    </tr>

                    <tr ng-repeat-start="(key, row_answer) in selected.row.answers" ng-if="key!=0">
                        <td class="single line" rowspan="{{ rowPercent ? 2 : 1 }}" style="font-weight: 700">{{ row_answer.title }}</td>
                        <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat-start="column_answer in selected.column.answers">
                            {{ crosstable[id][column_answer.value][row_answer.value] || 0 }}
                        </td>

                        <td class="right aligned" ng-repeat-end ng-if="colPercent">
                            {{ getTotalPercent(getCrossColumnTotal(id,column_answer.value),crosstable[id][column_answer.value][row_answer.value] || 0) | number : 2 }}%
                        </td>

                        <td class="right aligned" ng-class="{disabled: target.loading}">{{getCrossRowTotal(id,row_answer.value)}}</td>


                        <td class="right aligned" ng-if="colPercent">
                            {{ getTotalPercent(getCrossTotal(id),getCrossRowTotal(id,row_answer.value) || 0) | number : 2 }}%
                        </td>
                        <td class="right aligned" ng-if="meanSet">
                            {{getCrossRowMean(id,row_answer.value) | number : 2 }}
                        </td>
                    </tr>


                    <tr ng-repeat-end ng-if="!$first && rowPercent">
                        <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat="column_answer in selected.column.answers">
                            {{ getTotalPercent(getCrossRowTotal(id,row_answer.value), crosstable[id][column_answer.value][row_answer.value] || 0) | number : 2 }}%
                        </td>
                        <td class="right aligned">100%</td>
                    </tr>

                    <tr >
                        <td class="single line" style="font-weight: 700">總和</td>
                        <td class="right aligned" ng-repeat-start="answer in selected.column.answers">{{ getCrossColumnTotal(id,answer.value) }}</td>
                        <td class="right aligned" ng-repeat-end ng-if="colPercent">100%</td>
                        <td class="right aligned" colspan="{{1+meanSet}}" rowspan="{{1+meanSet}}">{{ getCrossTotal(id) }} </td>
                        <td class="right aligned" ng-if="colPercent">100%</td>
                    </tr>
                    <tr ng-repeat-end ng-if="meanSet">
                        <td class="single line" style="font-weight: 700">平均</td>
                        <td class="right aligned" ng-repeat="answer in selected.column.answers">{{ getCrossColumnMean(id,answer.value) | number : 2 }}</td>
                    </tr>
                </tbody>

                <tbody ng-if="totalPercent">
                    <tr ng-repeat-start="(id, target) in targets">
                        <td rowspan="{{ selected.row.answers.length*(rowPercent ? 2 : 1)+1+meanSet }}" style="background: #f9fafb;text-align: inherit;color: rgba(0,0,0,.87);font-weight: 700">
                            {{ selected.row.title }}
                        </td>
                        <td class="single line" rowspan="{{ selected.row.answers.length*(rowPercent ? 2 : 1)+1+meanSet }}" style="font-weight: 700">{{ target.name }}</td>
                        <td class="single line" rowspan="{{ rowPercent ? 2 : 1 }}" style="font-weight: 700">{{ selected.row.answers[0].title }}</td>
                        <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat-start="answer in selected.column.answers">
                            {{ crosstable[id][answer.value][selected.row.answers[0].value] || 0 }}
                        </td>
                        <td class="right aligned" ng-repeat-end ng-if="totalPercent">
                            {{ getTotalPercent(getCrossTotal(id),crosstable[id][answer.value][selected.row.answers[0].value]  || 0) | number : 2 }}%
                        </td>
                        <td class="right aligned" ng-class="{disabled: target.loading}">
                            {{ getCrossRowTotal(id,selected.row.answers[0].value) }}
                        </td>
                        <td class="right aligned" ng-if="totalPercent">
                            {{ getTotalPercent(getCrossTotal(id),getCrossRowTotal(id,selected.row.answers[0].value)  || 0) | number : 2 }}%
                        </td>
                        <td class="right aligned" ng-if="meanSet">
                            {{getCrossRowMean(id,selected.row.answers[0].value) | number : 2 }}
                        </td>
                    </tr>


                    <tr ng-if="rowPercent">
                        <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat="column_answer in selected.column.answers">
                            {{ getTotalPercent(getCrossRowTotal(id,selected.row.answers[0].value), crosstable[id][column_answer.value][selected.row.answers[0].value] || 0) | number : 2 }}%
                        </td>
                        <td class="right aligned">100%</td>
                    </tr>

                    <tr ng-repeat-start="(key, row_answer) in selected.row.answers" ng-if="key!=0">
                        <td class="single line" rowspan="{{ rowPercent ? 2 : 1 }}" style="font-weight: 700">{{ row_answer.title }}</td>
                        <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat-start="column_answer in selected.column.answers">
                            {{ crosstable[id][column_answer.value][row_answer.value] || 0 }}
                        </td>

                        <td class="right aligned" ng-repeat-end ng-if="totalPercent">
                            {{ getTotalPercent(getCrossTotal(id),crosstable[id][column_answer.value][row_answer.value] || 0) | number : 2 }}%
                        </td>

                        <td class="right aligned" ng-class="{disabled: target.loading}">{{getCrossRowTotal(id,row_answer.value)}}</td>


                        <td class="right aligned" ng-if="totalPercent">
                            {{ getTotalPercent(getCrossTotal(id),getCrossRowTotal(id,row_answer.value) || 0) | number : 2 }}%
                        </td>
                        <td class="right aligned" ng-if="meanSet">
                            {{getCrossRowMean(id,row_answer.value) | number : 2 }}
                        </td>
                    </tr>


                    <tr ng-repeat-end ng-if="!$first && rowPercent">
                        <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat="column_answer in selected.column.answers">
                            {{ getTotalPercent(getCrossTotal(id), crosstable[id][column_answer.value][row_answer.value] || 0) | number : 2 }}%
                        </td>
                        <td class="right aligned">100%</td>
                    </tr>

                    <tr >
                        <td class="single line" style="font-weight: 700">總和</td>
                        <td class="right aligned" ng-repeat-start="answer in selected.column.answers">{{ getCrossColumnTotal(id,answer.value) }}</td>
                        <td class="right aligned" ng-repeat-end ng-if="totalPercent"> {{ getTotalPercent(getCrossTotal(id), getCrossColumnTotal(id,answer.value) || 0) | number : 2 }}%</td>
                        <td class="right aligned" colspan="{{1+meanSet}}" rowspan="{{1+meanSet}}">{{ getCrossTotal(id) }} </td>
                        <td class="right aligned" ng-if="totalPercent">100%</td>
                    </tr>
                    <tr ng-repeat-end ng-if="meanSet">
                        <td class="single line" style="font-weight: 700">平均</td>
                        <td class="right aligned" ng-repeat="answer in selected.column.answers">{{ getCrossColumnMean(id,answer.value) | number : 2 }}</td>
                    </tr>
                </tbody>

            </table>

        </div>
    </div>

</div>

</div>
        `,
        link: function(scope) {
            scope.report = {output: 'table', type: 'row'};
            scope.outputType = '輸出樣式:表格(預設)';
            scope.reportOutput = 'table';
            scope.colPercent = false;
            scope.rowPercent = false;
            scope.totalPercent = false;
            scope.meanSet = 0;
            scope.tableOption = '個數';
            scope.tableOptions = ('行% 列% 總和% 平均數 個數').split(' ').map(function (eachOption) { return { abbrev: eachOption }; });
            scope.charts = [{title: '表格', name: 'table', icon: 'table'}, {title: '長條圖', name: 'bar', icon: 'bar chart'}, {title: '圓餅圖', name: 'pie', icon: 'pie chart'}];

            scope.drawChart = function() {
                scope.report.output = angular.copy(scope.reportOutput);
                scope.disabledCharts = {bar: false, pie:  false};
                if (scope.report.output == 'table') {
                    if (scope.selected.column && !scope.selected.row)
                        scope.report.type = 'column';
                    if (!scope.selected.column && scope.selected.row)
                        scope.report.type = 'row';
                    if (scope.selected.column && scope.selected.row)
                        scope.report.type = 'cross';
                }
                if (scope.report.output == 'bar') {
                    if (!scope.selected.column || !scope.selected.row)
                        scope.report.type = '';
                    if (scope.selected.column && scope.selected.row)
                        scope.report.type = 'cross';
                }
                if (scope.report.output == 'pie') {
                    if (!scope.selected.column || !scope.selected.row)
                        scope.report.type = '';
                    if (scope.selected.column && scope.selected.row)
                        scope.report.type = 'cross';
                }
            };

            scope.drawChart();


            // scope.$watch('cross_percent', function(val) {
            //     scope.colPercent = val == 'col';
            // });

            scope.getTotalPercent = function(total, value) {
                return total == 0 ? 0 : value*100/total;
            }

            scope.getFrequenceTotal = function(answers, id) {
                if (!scope.frequence[id]) {
                    return 0;
                }

                var total = 0;
                for(var i in answers) {
                    total += scope.frequence[id][answers[i].value]*1 || 0;
                }
                return total;
            }

            scope.getCrossTotal = function(key) {
                if (scope.crosstable[key] == null ) {
                    return 0;
                }
                var sum_col_row = 0;
                var crosstable = scope.crosstable[key];
                var column_answers = scope.selected.column.answers;
                var colum_total = [];

                for(var i in column_answers) {
                    var column_key = column_answers[i].value;
                    var temp_total = 0;
                    for(var j in scope.selected.row.answers) {
                        var value = scope.selected.row.answers[j].value;
                        var amount = crosstable[column_key][value]*1 || 0;
                        temp_total = temp_total+amount;
                    }
                    colum_total[i] = temp_total;
                }

                for(var i in column_answers) {
                    sum_col_row = sum_col_row+colum_total[i];
                }
                return sum_col_row;
            }

            scope.getCrossColumnTotal = function(id, key){
                if (!scope.crosstable[id]) {
                    return 0;
                }
                var sum = 0;
                var crosstable = scope.crosstable[id];

                if(crosstable[key]==null){crosstable[key]=[]};
                for(var i in scope.selected.row.answers) {
                    var value = scope.selected.row.answers[i].value;
                    var amount = crosstable[key][value]*1 || 0;
                    sum += amount;
                }
                return sum;
            }

            scope.getCrossRowTotal = function(id, key){
                console.log(scope.crosstable);
                if (!scope.crosstable[id]) {
                    return 0;
                }
                var sum = 0;
                var crosstable = scope.crosstable[id];

                for(var i in scope.selected.column.answers) {
                    var value = scope.selected.column.answers[i].value;
                    if(crosstable[value]==null){crosstable[value]=[]};
                    var amount = crosstable[value][key]*1 || 0;
                    sum += amount;
                }
                return sum;
            }

            scope.setRowPercent = function() {
                scope.colPercent = false;
                scope.rowPercent = true;
                scope.totalPercent = false;
                scope.meanSet = 0;
            };

            scope.setColPercent = function() {
                scope.colPercent = true;
                scope.rowPercent = false;
                scope.totalPercent = false;
                scope.meanSet = 0;
            };

            scope.setTotalPercent = function(){
                scope.colPercent = false;
                scope.rowPercent = false;
                scope.totalPercent = true;
                scope.meanSet = 0;
            };

            scope.setMean = function() {
                if(!scope.meanSet){
                    scope.colPercent = false;
                    scope.rowPercent = false;
                    scope.totalPercent = false;
                    scope.meanSet = 1;
                }else{
                    scope.colPercent = false;
                    scope.rowPercent = false;
                    scope.totalPercent = false;
                    scope.meanSet = 0;
                }
            };

            scope.setNoPercent = function() {
                scope.colPercent = false;
                scope.rowPercent = false;
                scope.meanSet = 0;
            };

            scope.showPercent = function(mode){
                if (mode == '行%')
                    scope.setColPercent();
                if (mode == '列%')
                    scope.setRowPercent();
                if (mode == '總和%')
                    scope.setTotalPercent();
                if (mode == '平均數')
                    scope.setMean();
                if (mode == '個數')
                    scope.setNoPercent();
            };

            scope.getMean = function(answers,id){
                if (!scope.frequence[id]) {
                    return 0;
                }
                var totalValue = 0;
                var totalAmount = 0;
                for(var i in answers) {
                    totalValue += scope.frequence[id][answers[i].value]*answers[i].value*1 || 0;
                    totalAmount += scope.frequence[id][answers[i].value]*1 || 0;
                }

                var mean = totalValue/totalAmount;
                return mean;
            };

            scope.getCrossColumnMean = function(id, key){
                if (!scope.crosstable[id]) {
                    return 0;
                }
                var totalValue = 0;
                var totalAmount = 0;
                var crosstable = scope.crosstable[id];

                if(crosstable[key]==null){crosstable[key]=[]};
                for(var i in scope.selected.row.answers) {
                    var value = scope.selected.row.answers[i].value;
                    var amount = crosstable[key][value]*1 || 0;
                    totalValue += amount*value;
                    totalAmount += amount;
                }
                return totalValue/totalAmount;
            }

            scope.getCrossRowMean = function(id, key){
                if (!scope.crosstable[id]) {
                    return 0;
                }
                var totalValue = 0;
                var totalAmount = 0;
                var crosstable = scope.crosstable[id];

                for(var i in scope.selected.column.answers) {
                    var value = scope.selected.column.answers[i].value;
                    if(crosstable[value]==null){crosstable[value]=[]};
                    var amount = crosstable[value][key]*1 || 0;
                    totalValue += amount*value;
                    totalAmount += amount;
                }
                return totalValue/totalAmount;
            };

        }
    };
});