'use strict';

angular.module('analysis.chart', [])
.directive('ngBarChart', function($http) {
    return {
        restrict: 'E',
        replace: true,
        transclude: false,
        scope: {
            selected: '=',
            targets: '=',
            frequence: '='
        },
        template: `<div style="{{ 'min-height:'+getChartHeight()+'px' }}"></div>`,
        link: function(scope, element) {
            scope.getChartHeight = function() {
                if (scope.selected.column && !scope.selected.row) {
                    return scope.selected.column.answers.length*30+200;
                }
                if (scope.selected.row && !scope.selected.column) {
                    return scope.selected.row.answers.length*30+200;
                }
                if (scope.selected.column && scope.selected.row) {
                    var height = scope.selected.row.answers.length*scope.selected.column.answers.length*10+scope.selected.row.answers.length*10;
                    return height > 500 ? height : 500;
                }
            };

            scope.drawBar = function() {
                var targets = scope.targets;
                bar.series = [];
                bar.xAxis.title = bar.xAxis.title || {};
                bar.legend.title.text = '篩選條件';
                console.log(targets);

                for (var i in targets) {
                    var one = {name: targets[i].name, data: []};
                    if (scope.selected.column) {
                        bar.xAxis.title.text = scope.selected.column.title;
                        bar.xAxis.title.offset = -scope.selected.column.title.length*12;
                        var total = 0;
                        for(var j in scope.selected.column.answers) {
                            total += scope.frequence[i][scope.selected.column.answers[j].value]*1 || 0;
                        }
                        for(var j in scope.selected.column.answers) {
                            var value = scope.selected.column.answers[j].value;
                            var amount = scope.frequence[i][value]*1 || 0;
                            var percent = total == 0 ? 0 : amount*100/total;
                            one.data.push({y: percent, val: amount});
                        }
                    } else {
                        bar.xAxis.title.text = scope.selected.row.title;
                        bar.xAxis.title.offset = -scope.selected.row.title.length*12;
                        var total = 0;
                        for(var j in scope.selected.row.answers) {
                            total += scope.frequence[i][scope.selected.row.answers[j].value]*1 || 0;
                        }
                        for(var j in scope.selected.row.answers) {
                            var value = scope.selected.row.answers[j].value;
                            var amount = scope.frequence[i][value]*1 || 0;
                            var percent = total == 0 ? 0 : amount*100/total;
                            one.data.push({y: percent, val: amount});
                        }
                    }

                    bar.series.push(one);
                }
                if (scope.selected.column) {
                    bar.xAxis.categories = scope.selected.column.answers.map(function(answer) { return answer.title; });
                } else {
                    bar.xAxis.categories = scope.selected.row.answers.map(function(answer) { return answer.title; });
                }

                $(element).highcharts(bar);
            };

            scope.drawBar();
        }
    };
})

.directive('ngCrossBarChart', function($http) {
    return {
        restrict: 'E',
        replace: true,
        transclude: false,
        scope: {
            selected: '=',
            targets: '=',
            crosstable: '='
        },
        template: `<div style="{{ 'min-height:'+getChartHeight()+'px' }}"></div>`,
        link: function(scope, element) {
            scope.getChartHeight = function() {
                if (scope.selected.column && !scope.selected.row) {
                    return scope.selected.column.answers.length*30+200;
                }
                if (scope.selected.row && !scope.selected.column) {
                    return scope.selected.row.answers.length*30+200;
                }
                if (scope.selected.column && scope.selected.row) {
                    var height = scope.selected.row.answers.length*scope.selected.column.answers.length*10+scope.selected.row.answers.length*10;
                    return height > 500 ? height : 500;
                }
            };

            scope.drawCrossBar = function() {
                var targets = scope.targets;
                var crosstable = scope.crosstable[Object.keys(targets)[0]];

                bar.series = [];
                bar.legend.title.text = scope.selected.column.title;
                bar.xAxis.title = bar.xAxis.title || {};
                bar.xAxis.title.text = scope.selected.row.title;
                bar.xAxis.title.offset = -scope.selected.row.title.length*12;

                var column_answers = scope.selected.column.answers;

                var total = {};
                for(var i in column_answers) {
                    total[i] = 0;
                    var column_key = column_answers[i].value;
                    if(crosstable[column_key]==null){crosstable[column_key]=[]};
                    for(var j in scope.selected.row.answers) {
                        total[i] += crosstable[column_key][scope.selected.row.answers[j].value]*1 || 0;
                    }
                }
                for(var i in column_answers) {
                    var column_key = column_answers[i].value;
                    var one = {name: column_answers[i].title, data: []};
                    if(crosstable[column_key]==null){crosstable[column_key]=[]};
                    for(var j in scope.selected.row.answers) {
                        var value = scope.selected.row.answers[j].value;
                        var amount = crosstable[column_key][value] || 0;
                        var percent = total[i] == 0 ? 0 : amount*100/total[i];
                        one.data.push({y: percent, val: amount});
                    }
                    bar.series.push(one);
                }

                bar.xAxis.categories = scope.selected.row.answers.map(function(answer) { return answer.title; });
                $(element).highcharts(bar);
            };

            scope.drawCrossBar();
        }
    };
})

