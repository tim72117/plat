'use strict';

angular.module('analysis.table', [])
.directive('ngColumnTable', function($http, analysisMethod) {
    return {
        restrict: 'E',
        replace: true,
        transclude: false,
        scope: {
            selected: '=',
            targets: '=',
            frequence: '='
        },
        template: `
            <table class="ui celled structured table">
                <thead>
                    <tr>
                        <th><button class="ui mini button" ng-click="setMean(set)"><i class="plus icon"></i>平均數</button></th>
                        <th class="left aligned" colspan="{{ selected.column.answers.length+set.meanSet+1 }}">{{ selected.column.title }}</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th class="top aligned right aligned" style="min-width: 80px" ng-repeat="answer in selected.column.answers">{{ answer.title }}</th>
                        <th class="top aligned right aligned" style="min-width: 80px">總和</th>
                        <th class="top aligned right aligned" style="min-width: 80px" ng-if="set.meanSet">平均</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat-start="(id, target) in targets">
                        <td rowspan="2" class="single line">{{ target.name }}</td>
                        <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat="answer in selected.column.answers">
                            {{ frequence[id][answer.value] || 0 }} <br/>
                        </td>
                        <td class="right aligned">{{ getFrequenceTotal(selected.column.answers, frequence[id]) }}</td>
                        <td ng-if="set.meanSet" class="right aligned" rowspan="2">{{ getMean(selected.column.answers, frequence[id]) | number : 2 }}</td>
                    </tr>
                    <tr ng-repeat-end>
                        <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat="answer in selected.column.answers">
                            {{ getTotalPercent(getFrequenceTotal(selected.column.answers, frequence[id]), frequence[id][answer.value] || 0) | number : 2 }}%
                        </td>
                        <td class="right aligned">100%</td>
                    </tr>
                </tbody>
            </table>
        `,
        link: function(scope, element) {
            scope.set = {
                colPercent: false,
                rowPercent: false,
                totalPercent: false,
                meanSet: 0,
            };
            scope.getMean = analysisMethod.getMean;
            scope.setMean = analysisMethod.setMean;
            scope.getFrequenceTotal = analysisMethod.getFrequenceTotal;
            scope.getTotalPercent = analysisMethod.getTotalPercent;
        }
    };
})

.directive('ngRowTable', function($http, analysisMethod) {
    return {
        restrict: 'E',
        replace: true,
        transclude: false,
        scope: {
            selected: '=',
            targets: '=',
            frequence: '='
        },
        template: `
            <div>
                <table class="ui celled structured table">
                    <tbody>
                        <tr ng-repeat-start="(id, target) in targets">
                            <td rowspan="{{ selected.row.answers.length+set.meanSet+1 }}" style="background: #f9fafb;text-align: inherit;color: rgba(0,0,0,.87);font-weight: 700">
                                <button class="ui mini button" ng-click="setMean(set)"><i class="plus icon"></i>平均數</button>
                            </td>
                            <td rowspan="{{ selected.row.answers.length+set.meanSet+1 }}" style="background: #f9fafb;text-align: inherit;color: rgba(0,0,0,.87);font-weight: 700">{{ selected.row.title }}</td>
                            <td rowspan="{{ selected.row.answers.length+set.meanSet+1 }}" style="font-weight: 700; background-color:#f9fafb">{{ target.name }}</td>
                            <td class="left aligned" style="font-weight: 700">{{ selected.row.answers[0].title }}</td>
                            <td class="right aligned" ng-class="{disabled: target.loading}">
                                {{ frequence[id][selected.row.answers[0].value] || 0 }}
                            </td>
                            <td class="right aligned">
                                {{ getTotalPercent(getFrequenceTotal(selected.row.answers, frequence[id]), frequence[id][selected.row.answers[0].value] || 0) | number : 2 }}%
                            </td>
                        </tr>
                        <tr ng-repeat="(key, answer) in selected.row.answers" ng-if="key!=0">
                            <td class="left aligned" style="font-weight: 700">{{ answer.title }}</td>
                            <td class="right aligned" ng-class="{disabled: target.loading}">
                                {{ frequence[id][answer.value] || 0 }}
                            </td>
                            <td class="right aligned">
                                {{ getTotalPercent(getFrequenceTotal(selected.row.answers, frequence[id]), frequence[id][answer.value] || 0) | number : 2 }}%
                            </td>
                        </tr>
                        <tr>
                            <td class="left aligned" style="font-weight: 700">總和</td>
                            <td class="right aligned" >{{ getFrequenceTotal(selected.row.answers, frequence[id]) }}</td>
                            <td class="right aligned" >100%</td>
                        </tr>
                        <tr ng-if="set.meanSet" ng-repeat-end>
                            <td class="left aligned" style="font-weight: 700">平均</td>
                            <td class="right aligned" colspan="2">{{ getMean(selected.row.answers, frequence[id]) | number : 2 }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        `,
        link: function(scope, element) {
            scope.set = {
                colPercent: false,
                rowPercent: false,
                totalPercent: false,
                meanSet: 0,
            };
            scope.getMean = analysisMethod.getMean;
            scope.setMean = analysisMethod.setMean;
            scope.getFrequenceTotal = analysisMethod.getFrequenceTotal;
            scope.getTotalPercent = analysisMethod.getTotalPercent;
        }
    };
})

