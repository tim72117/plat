@extends('project.layout-main')

@section('head')
<title>{{ $project->name }}</title>

<!--[if lt IE 9]><script src="/js/html5shiv.js"></script><![endif]-->
<script src="/js/jquery-1.11.2.min.js"></script>
<script src="/js/angular/1.5.3/angular.min.js"></script>
<script src="/js/angular/1.5.3/angular-sanitize.min.js"></script>
<script src="/js/angular/1.5.3/angular-cookies.min.js"></script>
<script src="/js/angular/1.5.3/angular-animate.min.js"></script>
<script src="/js/angular/1.5.3/angular-aria.min.js"></script>
<script src="/js/angular/1.5.3/angular-messages.min.js"></script>
<script src="/js/angular_material/1.1.0-rc1/angular-material.min.js"></script>
<script src="/css/Semantic-UI/2.1.8/components/transition.min.js"></script>
<script src="/css/Semantic-UI/2.1.8/components/popup.min.js"></script>
<script src="/js/angular-semantic-ui/angularify.semantic.js"></script>
<script src="/js/angular-semantic-ui/dropdown.js"></script>

<link rel="stylesheet" href="/css/Semantic-UI/2.1.8/semantic.min.css" />
<link rel="stylesheet" href="/css/angular_material/1.1.0-rc1/angular-material.min.css">

<style>
::-webkit-scrollbar {
    width: 10px;
    background: #fff;
}
::-webkit-scrollbar-thumb {
    background: #aaa;
}
.flex {
    display: -webkit-flex;
    display: flex;
}
.menu-left {
    -webkit-flex: initial;
            flex: initial;
    min-width: 300px;
    max-width: 350px;
}
.context {
    -webkit-flex: 1;
            flex: 1;
}
.no-animate {
    -webkit-transition: none !important;
    transition: none !important;
}
</style>

<script>
var app = angular.module('app', ['ngSanitize', 'ngCookies', 'ngMaterial'])

app.config(['$compileProvider', function ($compileProvider) {
    $compileProvider.debugInfoEnabled(false);
}])

.controller('mainController', function($scope, $filter, $http, $cookies) {
    $scope.pathname = window.location.pathname;
    $scope.openLeftMenu = false;
    $scope.toggleLeftMenu = function() {
        $scope.openLeftMenu = !$scope.openLeftMenu;
    };
})

.controller('leftMenuController', function($scope, $filter, $http) {
    $scope.root = {};
    $scope.getDocs = function() {
        $scope.loading = true;
        $http({method: 'POST', url: '/docs/lists', data:{} })
        .success(function(data, status, headers, config) {
            $scope.root.docs = data.docs;
            $scope.loading = false;
        }).error(function(e) {
            console.log(e);
        });
    };
    $scope.getDocs();
})

.filter('startFrom', function() {
    return function(input, start) {
        return input.slice(start);
    };
});
</script>
@stop

@section('body')
<div ng-controller="mainController">

    <div class="ui attached inverted secondary menu green">
        <div class="menu">
            <a class="item" ng-class="{active: openLeftMenu}" ng-click="toggleLeftMenu()"><i class="history icon"></i> 近期存取</a>
            <a class="item" href="/project/intro" ng-class="{active: pathname == '/project/intro'}"><i class="home icon"></i>{{ $project->name }}</a>

            <a class="item" href="/docs/management" ng-class="{active: pathname == '/docs/management'}">
                <i class="folder outline icon" ng-class="{open: pathname == '/docs/management'}"></i> 我的檔案
            </a>
            <div class="item active" ng-cloak ng-if="pathname.indexOf('doc/') != -1"> / {{ @$doc->isFile->title }}</div>
            <div class="item active" ng-cloak ng-if="pathname.indexOf('request/') != -1"> / {{ @$request->description }}</div>
        </div>

        <div class="right menu">
            <a class="item" href="/project/{{ $project->code }}/profile" ng-class="{active: pathname == '/project/{{ $project->code }}/profile'}">個人資料</a>
            <a class="item" href="/auth/password/change" ng-class="{active: pathname == '/auth/password/change'}">更改密碼</a>
            <a class="item" href="/auth/logout">登出</a>
        </div>
    </div>

    <div class="flex">
        <div ng-cloak ng-controller="leftMenuController" class="ui basic segment menu-left" ng-class="{loading: loading}" ng-if="openLeftMenu">
            <div class="ui relaxed divided list">

                <div class="item" ng-repeat="doc in root.docs | orderBy: 'opened_at':true | limitTo:10">
                    <i class="history icon"></i>
                    <div class="content">
                        <a href="@{{ doc.link }}">@{{ doc.title }}</a>
                    </div>
                </div>

            </div>
        </div>
        <div class="context">
            <?=$context?>
        </div>
    </div>

</div>
@stop
