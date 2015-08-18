<!DOCTYPE html>
<html xml:lang="zh-TW" lang="zh-TW" ng-app="app">
<head>
<meta charset="utf-8" />
<title><?//=$title?></title>

<!--[if lt IE 9]><script src="/js/html5shiv.js"></script><![endif]-->
<script src="/js/angular-1.3.14/angular.min.js"></script>

<link rel="stylesheet" href="/js/jquery-ui-1.11.4/jquery-ui.min.css" />

<link rel="stylesheet" href="/css/Semantic-UI/Semantic-UI-2.0.7/semantic.min.css" />

<script>
var app = angular.module('app', []);
app.controller('analysisController', function($scope, $filter, $interval, $http) {
	$scope.columns = [];
	$scope.question = {};
	$scope.is_variable = false;

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

    $scope.getAnswers = function(question) {
    	$scope.question = question;
    	$scope.is_variable = true;
    };

    $scope.selectAll = function() {
    	angular.forEach($scope.columns, function(column) {
    		column.selected = $scope.isSelectAll;
    	});
    };

    $scope.resetAll = function() {
    	$scope.isSelectAll = false;
    };	

    $scope.nextStep = function() {
    	var myForm = document.getElementById("form-columns");
    	var columns = $filter('filter')($scope.columns, {selected: true}).map(function(column) {
	  		var myInput = document.createElement("input");
	  		myInput.setAttribute("type", "text") ;
			myInput.setAttribute("name", "columns_selected[]");
			myInput.setAttribute("value", column.name);
			myForm.appendChild(myInput);
    		return column.name;
    	});
    	if (columns.length > 0) {
    		myForm.submit();
    	}
    };

    $scope.getColumns();
});

</script>

<style>

</style>

</head>
<?php
if (false)
foreach($questions as $ques) {

		//var_dump($ques);

		$label = $doc->createElement( 'label', str_replace("&", "&amp;", $ques->title) );
		// $label->setAttribute( "for", 'label_for_Q'.$ques['QID'] );
		// $label->setAttribute( "style", 'display:inline-block'.$label_style );
		
		// if( in_array($ques['QID'],$QID_selected_array) )
		// $label->setAttribute( "style", 'color:#f00' );
		$checkbox = $doc->createElement( 'input', '' );
		$checkbox->setAttribute( "type", "checkbox" );
		$checkbox->setAttribute( "value", $ques->name );
		// $checkbox->setAttribute("id", 'label_for_Q'.$ques['QID'] );
		
		// if( in_array($ques['QID'],$QID_selected_array) )
		// $checkbox->setAttribute("checked", 'checked' );
		
		$btn = $doc->createElement( 'input', '' );
		$btn->setAttribute("type", 'button' );
		$btn->setAttribute("value", '選項定義' );
		$btn->setAttribute("style", 'margin-right:10px;float:right' );
		$btn->setAttribute("name", 'btn01' );

		$span = $doc->createElement( 'span', '' );
		$span->setAttribute( "class", "file" );
		$span->setAttribute( "style", "width:530px;display:inline-block;border-bottom:0px dashed #555" );
		
		// if( $ques['qtype']!=='head' )
		$label->insertBefore( $checkbox,$label->firstChild );
		$span->appendChild( $label );
		
		$li = $doc->createElement( 'li', '' );
		// $li->setAttribute("xml:id", $ques['spss_name']);
		// $li->setAttribute("part", $ques['part']);
		$li->appendChild( $span );
		// if( $ques['qtype']!=='head' ) $li->appendChild( $btn );
		
		if( is_object($filtered_array[1]) )
		$filtered_array[1]->item(0)->appendChild( $li );
}
?>
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
					<a class="section" href="open">{{ title }}</a>
					<i class="right chevron icon divider"></i>
					<div class="active section">選擇題目</div>
				</div>
			</div>
	    </div>   

		<h3 class="ui block center aligned header"><i class="database icon"></i>{{ title }}</h3>

		<div class="ui grid">

			<div class="five wide column">
				<div class="ui fluid vertical pointing menu">
					<div class="header item">題目類型</div>
					<a class="item" ng-click="clouds = '1'" ng-class="{active: clouds == '1'}">1</a>
				</div>
			</div>

			<div class="eleven wide column">

				<div class="ui segment">

					<div class="ui checkbox">
						<input type="checkbox" id="selectAll" ng-model="isSelectAll" ng-change="selectAll()">
						<label for="selectAll">全選(勾選題目時，建議您參考問卷，以完整瞭解題目原意！)</label>
					</div>

					<div class="ui divider"></div>

					<div class="ui divided list" style="min-height:500px;max-height:500px;overflow-y:scroll">

						<div class="item" ng-repeat="column in columns">

                            <div class="middle aligned content">
								<div class="ui checkbox" style="margin-right:120px">
                                    <input type="checkbox" class="hidden" id="column-{{ $index }}" ng-model="column.selected" ng-change="resetAll()" />
                                    <label for="column-{{ $index }}">{{ column.title }}</label>
                                </div>
                                <div class="ui right floated mini button" ng-click="getAnswers(column)">選項定義</div>                             
                            </div>

						</div>

					</div>

				</div>



			</div>

		</div>

		<div class="ui basic segment">
			<div class="ui two attached buttons">
				<a class="ui button" href="open">上一步</a>
				<a class="ui button" href="javascript:void(0)" ng-click="nextStep()">下一步</a>
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
						</div>
					</div>
					<div class="three wide column">
						<h4 class="ui inverted header">....</h4>
						<div class="ui inverted link list">
							<a href="#" class="item">...</a>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

	

	<div class="ui small test modal transition visible active" style="margin-top: -300px" ng-if="is_variable">
		<h3 class="ui header">{{ question.title }}</h3>
		<div class="ui basic segment" style="max-height:500px;overflow-y:auto">
			
			<table class="ui celled table">
				<thead>
					<tr>
						<th>數值</th>
		                <th>選項名稱</th>
					</tr>
				</thead>
				<tr ng-repeat="answer in question.answers">
					<td>{{ answer.value }}</td>
					<td>{{ answer.title }}</td>
				</tr>
			</table> 
		</div>
	</div>

	<form method="post" action="analysis" id="form-columns" style="display:none"></form>

</body>
</html>