.directive('ngPieChart', function($http) {
    return {
        restrict: 'E',
        replace: true,
        transclude: false,
        scope: {
            selected: '=',
            targets: '=',
            frequence: '='
        },
        template: `<div style="{{ 'min-height:'+getChartHeight()+'px' }}"></div>`,
        link: function(scope, element) {
            scope.getChartHeight = function() {
                if (scope.selected.column && !scope.selected.row) {
                    return scope.selected.column.answers.length*30+200;
                }
                if (scope.selected.row && !scope.selected.column) {
                    return scope.selected.row.answers.length*30+200;
                }
                if (scope.selected.column && scope.selected.row) {
                    var height = scope.selected.row.answers.length*scope.selected.column.answers.length*10+scope.selected.row.answers.length*10;
                    return height > 500 ? height : 500;
                }
            };

            scope.drawPie = function() {
                var targets = scope.targets;
                var view_width = $(element).width();
                var pie_size=300;
                pie.series = [];
                var count_length = 0;
                for (var target in targets) {
                    var series = {
                        type:'pie',
                        name: targets[target].name,
                        colorByPoint: true,
                        data: [],
                        center: [view_width/2,200+count_length*(pie_size+70)],
                        size: pie_size,
                        showInLegend: false,
                        dataLabels: {
                            enabled: true,
                        },
                    };

                    if (scope.selected.column) {
                        for(var j in scope.selected.column.answers) {
                            series.data.push({
                                name: scope.selected.column.answers[j].title,
                                y: scope.frequence[target][scope.selected.column.answers[j].value]*1 || 0
                            });
                        }
                    }else{
                        for(var j in scope.selected.row.answers) {
                            series.data.push({
                                name: scope.selected.row.answers[j].title,
                                y: scope.frequence[target][scope.selected.row.answers[j].value]*1 || 0
                            });
                        }
                    }
                    pie.series.push(series);
                    count_length ++;

                }
                pie.chart.height = pie_size*(1+count_length);
                $(element).highcharts(pie);
            };

            scope.drawPie();
        }
    };
})

.directive('ngCrossPieChart', function($http) {
    return {
        restrict: 'E',
        replace: true,
        transclude: false,
        scope: {
            selected: '=',
            targets: '=',
            crosstable: '='
        },
        template: `<div style="{{ 'min-height:'+getChartHeight()+'px' }}"></div>`,
        link: function(scope, element) {
            scope.getChartHeight = function() {
                if (scope.selected.column && !scope.selected.row) {
                    return scope.selected.column.answers.length*30+200;
                }
                if (scope.selected.row && !scope.selected.column) {
                    return scope.selected.row.answers.length*30+200;
                }
                if (scope.selected.column && scope.selected.row) {
                    var height = scope.selected.row.answers.length*scope.selected.column.answers.length*10+scope.selected.row.answers.length*10;
                    return height > 500 ? height : 500;
                }
            };

            scope.drawCrossPie = function() {
                var targets = scope.targets;
                var crosstable = scope.crosstable[Object.keys(targets)[0]];
                var colors = ["#7cb5ec", "#90ed7d", "#f7a35c", "#8085e9", "#f15c80", "#e4d354", "#2b908f", "#f45b5b", "#698b22",
                            "#8b795e", "#8b8378", "#458b74", "#838b8b", "#00008b", "#cd3333", "#7ac5cd", "#66cd00", "#ee7621", "#ff7256",
                            "#cdc8b1", "#bcee68", "#9bcd9b"]

                donut.series=[];
                var column_series = [];
                var row_series = [];

                var column_answers = scope.selected.column.answers;
                var colum_total = [];
                var sum_col_row = 0;


                for(var i in column_answers) {
                    var column_key = column_answers[i].value;
                    var temp_total = 0;
                    if(crosstable[column_key]==null){crosstable[column_key]=[]};
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

                for(var i in column_answers) {
                    var column_percent = (colum_total[i]/sum_col_row)*100;
                    column_series.push({name: column_answers[i].title, y: Math.floor(column_percent*100)/100, color: colors[i]});
                }

                for(var i in column_answers) {
                    var column_key = column_answers[i].value;
                    if(crosstable[column_key]==null){crosstable[column_key]=[]};
                    for(var j in scope.selected.row.answers) {
                        var value = scope.selected.row.answers[j].value;
                        var amount = crosstable[column_key][value]*1 || 0;
                        var percent = (amount/sum_col_row)*100;
                        var brightness = 0.2 - 0.1*(j / scope.selected.row.answers.length) ;
                        row_series.push({name: scope.selected.row.answers[j].title, y: Math.floor(percent*100)/100, color: Highcharts.Color(colors[i]).brighten(brightness).get()});
                    }
                }
                donut.series=[{
                        name: '欄變數',
                        data: column_series,
                        size: '60%',
                        dataLabels: {
                            formatter: function () {
                                var new_name = this.point.name;
                                if(new_name.length>7) new_name = new_name.substring(0,7) + '...';
                                return this.y > 5 ? new_name  : null;
                            },
                        color: '#ffffff',
                        distance: -50
                        }

                    },{
                        name: '列變數',
                        data: row_series,
                        size: '80%',
                        innerSize: '60%',
                        dataLabels: {
                            formatter: function () {
                                // display only if larger than 1
                                var new_name = this.point.name;
                                if(new_name.length>7) new_name = new_name.substring(0,7) + '...';
                                return this.y > 1 ? '<b>' + new_name + ':</b> ' + this.y + '%' : null;
                            }
                        }
                    }];

                $(element).highcharts(donut);

            };

            scope.drawCrossPie();
        }
    };
});