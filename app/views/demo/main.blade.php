@extends('demo.layout-main')

@section('head')
<title>{{ $project->name }}</title>

<!--[if lt IE 9]><script src="/js/html5shiv.js"></script><![endif]-->
<script src="/js/jquery-1.11.2.min.js"></script>
<script src="/js/angular/1.4.7/angular.min.js"></script>
<script src="/js/angular/1.4.7/angular-sanitize.min.js"></script>
<script src="/js/angular/1.4.7/angular-cookies.min.js"></script>
<script src="/css/Semantic-UI/2.1.6/components/transition.min.js"></script>
<script src="/css/Semantic-UI/2.1.6/components/popup.min.js"></script>
<script src="/js/angular-semantic-ui/angularify.semantic.js"></script>
<script src="/js/angular-semantic-ui/dropdown.js"></script>

<link rel="stylesheet" href="/css/Semantic-UI/2.1.6/semantic.min.css" />

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
    width: 300px;
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

})
.controller('leftMenuController', function($scope, $filter, $http, $cookies) {
    $scope.menuMin = $cookies.get('menuMin') || false;
    $scope.pathname = window.location.pathname;
    $scope.closeLeftMenu = function() {
        $scope.menuMin = !$scope.menuMin;
        $cookies.put('menuMin', $scope.menuMin);
    };
    $scope.getDocs = function() {
        $scope.loading = true;
        $http({method: 'POST', url: '/docs/lists', data:{} })
        .success(function(data, status, headers, config) {
            $scope.apps = data.apps;
            $scope.requests = data.requests;
            $scope.loading = false;
        }).error(function(e){
            console.log(e);
        });
    };
    $scope.getDocs();
});
</script>
@stop

@section('body')
<div style="position:absolute;top:0;right:0;bottom:0;left:0">

    <div class="ui attached inverted secondary menu green" ng-controller="topMenuController">
        <div class="menu">
            <a class="item" ng-click="closeLeftMenu()"><i class="sidebar icon"></i></a>
            <div class="item">{{ $project->name }}</div>
        </div>
        <div class="right menu">
            <a class="item" href="/page/project"><i class="home icon"></i>首頁</a>
            <a class="item" href="/page/project/profile">個人資料</a>
            <a class="item" href="/auth/password/change">更改密碼</a>
            <a class="item" href="/auth/logout">登出</a>
        </div>
    </div>

    <div class="flex">
        <div class="menu-left dimmable dimmed" ng-controller="leftMenuController">
            <div class="ui inverted dimmer" ng-class="{active: loading}">
                <div class="ui text loader">Loading</div>
            </div>
            <div class="ui large fluid vertical menu">
                <div class="item">
                    <div class="ui icon small input"><input type="text" ng-model="searchText.title" placeholder="搜尋..."><i class="search icon"></i></div>
                </div>
                <div class="item">
                    <div ng-cloak class="menu" style="overflow-y: auto;max-height:400px">
                        <a class="header green item" ng-class="{active: pathname == '/'+app.link}" ng-repeat="app in apps | filter: searchText" href="/@{{ app.link }}">@{{ app.title }}</a>
                    </div>
                </div>
                <div class="item">
                    <div class="header"><i class="cloud upload large icon"></i>待上傳資料</div>
                    <div ng-cloak class="menu" style="overflow-y: auto;max-height:200px">
                        <a class="header green item" ng-class="{active: pathname == '/'+request.link}" ng-repeat="request in requests" href="/@{{ request.link }}">@{{ request.title }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="main">
            <?=$context?>
        </div>
    </div>

</div>

@stop