.directive('ngCrossTable', function($http, analysisMethod) {
    return {
        restrict: 'E',
        replace: true,
        transclude: false,
        scope: {
            selected: '=',
            targets: '=',
            crosstable: '='
        },
        template: `
            <div>
                <md-input-container>
                    <md-select ng-model="tableOption" aria-label="加入">
                        <md-option ng-repeat="option in tableOptions" ng-value="option.set">{{option.title}}</md-option>
                    </md-select>
                </md-input-container>
                <ng-cross-count-table ng-if="tableOption.count"></ng-cross-count-table>
                <ng-cross-total-percent-table ng-if="tableOption.totalPercent"></ng-cross-total-percent-table>
                <ng-cross-row-percent-table ng-if="tableOption.rowPercent"></ng-cross-row-percent-table>
                <ng-cross-col-percent-table ng-if="tableOption.colPercent"></ng-cross-col-percent-table>
                <ng-cross-mean-table ng-if="tableOption.meanSet"></ng-cross-mean-table>
            </div>
        `,
        link: function(scope, element) {
            scope.tableOptions = [
                {title: '個數', set: {colPercent: false, rowPercent: false, totalPercent: false, meanSet: 0, count: true}},
                {title: '行%', set: {colPercent: true, rowPercent: false, totalPercent: false, meanSet: 0}},
                {title: '列%', set: {colPercent: false, rowPercent: true, totalPercent: false, meanSet: 0}},
                {title: '總和%', set: {colPercent: false, rowPercent: false, totalPercent: true, meanSet: 0}},
                {title: '平均數', set: {colPercent: false, rowPercent: false, totalPercent: false, meanSet: 1}},
            ];

            scope.tableOption = scope.tableOptions[0].set;

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

            scope.getTotalPercent = analysisMethod.getTotalPercent;
        }
    };
})

