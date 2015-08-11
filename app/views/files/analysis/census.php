<!DOCTYPE html>
<html xml:lang="zh-TW" lang="zh-TW" ng-app="app">
<head>
<meta charset="utf-8" />
<title><?//=$title?></title>

<!--[if lt IE 9]><script src="/js/html5shiv.js"></script><![endif]-->
<script src="/js/angular-1.3.14/angular.min.js"></script>
<script src="/js/jquery-1.11.2.min.js"></script>
<script src="/js/jquery-ui-1.11.4/jquery-ui.min.js"></script>

<link rel="stylesheet" href="/js/jquery-ui-1.11.4/jquery-ui.min.css" />

<link rel="stylesheet" href="/css/Semantic-UI/Semantic-UI-2.0.7/semantic.min.css" />

<script type="text/javascript">
var is_test = true;
$(document).ready(function(){	

	$( "#down_box" ).dialog({
		width:420,
		modal: true,
		resizable:false,
		position:['center',250],
		autoOpen: false
	});	
	
	$('ul.page5_list li').mousedown(function(){
					
		$.getJSON('get_census_info.php?CID='+$(this).attr('CID'),function(data) {
				console.log(data);	
				
				$('#text_title').text(data.dataBaseName);
				$('#census_time_start').text(data.census_time_start);
				$('#census_time_end').text(data.census_time_end);
				$('#census_method_name').text(data.census_method_name);				
				$('#census_target').text(data.census_target);
				$('#census_quantity_total').text(data.census_quantity_total);
				$('#census_quantity_sample').text(data.census_quantity_sample);
				$('#census_quantity_gets').text(data.census_quantity_gets);
				$('#census_quantity_percent').text(data.census_quantity_percent+'%');
				
				if(data.census_method=="sampling"){
					$('#census_quantity_sample_line').show();
				}else{
					$('#census_quantity_sample_line').hide();
				}
								
				if(data.link_questionaire!=''){
					$('#down_t1').removeAttr('disabled').attr('href',data.link_questionaire);
				}else{
					$('#down_t1').attr('disabled','disabled');
				}
				if(data.link_report!=''){
					$('#down_t2').removeAttr('disabled').attr('href',data.link_report);
				}else{
					$('#down_t2').attr('disabled','disabled');
				}

				
				$('.download_new').show();		

				if(data.isready!=1){
					$('#text_title').append('<span style="color:red;font-size:.8em">(資料測試中，如有問題請回報。)</span>');
				}
				
				$('#q_contextlist tbody').empty();
				for(i=0;i<data.part_inf.length;i++)
				{
					$('#q_contextlist tbody').append('<tr><td align="right">第'+(data.part_inf[i][0])+'部分:</td><td>'+data.part_inf[i][1]+'</td></tr>');					
				}		
				
				console.log(data.pointer_array);
				for(i in data.pointer_array){
					$('#pointer').append('<div>'+data.pointer_array[i]['quesStat']+'</div>');
				}
				
		}).error(function(e){ console.log(e); });	
	});
	
	$('#down_t2').click(function(){
		if($(this).is('[disabled]')){
			alert('全國性分析報告尚未釋出，請稍後再進行下載。');
			return false;
		}
	});
	$('.download a').click(function(){
		if($(this).is('[disabled]')){
			//alert('尚未提供下載。');
			return false;
		}
	});
	
});

