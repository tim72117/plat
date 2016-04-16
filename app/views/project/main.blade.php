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
<script src="/js/angular_material/1.1.0/angular-material.min.js"></script>
<script src="/css/Semantic-UI/2.1.8/components/transition.min.js"></script>
<script src="/css/Semantic-UI/2.1.8/components/popup.min.js"></script>
<script src="/js/angular-semantic-ui/angularify.semantic.js"></script>
<script src="/js/angular-semantic-ui/dropdown.js"></script>

<link rel="stylesheet" href="/css/Semantic-UI/2.1.8/semantic.min.css" />
<link rel="stylesheet" href="/js/angular_material/1.1.0/angular-material.min.css">

<style>
::-webkit-scrollbar {
    width: 10px;
    background: #fff;
}
::-webkit-scrollbar-thumb {
    background: #aaa;
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

.controller('mainController', function($scope, $filter, $http, $cookies, $mdSidenav) {
    $scope.pathname = window.location.pathname;
    $scope.openLeftMenu = false;
    $scope.toggleLeftMenu = function() {
        $mdSidenav('left').toggle();
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
<body ng-cloak ng-controller="mainController" layout="column">
    <md-toolbar layout="row">
        <div class="md-toolbar-tools">
            <md-button ng-class="{'md-raised': openLeftMenu}" ng-click="toggleLeftMenu()"><i class="history icon"></i> 近期存取</md-button>
            <md-button ng-class="{'md-raised': pathname == '/project/intro'}" href="/project/intro"><i class="home icon"></i>{{ $project->name }}</md-button>
            <md-button ng-class="{'md-raised': pathname == '/docs/management'}" href="/docs/management">
                <i class="folder outline icon" ng-class="{open: pathname == '/docs/management'}"></i>我的檔案
            </md-button>
            <span flex></span>
            <md-button ng-class="{'md-raised': pathname == '/project/{{ $project->code }}/profile'}" href="/project/{{ $project->code }}/profile">個人資料</md-button>
            <md-button ng-class="{'md-raised': pathname == '/auth/password/change'}" href="/auth/password/change">更改密碼</md-button>
            <md-button href="/auth/logout">登出</md-button>
        </div>
    </md-toolbar>
    <md-content layout="row" flex>
        <md-sidenav class="md-sidenav-left" md-component-id="left" layout="column">
            <md-content ng-controller="leftMenuController" layout="row" flex ng-if="openLeftMenu">
                <md-content layout="column" flex layout-align="center center" ng-if="loading">
                    <md-progress-circular md-mode="indeterminate"></md-progress-circular>
                </md-content>
                <md-list>
                    <md-list-item ng-repeat="doc in root.docs | orderBy: 'opened_at':true | limitTo:10">
                        <md-icon><i class="history icon"></i></md-icon>
                        <div class="md-list-item-text" layout="column">
                            <a href="@{{ doc.link }}">@{{ doc.title }}</a>
                        </div>
                    </md-list-item>
                </md-list>
            </md-content>
        </md-sidenav>
        <md-content layout="column" flex>
            <?=$context?>
        </md-content>
    </md-content>
</body>
@stop