.directive('ngCrossCountTable', function($http) {
    return {
        restrict: 'E',
        replace: true,
        transclude: false,
        scope: true,
        template: `
            <div style="width:{{ selected.column.answers.length*120+120 }}px;min-width:600px">
                <table class="ui celled structured table">
                    <thead>
                        <tr>
                            <th style="min-width:150px" colspan="3"></th>
                            <th colspan="{{ selected.column.answers.length+1 }}">{{ selected.column.title }}</th>
                        </tr>
                        <tr>
                            <th colspan="3"></th>
                            <th class="top aligned left aligned" ng-repeat="answer in selected.column.answers">{{ answer.title }}</th>
                            <th>總和</th>
                        </tr>
                    </thead>
                    <tbody ng-repeat="(id, target) in targets">
                        <tr>
                            <td rowspan="{{ selected.row.answers.length+1 }}" style="background: #f9fafb;text-align: inherit;color: rgba(0,0,0,.87);font-weight: 700">
                                {{ selected.row.title }}
                            </td>
                            <td class="single line" rowspan="{{ selected.row.answers.length+1 }}" style="font-weight: 700">{{ target.name }}</td>
                            <td class="single line" style="font-weight: 700">{{ selected.row.answers[0].title }}</td>
                            <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat="answer in selected.column.answers">
                                {{ crosstable[id][answer.value][selected.row.answers[0].value] || 0 }}
                            </td>
                            <td class="right aligned" ng-class="{disabled: target.loading}">
                                {{ getCrossRowTotal(id,selected.row.answers[0].value) }}
                            </td>
                        </tr>
                        <tr ng-repeat="(key, row_answer) in selected.row.answers" ng-if="!$first">
                            <td class="single line" style="font-weight: 700">{{ row_answer.title }}</td>
                            <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat="column_answer in selected.column.answers">
                                {{ crosstable[id][column_answer.value][row_answer.value] || 0 }}
                            </td>
                            <td class="right aligned" ng-class="{disabled: target.loading}">{{getCrossRowTotal(id,row_answer.value)}}</td>
                        </tr>
                        <tr >
                            <td class="single line" style="font-weight: 700">總和</td>
                            <td class="right aligned" ng-repeat="answer in selected.column.answers">{{ getCrossColumnTotal(id,answer.value) }}</td>
                            <td class="right aligned">{{ getCrossTotal(id) }} </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        `,
    };
})

.directive('ngCrossMeanTable', function($http) {
    return {
        restrict: 'E',
        replace: true,
        transclude: false,
        scope: true,
        template: `
            <div style="width:{{ selected.column.answers.length*120+120 }}px;min-width:600px">
                <table class="ui celled structured table">
                    <thead>
                        <tr>
                            <th style="min-width:150px" colspan="3"></th>
                            <th colspan="{{ selected.column.answers.length+2 }}">{{ selected.column.title }}</th>
                        </tr>
                        <tr>
                            <th colspan="3"></th>
                            <th class="top aligned left aligned" ng-repeat="answer in selected.column.answers">{{ answer.title }}</th>
                            <th>總和</th>
                            <th>平均</th>
                        </tr>
                    </thead>
                    <tbody ng-repeat="(id, target) in targets">
                        <tr>
                            <td rowspan="{{ selected.row.answers.length+2 }}" style="background: #f9fafb;text-align: inherit;color: rgba(0,0,0,.87);font-weight: 700">
                                {{ selected.row.title }}
                            </td>
                            <td class="single line" rowspan="{{ selected.row.answers.length+2 }}" style="font-weight: 700">{{ target.name }}</td>
                            <td class="single line" style="font-weight: 700">{{ selected.row.answers[0].title }}</td>
                            <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat="answer in selected.column.answers">
                                {{ crosstable[id][answer.value][selected.row.answers[0].value] || 0 }}
                            </td>
                            <td class="right aligned" ng-class="{disabled: target.loading}">
                                {{ getCrossRowTotal(id,selected.row.answers[0].value) }}
                            </td>
                            <td class="right aligned">
                                {{getCrossRowMean(id,selected.row.answers[0].value) | number : 2 }}
                            </td>
                        </tr>
                        <tr ng-repeat="(key, row_answer) in selected.row.answers" ng-if="!$first">
                            <td class="single line" style="font-weight: 700">{{ row_answer.title }}</td>
                            <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat="column_answer in selected.column.answers">
                                {{ crosstable[id][column_answer.value][row_answer.value] || 0 }}
                            </td>
                            <td class="right aligned" ng-class="{disabled: target.loading}">{{getCrossRowTotal(id,row_answer.value)}}</td>
                            <td class="right aligned">
                                {{getCrossRowMean(id,row_answer.value) | number : 2 }}
                            </td>
                        </tr>
                        <tr >
                            <td class="single line" style="font-weight: 700">總和</td>
                            <td class="right aligned" ng-repeat="answer in selected.column.answers">{{ getCrossColumnTotal(id,answer.value) }}</td>
                            <td class="right aligned" colspan="2" rowspan="2">{{ getCrossTotal(id) }} </td>
                        </tr>
                        <tr>
                            <td class="single line" style="font-weight: 700">平均</td>
                            <td class="right aligned" ng-repeat="answer in selected.column.answers">{{ getCrossColumnMean(id,answer.value) | number : 2 }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        `,
    };
})

