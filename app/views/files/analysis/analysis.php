
<script src="/js/Highcharts-4.1.8/js/highcharts.js"></script>
<script src="/css/Semantic-UI/2.1.4/semantic.min.js"></script>
<script src="/js/chart/bar.js"></script>
<script src="/js/chart/pie.js"></script>
<script src="/js/chart/donut.js"></script>

<div ng-cloak ng-controller="analysisController" ng-class="{'ui container': full}" style="{{ !full ? 'max-width:1127px' : '' }}">

    <div class="ui basic segment" ng-if="full">
        <div class="ui small breadcrumb">
            <a class="section" href="/page/project">查詢平台</a>
            <i class="right chevron icon divider"></i>
            <a class="section" href="open">選擇資料庫</a>
            <i class="right chevron icon divider"></i>
            <a class="section" href="menu">選擇題目</a>
            <i class="right chevron icon divider"></i>
            <div class="active section">開始分析</div>
        </div>
    </div>

    <div class="ui basic segment">

    <div class="ui grid">

        <div class="five wide column dimmable dimmed">

            <div class="ui inverted dimmer" ng-class="{active: loading}">
                <div class="ui text loader">Loading</div>
            </div>

            <div class="ui small fluid vertical accordion menu" style="margin-top:0">
                <div class="item">
                    <div class="ui icon input"><input type="text" ng-model="searchText.title" placeholder="搜尋關鍵字..."><i class="search icon"></i></div>
                </div>
                <div class="item">
                    <div class="content">
                        <div class="menu" style="overflow-y: auto;max-height:{{ targets.size > 1 ? 250 : 500 }}px">
                            <div class="item" ng-repeat="column in columns | filter: {choosed: true} | filter: searchText">
                                <div class="content">
                                    <div class="ui checkbox" style="display: block">
                                        <input type="checkbox" class="hidden" id="column-{{ $index }}" ng-model="column.selected" ng-change="setColumns(column);getCount()" />
                                        <label for="column-{{ $index }}" style="overflow:hidden;white-space: nowrap;text-overflow: ellipsis" title="{{ column.title }}">{{ column.title }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <h4 class="ui header">加入篩選條件</h4>
                </div>
                <div class="item" ng-repeat="(group_key, group) in targets.groups">
                    <a class="title" ng-class="{active: group.selected}" ng-click="setGroup(group)">
                        <i class="dropdown icon"></i>
                        {{ group.name }}
                    </a>
                    <div class="content" ng-class="{active: group.selected}">
                        <div class="menu" style="overflow-y: auto;max-height:200px">
                            <div class="item" ng-repeat="(target_key, target) in group.targets">
                                <div class="ui checkbox" style="display: block">
                                    <input type="checkbox" class="hidden" id="target-{{ target_key }}" ng-model="target.selected" ng-change="getCount()" />
                                    <label for="target-{{ target_key }}" style="overflow:hidden;white-space: nowrap;text-overflow: ellipsis" title="{{ target.name }}">{{ target.name }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="eleven wide column">

            <div class="ui top attached tabular menu">
                <div class="item" ng-class="{active: tool===1}" ng-click="tool=1">次數分配 / 交叉表</div>
<!--                 <div class="item" ng-class="{active: tool===2}" ng-click="tool=3">平均數比較</div>
                <div class="item" ng-class="{active: tool===3}" ng-click="tool=4">相關分析</div>
                <div class="item" ng-class="{active: tool===4}" ng-click="tool=5">迴歸分析</div> -->
                <div class="right menu">
                    <div class="item">

                        <div class="ui left labeled button">
                            <div class="ui right pointing label">
                                <i class="wizard icon"></i>
                                輸出結果
                            </div>
                            <div class="ui dropdown orange button chart">
                                <input type="hidden" value="table">
                                <div class="text"></div>
                                <div class="menu">
                                    <div class="item" data-value="table">
                                        <i class="table icon"></i>表格
                                    </div>
                                    <div class="item" data-value="bar">
                                        <i class="bar chart icon"></i>長條圖
                                    </div>
                                    <div class="item" data-value="pie">
                                        <i class="pie chart icon"></i>圓餅圖
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="ui bottom attached tab segment" ng-class="{active: tool===1}" style="min-height:600px">
                <?php include_once('tb/use/tb1_inner_tiped.php') ?>
            </div>
            <div class="ui bottom attached tab segment" ng-class="{active: tool===2}" style="height:500px">
                <?php // include_once('tb/use/'.$tb3_inner)?>3
            </div>
            <div class="ui bottom attached tab segment" ng-class="{active: tool===3}" style="height:500px">
                <?php // include_once('tb/use/'.$tb4_inner)?>4
            </div>
            <div class="ui bottom attached tab segment" ng-class="{active: tool===4}" style="height:500px">
                <?php // include_once('tb/use/'.$tb5_inner)?>5
            </div>


        </div>

    </div>
    </div>

</div>

<script>
var full = full | false;
app.controller('analysisController', function($scope, $filter, $interval, $http) {
    $scope.tool = 1;
    $scope.frequence = {};
    $scope.crosstable = {};
    $scope.targets = [];
    $scope.target = {};
    $scope.columns = [];
    $scope.limit = 2;
    $scope.selected = {columns: [], rows: []};
    $scope.result = 'table';
    $scope.loading = false;
    $scope.full = full;
    $scope.auto_length = 500;
    $scope.getColumns = function() {
        $scope.loading = true;
        $http({method: 'POST', url: 'get_analysis_questions', data:{} })
        .success(function(data, status, headers, config) {
            $scope.columns = data.questions;
            $scope.title = data.title;
            $scope.loading = false;
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getGroups = function (items) {
        var groupArray = [];
        angular.forEach(items, function (item, idx) {
            if (groupArray.indexOf(item.GroupByFieldName) == -1)
                groupArray.push(item.GroupByFieldName);
        });
        return groupArray.sort();
    };

    $scope.targetsSelected = function() {
        var selected = {};

        for (var i in $scope.targets.groups) {
            var group = $scope.targets.groups[i];
            for (var id in group.targets) {
                if (group.targets[id].selected) {
                    selected[id] = group.targets[id];
                }
            }
        };
        return selected;
    };

    $scope.clear = function(column) {
        var index = $scope.columns.indexOf(column);
        $scope.columns[index].selected = false;
    };

    $scope.exchange = function() {
        var columns = $scope.selected.columns;
        $scope.selected.columns = $scope.selected.rows;
        $scope.selected.rows = columns;
        $scope.getCount();
    };

    $scope.setColumns = function(column) {
        if (column.selected) {
            if ($scope.selected.columns.length == 0) {
                $scope.selected.columns.push(column);
            } else if ($scope.selected.rows.length == 0) {
                $scope.selected.rows.push(column);
            }
        } else {
            if ($scope.selected.columns.indexOf(column) > -1) {
                $scope.selected.columns.length = 0;
            }
            if ($scope.selected.rows.indexOf(column) > -1) {
                $scope.selected.rows.length = 0;
            }
        }
        if ($filter("filter")($scope.columns, {selected: true}).length > $scope.limit) {
            column.selected = false;
        };
    }

    $scope.setGroup = function(group) {
        if (!group.selected){
            group.selected = true;
        }else{
            group.selected = false;
        }

    };

    $scope.getTargets = function() {
        $http({method: 'POST', url: 'get_targets', data:{} })
        .success(function(data, status, headers, config) {
            $scope.targets = data.targets;
            $scope.targets.size = Object.keys($scope.targets.groups).length;
            //$scope.targets['my'].selected = true;
            angular.forEach($scope.targets.groups, function(group, group_key) {
                angular.forEach(group.targets, function(target, target_key) {
                    $scope.$watch('targets.groups["'+group_key+'"].targets["'+target_key+'"].selected', function(selected) {
                        if( selected )
                        {
                            //var name = $filter('filter')($scope.columns, {selected: true})[0].name;
                            //$scope.getResult(name, group_key, target_key);
                        }
                    });
                });
            });
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getCount = function() {
        if ($scope.selected.columns.length == 1 && $scope.selected.rows.length == 0) {
            var names = [$scope.selected.columns[0].name];
            $scope.getResults($scope.getFrequence, names);
        }

        if ($scope.selected.columns.length == 0 && $scope.selected.rows.length == 1) {
            var names = [$scope.selected.rows[0].name];
            $scope.getResults($scope.getFrequence, names);
        }

        if ($scope.selected.columns.length == 1 && $scope.selected.rows.length == 1) {
            var names = [$scope.selected.columns[0].name, $scope.selected.rows[0].name];
            $scope.getResults($scope.getCrossTable, names);
        }
        if ($scope.selected.columns.length == 0 && $scope.selected.rows.length == 0) {
            $scope.reset();
        };
    }

    $scope.getResults = function(method, names) {
        $scope.loading = true;
        $scope.results = {};
        var groups = $scope.targets.groups;
        for (group_key in groups) {
            for (target_key in groups[group_key].targets) {
                if (groups[group_key].targets[target_key].selected)
                    method(names, group_key, target_key);
            }
        }
        $scope.loading = false;
    };

    $scope.getFrequence = function(names, group_key, target_key) {
        $scope.targets.groups[group_key].targets[target_key].loading = true;
        $http({method: 'POST', url: 'get_frequence', data:{name: names[0], group_key: group_key, target_key: target_key} })
        .success(function(data, status, headers, config) {
            $scope.frequence[target_key] = data.frequence;
            $scope.total = 0 ;
            angular.forEach($scope.frequences, function(value) {
                $scope.total += (value.total || 0)*1;
            });
            $scope.targets.groups[group_key].targets[target_key].loading = false;

            $scope.drawChart();
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getCrossTable = function(names, group_key, target_key) {
        $scope.targets.groups[group_key].targets[target_key].loading = true;
        $http({method: 'POST', url: 'get_crosstable', data:{name1: names[0], name2: names[1], group_key: group_key, target_key: target_key} })
        .success(function(data, status, headers, config) {
            $scope.crosstable[target_key] = data.crosstable;
            $scope.total = 0 ;
            angular.forEach($scope.frequences, function(value) {
                $scope.total += (value.total || 0)*1;
            });
            $scope.targets.groups[group_key].targets[target_key].loading = false;

            $scope.drawChart();
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getColumns();
    $scope.getTargets();

    $scope.drawBar = function() {
        var targets = $scope.targetsSelected();

        bar.series = [];

        for (var i in targets) {
            var one = {name: targets[i].name, data: []};
            if ($scope.selected.columns.length > 0 ){
                for(var j in $scope.selected.columns[0].answers) {
                    var value = $scope.selected.columns[0].answers[j].value;
                    var amount = $scope.frequence[i][value] | 0;
                    one.data.push(amount);
                }
            }else{
                for(var j in $scope.selected.rows[0].answers) {
                var value = $scope.selected.rows[0].answers[j].value;
                var amount = $scope.frequence[i][value] | 0;
                one.data.push(amount);
                }
            }

            bar.series.push(one);
        }
        if ($scope.selected.columns.length > 0 ){
            bar.xAxis.categories = $scope.selected.columns[0].answers.map(function(answer) { return answer.title; });
        }else{
            bar.xAxis.categories = $scope.selected.rows[0].answers.map(function(answer) { return answer.title; });
        }

        $('#bar-container').highcharts(bar);
    };

    $scope.drawCrossBar = function() {
        var targets = $scope.targetsSelected();
        var crosstable = $scope.crosstable[Object.keys(targets)[0]];

        bar.series = [];

        var column_answers = $scope.selected.columns[0].answers;

        for(var i in column_answers) {
            var column_key = column_answers[i].value;
            var one = {name: column_answers[i].title, data: []};
            for(var j in $scope.selected.rows[0].answers) {
                var value = $scope.selected.rows[0].answers[j].value;
                var amount = crosstable[column_key][value] | 0;
                one.data.push(amount);
            }
            bar.series.push(one);
        }

        bar.xAxis.categories = $scope.selected.rows[0].answers.map(function(answer) { return answer.title; });
        $('#bar-container').highcharts(bar);
    };

    $scope.drawPie = function() {
        var targets = $scope.targetsSelected();
        var view_width = $('#pie-container').width();
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

            if ($scope.selected.columns.length > 0 ){
                for(var j in $scope.selected.columns[0].answers) {
                    series.data.push({
                        name: $scope.selected.columns[0].answers[j].title,
                        y: $scope.frequence[target][$scope.selected.columns[0].answers[j].value] | 0
                    });
                }
            }else{
                for(var j in $scope.selected.rows[0].answers) {
                    series.data.push({
                        name: $scope.selected.rows[0].answers[j].title,
                        y: $scope.frequence[target][$scope.selected.rows[0].answers[j].value] | 0
                    });
                }
            }
            pie.series.push(series);
            count_length ++;

        }
        pie.chart.height = pie_size*(1+count_length);
        $('#pie-container').highcharts(pie);
    };

    $scope.drawCrossPie = function() {
        var targets = $scope.targetsSelected();
        var crosstable = $scope.crosstable[Object.keys(targets)[0]];
        var colors = ["#7cb5ec", "#434348", "#90ed7d", "#f7a35c", "#8085e9", "#f15c80", "#e4d354", "#2b908f", "#f45b5b", "#698b22",
                      "#8b795e", "#8b8378", "#458b74", "#838b8b", "#00008b", "#cd3333", "#7ac5cd", "#66cd00", "#ee7621", "#ff7256",
                      "#cdc8b1", "#bcee68", "#9bcd9b"]

        donut.series=[];
        column_series = [];
        row_series = [];

        var column_answers = $scope.selected.columns[0].answers;
        colum_total = [];
        var sum_col_row = 0;


        for(var i in column_answers) {
            var column_key = column_answers[i].value;
            var temp_total = 0;
            for(var j in $scope.selected.rows[0].answers) {
                var value = $scope.selected.rows[0].answers[j].value;
                var amount = crosstable[column_key][value] | 0;
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
            for(var j in $scope.selected.rows[0].answers) {
                var value = $scope.selected.rows[0].answers[j].value;
                var amount = crosstable[column_key][value] | 0;
                var percent = (amount/sum_col_row)*100;
                var brightness = 0.2 - 0.1*(j / $scope.selected.rows[0].answers.length) ;
                row_series.push({name: $scope.selected.rows[0].answers[j].title, y: Math.floor(percent*100)/100, color: Highcharts.Color(colors[i]).brighten(brightness).get()});
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

        $('#pie-container').highcharts(donut);

    };

    $scope.reset = function() {
        if ($('#bar-container').highcharts())
            $('#bar-container').highcharts().destroy();
    };

    $scope.drawChart = function() {
        if ($scope.result == 'bar' && $scope.selected.columns.length == 0 )
            $scope.drawBar();
        if ($scope.result == 'bar' && $scope.selected.rows.length == 0)
            $scope.drawBar();
        if ($scope.result == 'bar' && $scope.selected.columns.length > 0 && $scope.selected.rows.length > 0)
            $scope.drawCrossBar();
        if ($scope.result == 'pie' && $scope.selected.columns.length == 0)
            $scope.drawPie();
        if ($scope.result == 'pie' && $scope.selected.rows.length == 0)
            $scope.drawPie();
        if ($scope.result == 'pie' && $scope.selected.columns.length > 0 && $scope.selected.rows.length > 0)
            $scope.drawCrossPie();

    };


    $scope.getTotalPercent = function(part_value,id) {
        var col_or_row_total = 0;

        if ($scope.selected.columns.length > 0 ){
            for(var j in $scope.selected.columns[0].answers) {
                var value = $scope.selected.columns[0].answers[j].value;
                var amount = $scope.frequence[id][value] | 0;
                col_or_row_total = col_or_row_total+amount;
            }
        }else{
            for(var j in $scope.selected.rows[0].answers) {
                var value = $scope.selected.rows[0].answers[j].value;
                var amount = $scope.frequence[id][value] | 0;
                col_or_row_total = col_or_row_total+amount;
            }
        }
        var percent = (part_value/col_or_row_total)*100;
        return percent;
    }

    $scope.getTotal = function(id) {
        var col_or_row_total = 0;

        if ($scope.selected.columns.length > 0 ){
            for(var j in $scope.selected.columns[0].answers) {
                var value = $scope.selected.columns[0].answers[j].value;
                var amount = $scope.frequence[id][value] | 0;
                col_or_row_total = col_or_row_total+amount;
            }
        }else{
            for(var j in $scope.selected.rows[0].answers) {
                var value = $scope.selected.rows[0].answers[j].value;
                var amount = $scope.frequence[id][value] | 0;
                col_or_row_total = col_or_row_total+amount;
            }
        }

        return col_or_row_total;
    }

    $scope.getTotal2 = function(key) {
        var targets = $scope.targetsSelected();
        var crosstable = $scope.crosstable[key];
        var column_answers = $scope.selected.columns[0].answers;
        colum_total = [];
        var sum_col_row = 0;


        for(var i in column_answers) {
            var column_key = column_answers[i].value;
            var temp_total = 0;
            for(var j in $scope.selected.rows[0].answers) {
                var value = $scope.selected.rows[0].answers[j].value;
                var amount = crosstable[column_key][value] | 0;
                temp_total = temp_total+amount;
            }
            colum_total[i] = temp_total;
        }

        for(var i in column_answers) {
            sum_col_row = sum_col_row+colum_total[i];
        }

        return sum_col_row;
    }

    $('.chart.dropdown').dropdown({onChange: function(value) {
        $scope.$apply(function() {
            $scope.result = value;
        });
        $scope.drawChart();
    }});

});
app.filter('groupby', function(){
    return function(items, group){
        return items.filter(function(element, index, array) {
            return element.GroupByFieldName===group;
        });
    };
});
</script>
