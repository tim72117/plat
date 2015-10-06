
<script src="/js/Highcharts-4.1.8/js/highcharts.js"></script>
<script src="/css/Semantic-UI/2.1.4/semantic.min.js"></script>
<script src="/js/chart/bar.js"></script>
<script src="/js/chart/pie.js"></script>

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
                    <div class="ui icon input"><input type="text" ng-model="searchText.title" placeholder="搜尋欄位..."><i class="search icon"></i></div>
                </div>
                <div class="item">
                    <div class="content">
                        <div class="menu" style="overflow-y: auto;max-height:{{ targets.size > 1 ? 250 : 500 }}px">
                            <div class="item" ng-repeat="column in columns | filter: {choosed: true} | filter: searchText">
                                <div class="content">
                                    <div class="ui checkbox" style="display: block">
                                        <input type="checkbox" class="hidden" id="column-{{ $index }}" ng-model="column.selected" ng-change="setColumns(column);getCount()" />
                                        <label for="column-{{ $index }}" style="overflow:hidden;white-space: nowrap;text-overflow: ellipsis">{{ column.title }}</label>
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
                                <div class="ui checkbox">
                                    <input type="checkbox" class="hidden" id="target-{{ target_key }}" ng-model="target.selected" ng-change="getCount()" />
                                    <label for="target-{{ target_key }}">{{ target.name }}</label>
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
                        <div class="ui floating labeled icon dropdown button chart">
                            <input type="hidden" value="table">
                            <i class="wizard icon"></i>
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

            <div class="ui bottom attached tab segment" ng-class="{active: tool===1}" style="min-height:600px">
                <? include_once('tb/use/tb1_inner_tiped.php') ?>
            </div>
            <div class="ui bottom attached tab segment" ng-class="{active: tool===2}" style="height:500px">
                <?// include_once('tb/use/'.$tb3_inner)?>3
            </div>
            <div class="ui bottom attached tab segment" ng-class="{active: tool===3}" style="height:500px">
                <?// include_once('tb/use/'.$tb4_inner)?>4
            </div>
            <div class="ui bottom attached tab segment" ng-class="{active: tool===4}" style="height:500px">
                <?// include_once('tb/use/'.$tb5_inner)?>5
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
        angular.forEach($scope.targets.groups, function(group, key) {
            if (group.selected)
                group.selected = false;
        });
        group.selected = true;
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
            for(var j in $scope.selected.columns[0].answers) {
                var value = $scope.selected.columns[0].answers[j].value;
                var amount = $scope.frequence[i][value] | 0;
                one.data.push(amount);
            }
            bar.series.push(one);
        }

        bar.xAxis.categories = $scope.selected.columns[0].answers.map(function(answer) { return answer.title; });
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

        pie.series = [];
        for (var target in targets) {
            var series = {
                name: targets[target].name,
                colorByPoint: true,
                data: []
            };
            for(var j in $scope.selected.columns[0].answers) {
                series.data.push({
                    name: $scope.selected.columns[0].answers[j].title,
                    y: $scope.frequence[target][$scope.selected.columns[0].answers[j].value] | 0
                });
            }
            pie.series.push(series);
        }

        $('#bar-container').highcharts(pie);
    };

    $scope.reset = function() {
        if ($('#bar-container').highcharts())
            $('#bar-container').highcharts().destroy();
    };

    $scope.drawChart = function() {
        if ($scope.result == 'bar' && $scope.selected.columns.length > 0 && $scope.selected.rows.length == 0)
            $scope.drawBar();
        if ($scope.result == 'bar' && $scope.selected.columns.length > 0 && $scope.selected.rows.length > 0)
            $scope.drawCrossBar();
        if ($scope.result == 'pie' && $scope.selected.columns.length > 0 && $scope.selected.rows.length == 0)
            $scope.drawPie();
    };

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