.directive('ngCrossColPercentTable', function($http) {
    return {
        restrict: 'E',
        replace: true,
        transclude: false,
        scope: true,
        template: `
            <div style="width:{{ selected.column.answers.length*120+120 }}px;min-width:600px">
                <table class="ui celled structured table">
                    <thead>
                        <tr>
                            <th style="min-width:150px" colspan="3"></th>
                            <th colspan="{{ selected.column.answers.length*2+2 }}">{{ selected.column.title }}</th>
                        </tr>
                        <tr>
                            <th colspan="3"></th>
                            <th colspan="2" class="top aligned left aligned" ng-repeat="answer in selected.column.answers">{{ answer.title }}</th>
                            <th colspan="2">總和</th>
                        </tr>
                    </thead>
                    <tbody ng-repeat="(id, target) in targets">
                        <tr>
                            <td rowspan="{{ selected.row.answers.length+1 }}" style="background: #f9fafb;text-align: inherit;color: rgba(0,0,0,.87);font-weight: 700">
                                {{ selected.row.title }}
                            </td>
                            <td class="single line" rowspan="{{ selected.row.answers.length+1 }}" style="font-weight: 700">{{ target.name }}</td>
                            <td class="single line" style="font-weight: 700">{{ selected.row.answers[0].title }}</td>
                            <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat-start="answer in selected.column.answers">
                                {{ crosstable[id][answer.value][selected.row.answers[0].value] || 0 }}
                            </td>
                            <td class="right aligned" ng-repeat-end>
                                {{ getTotalPercent(getCrossColumnTotal(id,answer.value),crosstable[id][answer.value][selected.row.answers[0].value]  || 0) | number : 2 }}%
                            </td>
                            <td class="right aligned" ng-class="{disabled: target.loading}">
                                {{ getCrossRowTotal(id,selected.row.answers[0].value) }}
                            </td>
                            <td class="right aligned">
                                {{ getTotalPercent(getCrossTotal(id),getCrossRowTotal(id,selected.row.answers[0].value)  || 0) | number : 2 }}%
                            </td>
                        </tr>
                        <tr ng-repeat="(key, row_answer) in selected.row.answers" ng-if="!$first">
                            <td class="single line" style="font-weight: 700">{{ row_answer.title }}</td>
                            <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat-start="column_answer in selected.column.answers">
                                {{ crosstable[id][column_answer.value][row_answer.value] || 0 }}
                            </td>
                            <td class="right aligned" ng-repeat-end>
                                {{ getTotalPercent(getCrossColumnTotal(id,column_answer.value),crosstable[id][column_answer.value][row_answer.value] || 0) | number : 2 }}%
                            </td>
                            <td class="right aligned" ng-class="{disabled: target.loading}">{{getCrossRowTotal(id,row_answer.value)}}</td>
                            <td class="right aligned">
                                {{ getTotalPercent(getCrossTotal(id),getCrossRowTotal(id,row_answer.value) || 0) | number : 2 }}%
                            </td>
                        </tr>
                        <tr >
                            <td class="single line" style="font-weight: 700">總和</td>
                            <td class="right aligned" ng-repeat-start="answer in selected.column.answers">{{ getCrossColumnTotal(id,answer.value) }}</td>
                            <td class="right aligned" ng-repeat-end>100%</td>
                            <td class="right aligned">{{ getCrossTotal(id) }} </td>
                            <td class="right aligned">100%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        `,
    };
})

