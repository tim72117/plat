<?php
$_SESSION['def_city'] = '30';

$census = DB::reconnect('sqlsrv_analysis')->table('census_info')->where('used_site', 'used')->get();
?>

<div ng-controller="analysisController" style="position:absolute;top:10px;left:10px;right:10px;bottom:10px;overflow-y: auto;padding:1px">

<div ng-cloak class="ui segment">
    
    <div class="ui left floated segment" style="width:300px">
            <select ng-model="census_selected" ng-options="census.CID as census.census_text_title for census in census_" ng-change="get_questions(census_selected)"></select>

            <select ng-model="part_selected" ng-options="part.part_name for part in parts" ng-change=""></select>
            
            <div class="ui divided list" style="overflow-y: auto;max-height:500px">
                <div class="item">
                    <div class="ui checkbox">
                        <input type="checkbox" id="label_ques_for_all" />
                        <label for="label_ques_for_all">全選<span style="color:red">(勾選題目時，建議您參考問卷，以完整瞭解題目原意！)</span></label>
                    </div>
                </div>
                <div class="item" ng-repeat="question in questions">
                    <div class="content">
                        <div class="ui checkbox">
                            <input type="checkbox" id="question-{{ $index }}"  ng-model="question.selected" />
                            <label for="question-{{ $index }}">{{ question.label }}</label>
                        </div>  

                    </div>

                    <div class="list">
                        <div class="item" ng-repeat="subs in question.subs">

                        </div>
                    </div>
                </div>
            </div>
    </div>
    
    <div class="ui left floated basic segment" style="width:800px">
        <div class="ui top attached tabular menu">
            <div class="item" ng-class="{active: tool===1}" ng-click="tool=1">次數分配</div>
            <div class="item" ng-class="{active: tool===2}" ng-click="tool=2">交叉表</div>
            <div class="item" ng-class="{active: tool===3}" ng-click="tool=3">平均數比較</div>
            <div class="item" ng-class="{active: tool===4}" ng-click="tool=4">相關分析</div>
            <div class="item" ng-class="{active: tool===5}" ng-click="tool=5">迴歸分析</div>
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

        <div class="ui positive button" ng-click="start()">開始分析</div>
    </div>

</div>

<div class="ui dimmer modals page transition" ng-class="{visible: dialog.open, active: dialog.open}">
	<div class="ui fullscreen modal transition visible active" style="margin-top: -221px;">
		<i class="close icon" ng-click="dialog.open=false"></i>
		<div class="header">{{ dialog.question_label }}</div>
		<div class="content">
			<table class="ui table">
				<thead>

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
    $scope.census_ = angular.fromJson(<?=json_encode($census)?>);
    $scope.authority = 1;
    $scope.tool = 1;
	$scope.dialog = {open: false, frequences: []};
	$scope.target_group = 'my';
	$scope.target = {my: {selected: true}};

	console.log($scope.target);
	
	$scope.getGroups = function (items) {
        var groupArray = [];
        angular.forEach(items, function (item, idx) {
            if( groupArray.indexOf(item.GroupByFieldName) === -1 )
				groupArray.push(item.GroupByFieldName);
        });
        return groupArray.sort();
    };
    
    $scope.run = function(QID, target) {
		$http({method: 'POST', url: 'get_count_frequence', data:{QID: QID, target: target} })
		.success(function(data, status, headers, config) {
			console.log(data);
         	$scope.dialog.question_label = data.question_label;            
			$scope.dialog.frequences.push({variables: data.variables, school: data.school});
//			$scope.dialog_frequence.otherinf = data.otherinf;
			
		}).error(function(e){
			console.log(e);
		});
    };
	
    $scope.start = function() {
        var QID = $filter('filter')($scope.questions, {selected: true})[0].QID;
        var targets = $scope.target;
        $scope.dialog.frequences = [];
        for(target in targets) {
            console.log(target);
            $scope.run(QID, target);
        }		
        $scope.dialog.open = true;
	};
    
    $scope.get_questions = function(CID) {
        $http({method: 'POST', url: 'get_questions', data:{CID: CID} })
		.success(function(data, status, headers, config) {
			console.log(data);
            $scope.questions = data.questions;
            $scope.parts = data.census_parts;
		}).error(function(e){
			console.log(e);
		});
    };
    
    $scope.get_varibales = function(CID) {
        $http({method: 'POST', url: 'get_varibale', data:{CID: CID} })
		.success(function(data, status, headers, config) {
			console.log(data);
            $scope.questions = data.questions;
            $scope.parts = data.census_parts;
		}).error(function(e){
			console.log(e);
		});
    };
	
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
ul.filetree {
    scrollbar-face-color:#FFF;
    SCROLLBAR-TRACK-COLOR:#FFF;
    SCROLLBAR-ARROW-COLOR:#000;
    SCROLLBAR-HIGHLIGHT-COLOR:#000;
    SCROLLBAR-3DLIGHT-COLOR:#FFF;
    SCROLLBAR-SHADOW-COLOR:#000;
    SCROLLBAR-DARKSHADOW-COLOR:#FFF;	
}
::-webkit-scrollbar {
	width:5px;
	background:rgba(255,255,255,0.5);
}
::-webkit-scrollbar-thumb {background:#ccc;-webkit-border-radius:10px;}

</style>