var app = angular.module('app', []);
app.controller('analysisController', function($scope, $filter, $interval, $http) {
	$scope.docs = [];
	$scope.doc = {};
	$scope.clouds = 'C10';

    $scope.getCensus = function() {
        $http({method: 'POST', url: 'all_census', data:{} })
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.docs = data.docs;
            var docs = $filter('filter')($scope.docs, {selected: true});
            if (docs.length > 0) {
            	$scope.doc = docs[0];
            }
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.selectDoc = function(doc) {
    	$scope.doc.selected = false;
    	$scope.doc = doc;
    	doc.selected = true;
    };

    $scope.enterDoc = function() {
    	var docs = $filter('filter')($scope.docs, {selected: true});
    	if (docs.length > 0) {
    		location.replace('/file/' + docs[0].intent_key + '/menu'); 
    	};    	
    };

    $scope.getCensus();
});

</script>
<style>

</style>
</head>

<body ng-controller="analysisController">
	
<div class="ui container">

		<div class="ui secondary menu">
	        <div class="item">
	            <img class="ui image" src="/analysis/use/images/logo_top.png" />
	        </div>
	        <div class="item">
				<div class="ui small breadcrumb">
					<a class="section">首頁</a>
					<i class="right chevron icon divider"></i>
					<div class="active section">選擇資料庫</div>
				</div>
			</div>
	    </div>  

	<h3 class="ui block center aligned header"><i class="database icon"></i>選擇資料庫 </h3>

	<div class="ui grid">
		
		<div class="four wide column">
			<div class="ui fluid vertical pointing menu">
				<div class="header item">資料庫類型 <div class="ui pointing left olive large label"><i class="puzzle icon"></i> Step 1 </div></div>
				<a class="item" ng-click="clouds = 'C10'" ng-class="{active: clouds == 'C10'}">高一專一</a>
				<a class="item" ng-click="clouds = 'C11'" ng-class="{active: clouds == 'C11'}">高二專二</a>
				<a class="item" ng-click="clouds = 'CT'" ng-class="{active: clouds == 'CT'}">教師</a>
				<a class="item" ng-click="clouds = 'C11P'" ng-class="{active: clouds == 'C11P'}">高二家長</a>
			</div>
		</div>	
		<div class="four wide column">	
			<div class="ui fluid vertical menu">
				<div class="header item">資料庫 <div class="ui pointing left olive large label"><i class="puzzle icon"></i> Step 2 </div></div>
				<a class="item" ng-repeat="doc in docs | filter: {target_people: clouds}" ng-class="{active: doc.selected}"  ng-click="selectDoc(doc)">
					{{ doc.is_file.title }}
				</a>
			</div>
		</div>

		<div class="eight wide column">
			<div class="ui segment" style="min-height:550px">
				<h4>資料庫資訊</h4>
				<div class="ui divider"></div>
				<table width="400" border="0" cellspacing="0" cellpadding="0" style="font-size:.8em;line-height: 20px">
				<tr>
				<td width="140" align="right">調查開始時間 :</td>
				<td width="260" id="census_time_start"></td>
				</tr>
				<tr>
				<td align="right">調查結束時間 :</td>
				<td id="census_time_end"></td>
				</tr>
				<tr>
				<td align="right">調查方式 :</td>
				<td id="census_method_name"></td>
				</tr>
				<tr>
				<td align="right">調查對象 :</td>
				<td id="census_target"></td>
				</tr>
				<tr>
				<td align="right">母體數量 :</td>
				<td id="census_quantity_total"></td>
				</tr>
				<tr id="census_quantity_sample_line" style="display:none">
				<td align="right">抽樣數 :</td>
				<td id="census_quantity_sample"></td>
				</tr>
				<tr>
				<td align="right">回收數 :</td>
				<td id="census_quantity_gets"></td>
				</tr>
				<tr>
				<td align="right">回收率 :</td>
				<td id="census_quantity_percent"></td>
				</tr>
				<tr>
				<td align="right">問卷內容 :</td>
				<td><div class="download_new" style="display:none"><input type="button" style="width:70px"  value="開啟" id="down_t0" /></div></td>
				</tr>
				</table>
				<div id="pointer" style="font-size:.8em;line-height: 20px;overflow-y: scroll;height:200px">1</div>
			</div>

		</div>

	</div>

	<div class="ui basic segment">
		<div class="ui two attached buttons">
			<button class="ui button">回上一頁</button>
			<button class="ui button" ng-click="enterDoc()">進入資料庫</button>
		</div>
	</div>

	<div class="ui hidden divider"></div>

	<div class="ui inverted vertical footer segment" style="background-color: rgba(62,97,6,0.8);">
		<div class="ui container">
			<div class="ui stackable inverted divided equal height stackable grid">
				<div class="three wide column">
					<h4 class="ui inverted header">...</h4>
					<div class="ui inverted link list">
						<a href="#" class="item">...</a>
						<a href="#" class="item">...</a>
					</div>
				</div>
				<div class="three wide column">
					<h4 class="ui inverted header">....</h4>
					<div class="ui inverted link list">
						<a href="#" class="item">...</a>
						<a href="#" class="item">...</a>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>




<div id="down_box" title="問卷內容">
   <table id="q_contextlist" width="400" border="0" cellspacing="0" cellpadding="0">
    <thead>
		<tr>
			<td width="120" align="right" class="black" style="font-size:.9em">問卷內容 :</td>
			<td width="280">&nbsp;</td>
		</tr>
    </thead>
    <tbody style="font-size:.8em;line-height: 20px"></tbody>
    </table>   
	<div class="download" style="margin-left:30px;margin-right:0"><a id="down_t1" disabled="disabled" href="#" target="_blank">問卷下載</a></div>
	<div class="download" style="margin-left:10px;margin-right:0"><a id="down_t2" disabled="disabled" href="#" target="_blank">全國報告</a></div>
</div>


</body>
</html>