.directive('ngCrossRowPercentTable', function($http) {
    return {
        restrict: 'E',
        replace: true,
        transclude: false,
        scope: true,
        template: `
            <div style="width:{{ selected.column.answers.length*120+120 }}px;min-width:600px">
                <table class="ui celled structured table">
                    <thead>
                        <tr>
                            <th style="min-width:150px" colspan="3"></th>
                            <th colspan="{{ selected.column.answers.length+1 }}">{{ selected.column.title }}</th>
                        </tr>
                        <tr>
                            <th colspan="3"></th>
                            <th class="top aligned left aligned" ng-repeat="answer in selected.column.answers">{{ answer.title }}</th>
                            <th>總和</th>
                        </tr>
                    </thead>
                    <tbody ng-repeat="(id, target) in targets">
                        <tr>
                            <td rowspan="{{ selected.row.answers.length*2+1 }}" style="background: #f9fafb;text-align: inherit;color: rgba(0,0,0,.87);font-weight: 700">
                                {{ selected.row.title }}
                            </td>
                            <td class="single line" rowspan="{{ selected.row.answers.length*2+1 }}" style="font-weight: 700">{{ target.name }}</td>
                            <td class="single line" rowspan="2" style="font-weight: 700">{{ selected.row.answers[0].title }}</td>
                            <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat="answer in selected.column.answers">
                                {{ crosstable[id][answer.value][selected.row.answers[0].value] || 0 }}
                            </td>
                            <td class="right aligned" ng-class="{disabled: target.loading}">
                                {{ getCrossRowTotal(id,selected.row.answers[0].value) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat="column_answer in selected.column.answers">
                                {{ getTotalPercent(getCrossRowTotal(id,selected.row.answers[0].value), crosstable[id][column_answer.value][selected.row.answers[0].value] || 0) | number : 2 }}%
                            </td>
                            <td class="right aligned">100%</td>
                        </tr>
                        <tr ng-repeat-start="(key, row_answer) in selected.row.answers" ng-if="!$first">
                            <td class="single line" rowspan="2" style="font-weight: 700">{{ row_answer.title }}</td>
                            <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat="column_answer in selected.column.answers">
                                {{ crosstable[id][column_answer.value][row_answer.value] || 0 }}
                            </td>
                            <td class="right aligned" ng-class="{disabled: target.loading}">{{getCrossRowTotal(id,row_answer.value)}}</td>
                        </tr>
                        <tr ng-repeat-end ng-if="!$first">
                            <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat="column_answer in selected.column.answers">
                                {{ getTotalPercent(getCrossRowTotal(id,row_answer.value), crosstable[id][column_answer.value][row_answer.value] || 0) | number : 2 }}%
                            </td>
                            <td class="right aligned">100%</td>
                        </tr>
                        <tr>
                            <td class="single line" style="font-weight: 700">總和</td>
                            <td class="right aligned" ng-repeat="answer in selected.column.answers">{{ getCrossColumnTotal(id,answer.value) }}</td>
                            <td class="right aligned">{{ getCrossTotal(id) }} </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        `,
    };
})

