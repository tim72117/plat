<!DOCTYPE html>
<html xml:lang="zh-TW" lang="zh-TW" ng-app="app">
<head>
<meta charset="utf-8" />
<title><?//=$title?></title>

<!--[if lt IE 9]><script src="/js/html5shiv.js"></script><![endif]-->
<script src="/js/angular-1.3.14/angular.min.js"></script>
<script src="/js/jquery-1.11.2.min.js"></script>
<script src="/js/Highcharts-4.0.3/js/highcharts.js"></script>
<script src="/js/Highcharts-4.0.3/js/modules/exporting.src.js"></script>

<script src="/analysis/use/js/drawfigure.js"></script>
<script src="/analysis/use/js/frequence.js"></script>
<script src="/analysis/use/js/crosstable.js"></script>
<script src="/analysis/use/js/correlation.js"></script>

<link rel="stylesheet" href="/css/Semantic-UI/Semantic-UI-2.0.7/semantic.min.css" />

</head>

<body ng-controller="analysisController">

<div ng-cloak class="ui container">

    <div class="ui secondary menu">
        <div class="item">
            <img class="ui image" src="/analysis/use/images/logo_top.png" />
        </div>
        <div class="item">
            <div class="ui small breadcrumb">
                <a class="section">首頁</a>
                <i class="right chevron icon divider"></i>
                <a class="section" href="open">{{ title }}</a>
                <i class="right chevron icon divider"></i>
                <a class="section" href="menu">選擇題目</a>
                <i class="right chevron icon divider"></i>
                <div class="active section">開始分析</div>
            </div>
        </div>
    </div>    

    <div class="ui grid" style="min-height:600px">
        
        <div class="five wide column">
            <div class="ui segment">

                    <!-- <select ng-model="part_selected.part" ng-options="part.part as part.part_name for part in parts" ng-change=""></select> -->

                    <div class="ui list" style="overflow-y: auto;max-height:600px">

                        <div class="item" ng-repeat="column in columns | filter: part_selected">
                            <div class="content">
                                <div class="ui checkbox">
                                    <input type="checkbox" class="hidden" id="column-{{ $index }}" ng-model="column.selected" ng-click="setColumns(column);getCount()" />
                                    <label for="column-{{ $index }}">{{ column.title }}</label>
                                </div>  

                            </div>

                            <div class="list">
                                <div class="item" ng-repeat="subs in question.subs">

                                </div>
                            </div>
                        </div>

        <!--                 <div class="item" ng-repeat="column in columns | filter: part_selected">
                            <div class="ui checkbox">
                                <input type="checkbox" id="column-{{ $index }}"  ng-model="column.selected" ng-click="get_variables(column)" />
                                <label for="column-{{ $index }}">{{ column.title }}</label>
                            </div>  
                        </div>   --> 
                        
                    </div>
            </div>

            <div class="ui left pointing labeled icon dropdown button active visible" ng-click="target.show=!target.show">
                <i class="add icon"></i>
                <span class="text">加入分析對象</span>                        
                
                <div class="menu transition" ng-class="{visible: target.show}" ng-click="$event.stopPropagation()">
                    
                    <div class="ui basic segment">
                        <div class="ui basic button" ng-repeat="(group_key, group) in targets.groups" ng-class="{active: group.selected}" ng-click="setGroup(group)">{{ group.name }}</div>
                    </div>
                    
                    <div class="ui basic segment" ng-repeat="group in targets.groups" ng-if="group.selected">
                        <div class="ui list">
                            <div class="item" ng-repeat="(target_key, target) in group.targets">
                                <div class="ui checkbox">
                                    <input type="checkbox" class="hidden" id="target-{{ target_key }}" ng-model="target.selected" ng-change="getCount()" />
                                    <label for="target-{{ target_key }}">{{ target.name }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="ui basic segment" ng-show="target_group==='my'">                                
                        <div class="ui checkbox">
                            <input type="checkbox" class="hidden" id="target-my" ng-model="targets['my'].selected" />
                            <label for="target-my">本校</label>
                        </div>
                    </div>
                    
                </div>
                
            </div>


        </div>

    <!--     <div class="ui floating dropdown labeled icon button">
            <i class="filter icon"></i>
            <span class="text">Filter Posts</span>
            <div class="menu transition visible" style="width:350px">
                <div class="item" ng-repeat="question in questions | filter: part_selected">
                    <div class="content">
                            <div class="ui checkbox">
                                <input type="checkbox" id="question-{{ $index }}"  ng-model="question.selected" ng-click="get_variables(question)" />
                                <label for="question-{{ $index }}">{{ question.title }}</label>
                            </div>  
                    </div>         
                </div>      
            </div>
        </div>    -->
    
        <div class="eleven wide column">

            <div class="ui top attached tabular menu">
                <div class="item" ng-class="{active: tool===1}" ng-click="tool=1">次數分配 / 交叉表</div>
    <!--             <div class="item" ng-class="{active: tool===3}" ng-click="tool=3">平均數比較</div>
                <div class="item" ng-class="{active: tool===4}" ng-click="tool=4">相關分析</div>
                <div class="item" ng-class="{active: tool===5}" ng-click="tool=5">迴歸分析</div> -->
            </div>

            <div class="ui bottom attached tab segment" ng-class="{active: tool===1}" style="min-height:600px">
                <? include_once('tb/use/tb1_inner_tiped.php') ?>
            </div>
            <div class="ui bottom attached tab segment" ng-class="{active: tool===3}" style="height:500px">
                <?// include_once('tb/use/'.$tb3_inner)?>3
            </div>
            <div class="ui bottom attached tab segment" ng-class="{active: tool===4}" style="height:500px">
                <?// include_once('tb/use/'.$tb4_inner)?>4
            </div>
            <div class="ui bottom attached tab segment" ng-class="{active: tool===5}" style="height:500px">
                <?// include_once('tb/use/'.$tb5_inner)?>5
            </div>


        </div>

    </div>    

</div>

<div class="ui dimmer modals page transition" ng-class="{visible: dialog.open, active: dialog.open}">
    
	<div class="ui fullscreen modal transition visible active" style="margin-top: -221px;">
    
		<i class="close icon" ng-click="dialog.open=false"></i>
		<div class="header">{{ dialog.question_label }}</div>
		<div class="content">
        
            <div class="ui icon basic button">
                <i class="bar chart icon"></i>
            </div>
        
            <div class="ui icon basic button">
                <i class="clockwise rotated bar chart icon"></i>
            </div>
            
			<table class="ui table">
				<thead>
                    <tr>
                        <th></th>
                        <th ng-repeat="variable in variables">{{ variable.variable_label }}</th>
                        <th></th>
                    </tr>
				</thead>
				<tbody ng-repeat="frequence in dialog.frequences">
					<tr>
						<td rowspan="2">{{ frequence.school }}</td>
						<td ng-repeat="variable in frequence.variables">{{ variable.count }}</td>
						<td>'+sum+'</td>
					</tr>
					<tr>
						<td ng-repeat="countVariable in frequence.variables">{{  }}</td>
						<td></td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<th ng-repeat="(name, otherinf) in frequence.otherinf">{{ name }}</th>
					</tr>
					<tr>
						<td ng-repeat="otherinf in frequence.otherinf">{{ otherinf }}</td>
					</tr>
				</tfoot>
			</table>

		</div>
	</div>
</div>
</body>



<script>
var app = angular.module('app', []);
app.controller('analysisController', function($scope, $filter, $interval, $http) {
    $scope.authority = 1;
    $scope.tool = 1;
	$scope.dialog = {open: false, frequences: []};
    $scope.frequence = {};
    $scope.crosstable = {};
    $scope.targets = [];
    $scope.target = {};
    $scope.columns = [];
    $scope.limit = 2;
    $scope.selected = {columns: [], rows: []};
    
    $scope.getColumns = function() {
        $http({method: 'POST', url: 'get_census', data:{} })
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.columns = data.questions;
            $scope.title = data.title;
            $scope.parts = data.census_parts;
        }).error(function(e){
            console.log(e);
        });
    };
   	
	$scope.getGroups = function (items) {
        var groupArray = [];
        angular.forEach(items, function (item, idx) {
            if( groupArray.indexOf(item.GroupByFieldName) === -1 )
				groupArray.push(item.GroupByFieldName);
        });
        return groupArray.sort();
    };

    $scope.targetsSelected = function() {
        var selected = [];
        for (var i in $scope.targets.groups) {
            var group = $scope.targets.groups[i];
            for (var j in group.targets) {
                if (group.targets[j].selected) {
                    group.targets[j].id = j;
                    selected.push(group.targets[j]);
                };
            }            
        };        
        return selected;
    };

    $scope.clear = function(column) {
        var index = $scope.columns.indexOf(column);
        $scope.columns[index].selected = false;
    };

    $scope.setColumns = function(column) {
        if (!column.selected) {
            if ($scope.selected.columns.indexOf(column) > -1) {
                $scope.selected.columns.length = 0;
            }
            if ($scope.selected.rows.indexOf(column) > -1) {
                $scope.selected.rows.length = 0;
            }
        } else {
            if ($scope.selected.columns.length == 0) {
                $scope.selected.columns[0] = column;
            } else {
                if ($scope.selected.rows.length == 0) {
                    $scope.selected.rows[0] = column;
                };
            }
        } 
        if ($filter("filter")($scope.columns, {selected: true}).length > $scope.limit) {
            column.selected = false;
        };
    }

    $scope.setGroup = function(group) {
        angular.forEach($scope.targets.groups, function(group, key) {
            if( group.selected )
                group.selected = false;
        });
        group.selected = true;
    };

    $scope.getTargets = function() {
        $http({method: 'POST', url: 'get_targets', data:{} })
		.success(function(data, status, headers, config) {
            console.log(data);		
            $scope.targets = data.targets;
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
    }
    
    $scope.getResults = function(method, names) { 
        console.log(names);  
        $scope.results = {};
        var groups = $scope.targets.groups;
        for( group_key in groups ) {
            for( target_key in groups[group_key].targets ) {
                if( groups[group_key].targets[target_key].selected )
                    method(names, group_key, target_key);
            }       
        }
    };

    $scope.getFrequence = function(names, group_key, target_key) {  
        $scope.targets.groups[group_key].targets[target_key].loading = true;
        $scope.loading = true;        
        $http({method: 'POST', url: 'get_frequence', data:{name: names[0], group_key: group_key, target_key: target_key} })
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.frequence[target_key] = data.frequence;
            $scope.total = 0 ;
            angular.forEach($scope.frequences, function(value) {
                $scope.total += (value.total || 0)*1;
            });
            $scope.targets.groups[group_key].targets[target_key].loading = false;
            $scope.loading = false;
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getCrossTable = function(names, group_key, target_key) {  
        $scope.targets.groups[group_key].targets[target_key].loading = true; 
        $scope.loading = true;     
        $http({method: 'POST', url: 'get_crosstable', data:{name1: names[0], name2: names[1], group_key: group_key, target_key: target_key} })
        .success(function(data, status, headers, config) {
            console.log(target_key);
            $scope.crosstable[target_key] = data.crosstable;
            $scope.total = 0 ;
            angular.forEach($scope.frequences, function(value) {
                $scope.total += (value.total || 0)*1;
            });
            $scope.targets.groups[group_key].targets[target_key].loading = false;
            $scope.loading = false;
        }).error(function(e){
            console.log(e);
        });
    };
    
    $scope.getColumns();
    $scope.getTargets();    
	
});
app.filter('groupby', function(){
    return function(items, group){       
		return items.filter(function(element, index, array) {
            return element.GroupByFieldName===group;
        });        
    };
});
var chart1;
var chartFreqObj;
var tab_panel = null;
</script>

<style>

</style>

</html>