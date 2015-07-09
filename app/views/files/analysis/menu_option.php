<div ng-cloak ng-controller="analysisController" class="ui segment" style="position:absolute;top:10px;left:10px;right:10px;bottom:10px;overflow-y: auto">
    
    <div class="ui left floated segment" style="width:350px">

            <!-- <select ng-model="part_selected.part" ng-options="part.part as part.part_name for part in parts" ng-change=""></select> -->
            
            <div class="ui list" style="overflow-y: auto;max-height:600px">

                <div class="item" ng-repeat="question in questions | filter: part_selected">
                    <div class="content">
                        <div class="ui checkbox">
                            <input type="checkbox" id="question-{{ $index }}"  ng-model="question.selected" ng-click="get_variables(question)" />
                            <label for="question-{{ $index }}">{{ question.title }}</label>
                        </div>  

                    </div>

                    <div class="list">
                        <div class="item" ng-repeat="subs in question.subs">

                        </div>
                    </div>
                </div>

                            <div class="item" ng-repeat="question in questions | filter: part_selected">
                        <div class="ui checkbox">
                            <input type="checkbox" id="question-{{ $index }}"  ng-model="question.selected" ng-click="get_variables(question)" />
                            <label for="question-{{ $index }}">{{ question.title }}</label>
                        </div>  
            </div>   
                
            </div>
    </div>

    <div class="ui floating dropdown labeled icon button">
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
    </div>   
    
    <div class="ui left floated basic segment" style="min-width:800px">
        <div class="ui top attached tabular menu">
            <div class="item" ng-class="{active: tool===1}" ng-click="tool=1">次數分配</div>
            <div class="item" ng-class="{active: tool===2}" ng-click="tool=2">交叉表</div>
<!--             <div class="item" ng-class="{active: tool===3}" ng-click="tool=3">平均數比較</div>
            <div class="item" ng-class="{active: tool===4}" ng-click="tool=4">相關分析</div>
            <div class="item" ng-class="{active: tool===5}" ng-click="tool=5">迴歸分析</div> -->
        </div>

        <div class="ui bottom attached tab segment" ng-class="{active: tool===1}" style="min-height:500px">
            <? include_once('tb/use/tb1_inner_tiped.php')?>
        </div>
        <div class="ui bottom attached tab segment" ng-class="{active: tool===2}" style="height:500px">
            <?// include_once('tb/use/'.$tb2_inner)?>2
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

<script src="/analysis/use/js/jqueryUI/Highcharts-2.3.5/js/highcharts.js"></script>
<script src="/analysis/use/js/jqueryUI/Highcharts-2.3.5/js/modules/exporting.src.js"></script>

<script src="/analysis/use/js/drawfigure.js"></script>
<script src="/analysis/use/js/block_load.js"></script>
<script src="/analysis/use/js/frequence.js"></script>
<script src="/analysis/use/js/crosstable.js"></script>
<script src="/analysis/use/js/correlation.js"></script>

<script>
app.controller('analysisController', function($scope, $filter, $interval, $http) {
    $scope.authority = 1;
    $scope.tool = 1;
	$scope.dialog = {open: false, frequences: []};
    $scope.results = {};
    $scope.targets = [];
    $scope.target = {};
	
	$scope.getGroups = function (items) {
        var groupArray = [];
        angular.forEach(items, function (item, idx) {
            if( groupArray.indexOf(item.GroupByFieldName) === -1 )
				groupArray.push(item.GroupByFieldName);
        });
        return groupArray.sort();
    };
    
    $scope.setGroup = function(group) {
        angular.forEach($scope.targets.groups, function(group, key) {
            if( group.selected )
                group.selected = false;
        });
        group.selected = true;
    };
    
    $scope.getResult = function(name, group_key, target_key) {
        $scope.targets.groups[group_key].targets[target_key].loading = true;     
		$http({method: 'POST', url: 'get_frequence', data:{name: name, group_key: group_key, target_key: target_key} })
		.success(function(data, status, headers, config) {
			console.log(data);           
            $scope.results[target_key] = data.frequence;
            $scope.targets.groups[group_key].targets[target_key].loading = false;
            // $scope.dialog_frequence.otherinf = data.otherinf;
		}).error(function(e){
			console.log(e);
		});
    };
	
    $scope.getResults = function() {
        var name = $filter('filter')($scope.questions, {selected: true})[0].name;   
        $scope.results = {};
        var groups = $scope.targets.groups;
        for( group_key in groups ) {
            for( target_key in groups[group_key].targets ) {
                if( groups[group_key].targets[target_key].selected )
                    $scope.getResult(name, group_key, target_key);
            }		
        }
	};
    
    $scope.get_questions = function() {
        $http({method: 'POST', url: 'get_questions', data:{} })
		.success(function(data, status, headers, config) {
			console.log(data);
            $scope.questions = data.questions;
            $scope.parts = data.census_parts;
		}).error(function(e){
			console.log(e);
		});
    };
    
    $scope.get_variables = function(question) {
        
        var question_selected = $filter('filter')($scope.questions, {selected: true, name: '!' + question.name});
        if ( question_selected.length > 0 ) question_selected[0].selected = false;

        $scope.answers = question.answers; 

        $scope.getResults();
    };
    
    $scope.get_targets = function() {
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
                            var name = $filter('filter')($scope.questions, {selected: true})[0].name;
                            $scope.getResult(name, group_key, target_key);
                        }                        
                    });
                });    
            });
		}).error(function(e){
			console.log(e);
		});
    };
    
    $scope.get_questions();
    $scope.get_targets();    
	
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