.directive('ngCrossTotalPercentTable', function($http) {
    return {
        restrict: 'E',
        replace: true,
        transclude: false,
        scope: true,
        template: `
            <div style="width:{{ selected.column.answers.length*120+120 }}px;min-width:600px">
                <table class="ui celled structured table">
                    <thead>
                        <tr>
                            <th style="min-width:150px" colspan="3"></th>
                            <th colspan="{{ selected.column.answers.length*2+2}}">{{ selected.column.title }}</th>
                        </tr>
                        <tr>
                            <th colspan="3"></th>
                            <th colspan="2" class="top aligned left aligned" ng-repeat="answer in selected.column.answers">{{ answer.title }}</th>
                            <th colspan="2">總和</th>
                        </tr>
                    </thead>
                    <tbody ng-repeat="(id, target) in targets">
                        <tr>
                            <td rowspan="{{ selected.row.answers.length+1 }}" style="background: #f9fafb;text-align: inherit;color: rgba(0,0,0,.87);font-weight: 700">
                                {{ selected.row.title }}
                            </td>
                            <td class="single line" rowspan="{{ selected.row.answers.length+1 }}" style="font-weight: 700">{{ target.name }}</td>
                            <td class="single line" style="font-weight: 700">{{ selected.row.answers[0].title }}</td>
                            <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat-start="answer in selected.column.answers">
                                {{ crosstable[id][answer.value][selected.row.answers[0].value] || 0 }}
                            </td>
                            <td class="right aligned" ng-repeat-end>
                                {{ getTotalPercent(getCrossTotal(id),crosstable[id][answer.value][selected.row.answers[0].value]  || 0) | number : 2 }}%
                            </td>
                            <td class="right aligned" ng-class="{disabled: target.loading}">
                                {{ getCrossRowTotal(id,selected.row.answers[0].value) }}
                            </td>
                            <td class="right aligned">
                                {{ getTotalPercent(getCrossTotal(id),getCrossRowTotal(id,selected.row.answers[0].value)  || 0) | number : 2 }}%
                            </td>
                        </tr>
                        <tr ng-repeat="(key, row_answer) in selected.row.answers" ng-if="!$first">
                            <td class="single line" style="font-weight: 700">{{ row_answer.title }}</td>
                            <td class="right aligned" ng-class="{disabled: target.loading}" ng-repeat-start="column_answer in selected.column.answers">
                                {{ crosstable[id][column_answer.value][row_answer.value] || 0 }}
                            </td>
                            <td class="right aligned" ng-repeat-end>
                                {{ getTotalPercent(getCrossTotal(id),crosstable[id][column_answer.value][row_answer.value] || 0) | number : 2 }}%
                            </td>
                            <td class="right aligned" ng-class="{disabled: target.loading}">{{getCrossRowTotal(id,row_answer.value)}}</td>
                            <td class="right aligned">
                                {{ getTotalPercent(getCrossTotal(id),getCrossRowTotal(id,row_answer.value) || 0) | number : 2 }}%
                            </td>
                        </tr>
                        <tr>
                            <td class="single line" style="font-weight: 700">總和</td>
                            <td class="right aligned" ng-repeat-start="answer in selected.column.answers">{{ getCrossColumnTotal(id,answer.value) }}</td>
                            <td class="right aligned" ng-repeat-end> {{ getTotalPercent(getCrossTotal(id), getCrossColumnTotal(id,answer.value) || 0) | number : 2 }}%</td>
                            <td class="right aligned">{{ getCrossTotal(id) }} </td>
                            <td class="right aligned">100%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        `,
    };
})

.service('analysisMethod', function($http, $q) {
    function getMean(answers, frequence){
        if (!frequence) {
            return 0;
        }
        var totalValue = 0;
        var totalAmount = 0;
        for (var i in answers) {
            totalValue += frequence[answers[i].value]*answers[i].value*1 || 0;
            totalAmount += frequence[answers[i].value]*1 || 0;
        }

        var mean = totalValue/totalAmount;
        return mean;
    }

    function setMean(set) {
        if(!set.meanSet){
            set.colPercent = false;
            set.rowPercent = false;
            set.totalPercent = false;
            set.meanSet = 1;
        }else{
            set.colPercent = false;
            set.rowPercent = false;
            set.totalPercent = false;
            set.meanSet = 0;
        }
    }

    function getFrequenceTotal(answers, frequence) {
        if (!frequence) {
            return 0;
        }

        var total = 0;
        for(var i in answers) {
            total += frequence[answers[i].value]*1 || 0;
        }
        return total;
    }

    function getTotalPercent(total, value) {
        return total == 0 ? 0 : value*100/total;
    }

    return {
        getMean: getMean,
        setMean: setMean,
        getFrequenceTotal: getFrequenceTotal,
        getTotalPercent: getTotalPercent,
    };
});