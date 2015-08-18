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
var app = angular.module('app', []);
app.controller('analysisController', function($scope, $filter, $interval, $http) {
	$scope.docs = [];
	$scope.doc = {};
	$scope.clouds = 'C10';
	$scope.information = {};
	$scope.methods = {census: '普查', sampling: '抽樣調查'};

    $scope.allCensus = function() {
        $http({method: 'POST', url: 'all_census', data:{} })
        .success(function(data, status, headers, config) {
            $scope.docs = data.docs;
            var docs = $filter('filter')($scope.docs, {selected: true});
            if (docs.length > 0) {
            	$scope.selectDoc(docs[0]);
            	$scope.clouds = $scope.doc.target_people;
            }
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getInformation = function(doc) {
        $http({method: 'POST', url: '/file/' + doc.intent_key + '/information', data:{} })
        .success(function(data, status, headers, config) {
            $scope.information = data.information;
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.selectDoc = function(doc) {
    	$scope.getInformation(doc);    	
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

    $scope.allCensus();
});

</script>
<style>

</style>
</head>

<body ng-controller="analysisController">
	
<div class="ui container">

		<div class="ui secondary menu">
	        <div class="item">
				<h2 class="ui header">
				<img class="ui image" src="/analysis/use/images/edu_logo.png">
				<div class="content">教育部中小學師資資料庫</div>
				</h2>
	        </div>
	        <div class="item">
				<div class="ui small breadcrumb">
					<a class="section" href="/page/project">首頁</a>
					<i class="right chevron icon divider"></i>
					<div class="active section">選擇資料庫</div>
				</div>
			</div>
	    </div>  

	<h3 class="ui block center aligned header"><i class="database icon"></i>選擇資料庫 </h3>

	<div class="ui grid">
		
		<div class="four wide column">
			<div class="ui fluid vertical pointing menu">
				<div class="header item">資料庫類型 <div class="ui pointing left olive large label"><i class="puzzle icon"></i> 步驟 1 </div></div>
				<a href="javascript:void(0)" class="item" ng-click="clouds = 'T0'" ng-class="{active: clouds == 'T0'}">新進師資生</a>
				<a href="javascript:void(0)" class="item" ng-click="clouds = 'G0'" ng-class="{active: clouds == 'G0'}">應屆畢業生</a>
			</div>
		</div>	
		<div class="four wide column">	
			<div class="ui fluid vertical menu">
				<div class="header item">資料庫 <div class="ui pointing left olive large label"><i class="puzzle icon"></i> 步驟 2 </div></div>
				<a href="javascript:void(0)" class="item" ng-repeat="doc in docs | filter: {target_people: clouds}" ng-class="{active: doc.selected}" ng-click="selectDoc(doc)">
					{{ doc.is_file.title }}
				</a>
			</div>
		</div>

		<div class="eight wide column">
			<div class="ui segment" style="min-height:550px">
				<h4 class="ui header">資料庫資訊<div class="sub header">{{ doc.is_file.title }}</div></h4>
				<div class="ui divider"></div>
				<table class="ui definition table">
					<tbody>
						<tr>
							<td class="collapsing">調查開始時間 :</td>
							<td>{{ information.time_start }}</td>
						</tr>
						<tr>
							<td class="collapsing">調查結束時間 :</td>
							<td>{{ information.time_end }}</td>
						</tr>
						<tr>
							<td class="collapsing">調查方式 :</td>
							<td>{{ methods[information.method] }}</td>
						</tr>
						<tr>
							<td class="collapsing">調查對象 :</td>
							<td>{{ information.target_school }}</td>
						</tr>
						<tr>
							<td class="collapsing">母體數量 :</td>
							<td>{{ information.quantity_total }}</td>
						</tr>
						<tr ng-if="information.quantity_sample">
							<td class="collapsing">抽樣數 :</td>
							<td>{{ information.quantity_sample }}</td>
						</tr>
						<tr>
							<td class="collapsing">回收數 :</td>
							<td id="quantity_gets">{{ information.quantity_gets }}</td>
						</tr>
						<tr>
							<td class="collapsing">回收率 :</td>
							<td id="quantity_percent">{{ information.quantity_gets/information.quantity_total*100 || 0 }}%</td>
						</tr>
						<tr>
							<td class="collapsing">問卷內容 :</td>
							<td></td>
						</tr>
					</tbody>
				</table>
			</div>

		</div>

	</div>

	<div class="ui basic segment">
		<div class="ui two attached buttons">
			<button class="ui button">回上一頁</button>
			<button class="ui button" ng-class="{disabled: (docs | filter:{selected: true}).length < 1}" ng-click="enterDoc()">
				進入資料庫
				<div class="ui pointing left olive large label"><i class="puzzle icon"></i> 步驟 3 </div>
			</button>
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

</body>
</html>