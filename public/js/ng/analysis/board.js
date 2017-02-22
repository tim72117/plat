'use strict';

angular.module('analysis.result', ['analysis.chart', 'analysis.table'])
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
            <div layout="column">
                <md-input-container flex>
                    <label>{{outputType}}</label>
                    <md-select ng-model="reportOutput" ng-change="drawChart()">
                        <md-option ng-value="chart.name" ng-disabled="disabledCharts[chart.name]" ng-repeat="chart in charts"><i class="{{ chart.icon }} icon"></i>{{chart.title}}</md-option>
                    </md-select>
                </md-input-container>

                <md-content flex>
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
                        <ng-cross-bar-chart ng-if="report.output=='bar' && report.type=='cross'" targets="targets" selected="selected" crosstable="crosstable"></ng-cross-bar-chart>
                        <ng-cross-pie-chart ng-if="report.output=='pie' && report.type=='cross'" targets="targets" selected="selected" crosstable="crosstable"></ng-cross-pie-chart>
                    </div>
                    <div ng-if="report.output == 'table'">
                        <ng-col-table ng-if="report.type == 'col'" targets="targets" selected="selected" frequence="frequence"></ng-col-table>
                        <ng-row-table ng-if="report.type == 'row'" targets="targets" selected="selected" frequence="frequence"></ng-row-table>
                        <ng-cross-table ng-if="report.type == 'cross'" targets="targets" selected="selected" crosstable="crosstable"></ng-cross-table>
                    </div>
                </md-content>
            </div>
        `,
        link: function(scope) {
            scope.report = {output: 'table', type: 'row'};
            scope.outputType = '輸出樣式:表格(預設)';
            scope.reportOutput = 'table';
            scope.charts = [{title: '表格', name: 'table', icon: 'table'}, {title: '長條圖', name: 'bar', icon: 'bar chart'}, {title: '圓餅圖', name: 'pie', icon: 'pie chart'}];

            scope.drawChart = function() {
                scope.report.output = angular.copy(scope.reportOutput);
                scope.disabledCharts = {bar: false, pie:  false};
                if (scope.report.output == 'table') {
                    if (scope.selected.column && !scope.selected.row)
                        scope.report.type = 'col';
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
        }
    };
});