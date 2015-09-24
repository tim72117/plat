<?php
$user = Auth::user();
list($apps, $requests) = app\library\files\v0\FileProvider::make()->lists();
$project = DB::table('projects')->where('code', $user->getProject())->first();
?>
@extends('demo.layout-main')

@section('head')
<title><?=$project->name?></title>

<!--[if lt IE 9]><script src="/js/html5shiv.js"></script><![endif]-->
<script src="/js/jquery-1.11.2.min.js"></script>
<script src="/js/angular-1.3.14/angular.min.js"></script>
<script src="/js/angular-1.3.14/angular-sanitize.min.js"></script>
<script src="/js/angular-1.3.14/angular-cookies.min.js"></script>
<script src="/css/Semantic-UI/Semantic-UI-2.0.7/components/transition.min.js"></script>
<script src="/css/Semantic-UI/Semantic-UI-2.0.7/components/popup.min.js"></script>
<script src="/js/angular-semantic-ui/angularify.semantic.js"></script>
<script src="/js/angular-semantic-ui/dropdown.js"></script>

<link rel="stylesheet" href="/css/Semantic-UI/Semantic-UI-2.0.7/semantic.min.css" />

<style>
::-webkit-scrollbar {
	width: 10px;
    background: #fff;
}
::-webkit-scrollbar-thumb {
    background: #aaa;
}
.flex {
    display:-webkit-flex;
    display:flex;
}
.menu-left {
    -webkit-flex: initial;
            flex: initial;
    width: 350px;
    min-width: 100px;
    padding: 5px;
}
.main {
    -webkit-flex: 1;
            flex: 1;
    padding: 5px;
}
</style>

<script>
var app = angular.module('app', ['ngSanitize', 'ngCookies']);
app.filter('startFrom', function() {
    return function(input, start) {
        return input.slice(start);
    };
})
.controller('topMenuController', function($scope, $filter, $http) {
    $scope.queryLog = function() {
        angular.element('.queryLog').css('height', '50%');
    };
})
.controller('mainController', function($scope, $filter, $http, $cookies) {
    $scope.menuMin = getCookie($cookies.menuMin) || false;
    $scope.closeLeftMenu = function() {        
        $scope.menuMin = !$scope.menuMin;
        $cookies.menuMin = $scope.menuMin;
    };
});
function getCookie(value) {
    try {
        return angular.fromJson(value);
    } catch(e) {
        console.log(e);
        return null;
    };
}
</script>
@stop

@section('body')
<div style="position:absolute;top:0;right:0;bottom:0;left:0">

        <div ng-controller="mainController">

            <div class="ui attached inverted secondary menu green" ng-controller="topMenuController">   
                <div class="menu">  
                    <a class="item" ng-click="closeLeftMenu()"><i class="sidebar icon"></i></a>
                </div> 
                <div class="right menu">                          
                    <a class="item" href="/page/project"><i class="home icon"></i>首頁</a>
                    <a class="item" href="/page/project/profile">個人資料</a>
                    <a class="item" href="/auth/password/change">更改密碼</a>
                    <a class="item" href="/auth/logout">登出</a>
                    <a class="item" href="javascript:void(0)" ng-cloak ng-if="<?=(Auth::user()->id==1)?>" ng-click="queryLog()">queryLog</a>
                </div>
            </div> 

            <div class="flex">
                <div class="menu-left">
                    <div class="ui fluid vertical menu">
                        <h3 class="header active item"><i class="laptop large icon"></i><?=$project->name?></h3>                    
                        <? foreach ($apps as $app) {                        
                            echo '<a class="item green' . (Request::path()==$app['link']?' active':'') . '" href="/' . $app['link'] . '">' . $app['title'] . '</a>';
                        } ?> 
                        <h3 class="header active item"><i class="cloud upload large icon"></i>待上傳資料</h3>  
                        <? foreach ($requests as $request) {   
                            echo '<a class="item green' . (Request::path()==$request['link']?' active':'') . '" href="/' . $request['link'] . '">' . $request['title'] . '</a>';
                        } ?>
                    </div>
                </div>
                <div class="main">
                    <?=$context?>
                </div>
            </div>

        </div>
		
		<div class="queryLog" style="display:none;position: absolute;bottom:0;height:0;width:100%;background-color: #fff;overflow-y: scroll;border-top:1px solid #000">			
			<?
				if( Auth::user()->id==1 ){
					$queries = DB::getQueryLog();
					foreach($queries as $key => $query){
						echo $key.' - ';var_dump($query);echo '<br /><br />';
					}
				}
			?>
		</div>
	
</div>	

@stop
