@extends('project.layout-main')

@section('head')
<title>{{ $project->name }}</title>

<!--[if lt IE 9]><script src="/js/html5shiv.js"></script><![endif]-->
<script src="/js/jquery-1.11.2.min.js"></script>
<script src="/js/angular/1.5.8/angular.min.js"></script>
<script src="/js/angular/1.5.8/angular-sanitize.min.js"></script>
<script src="/js/angular/1.5.8/angular-cookies.min.js"></script>
<script src="/js/angular/1.5.8/angular-animate.min.js"></script>
<script src="/js/angular/1.5.8/angular-aria.min.js"></script>
<script src="/js/angular/1.5.8/angular-messages.min.js"></script>
<script src="/js/angular_material/1.1.1/angular-material.min.js"></script>
<script src="/css/Semantic-UI/2.2.4/components/transition.min.js"></script>
<script src="/css/Semantic-UI/2.2.4/components/popup.min.js"></script>
<script src="/js/angular-semantic-ui/angularify.semantic.js"></script>
<script src="/js/angular-semantic-ui/dropdown.js"></script>

<link rel="stylesheet" href="/css/Semantic-UI/2.2.4/semantic.min.css" />
<link rel="stylesheet" href="/js/angular_material/1.1.1/angular-material.min.css">

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

app.config(function ($compileProvider, $mdIconProvider, $mdThemingProvider) {
    $compileProvider.debugInfoEnabled(false);
    $mdIconProvider.defaultIconSet('/js/angular_material/core-icons.svg', 24);
})

.controller('mainController', function($scope, $mdSidenav, $mdToast, $timeout) {
    $scope.main = {};
    $scope.main.pathname = window.location.pathname;
    $scope.main.isOpenLeftMenu = false;
    $scope.main.loading = false;
    $scope.main.toggleLeftMenu = function() {
        $mdSidenav('left').toggle();
    };
    $scope.main.showHelp = function() {
        $mdToast.show(
            $mdToast.simple()
            .textContent('聯絡電話：02-77343669　傳真：02-33433910')
            .position('top right')
            .hideDelay(20000)
            .action('關閉')
            .highlightAction(true)
        );
    };

    $scope.second = 600;
    angular.element(document).bind('mousemove', function (e) { $scope.second = 600; });
    function idle() {
        $timeout(function() {
            $scope.second--;
            if ($scope.second < 0) {
                window.location = '/auth/logout';
            }
            idle();
        }, 1000);
    }
    idle();

})

.controller('leftMenuController', function($scope, $http) {
    $scope.leftMenu = {};
    $scope.leftMenu.getDocs = function() {
        $scope.leftMenu.loading = true;
        $http({method: 'POST', url: '/docs/lists', data:{}})
        .success(function(data, status, headers, config) {
            $scope.leftMenu.docs = data.docs;
            $scope.leftMenu.loading = false;
        }).error(function(e) {
            console.log(e);
        });
    };
    $scope.leftMenu.getDocs();
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
    <md-toolbar>
        <div class="md-toolbar-tools">
            <md-button ng-class="{'md-raised': main.isOpenLeftMenu}" ng-click="main.toggleLeftMenu()">
                <md-icon ng-style="{fill: main.isOpenLeftMenu ? '#000' : '#fff'}" md-svg-icon="history"></md-icon>
                近期存取
            </md-button>
            <md-button ng-class="{'md-raised': main.pathname == '/project/{{ $project->code }}/intro'}" href="/project/{{ $project->code }}/intro">
                <md-icon ng-style="{fill: main.pathname == '/project/{{ $project->code }}/intro' ? '#000' : '#fff'}" md-svg-icon="home"></md-icon>
                {{ $project->name }}
            </md-button>
            <md-button ng-class="{'md-raised': main.pathname == '/docs/management'}" href="/docs/management">
                <md-icon md-svg-icon="@{{main.pathname == '/docs/management' ? 'folder-open' : 'folder'}}"></md-icon>
                我的檔案
            </md-button>
            <h2> / {{ @$doc->isFile->title }}</h2>
            <span flex></span>
            <md-button ng-class="{'md-raised': main.pathname == '/project/{{ $project->code }}/profile'}" href="/project/{{ $project->code }}/profile">個人資料</md-button>
            <md-button ng-class="{'md-raised': main.pathname == '/auth/password/change'}" href="/auth/password/change">更改密碼</md-button>
            <md-button class="md-icon-button" aria-label="需要幫助" ng-click="main.showHelp()">
                <md-icon md-svg-icon="help-outline"></md-icon>
            </md-button>
            <md-button href="/auth/logout">登出</md-button>
        </div>
    </md-toolbar>
    <div layout="column" flex layout-align="center center" ng-if="main.loading">
        <md-progress-circular md-mode="indeterminate"></md-progress-circular>
    </div>
    <md-sidenav class="md-sidenav-left" md-component-id="left" md-is-open="main.isOpenLeftMenu">
        <md-toolbar>
            <h1 class="md-toolbar-tools">近期存取</h1>
        </md-toolbar>
        <md-content ng-controller="leftMenuController" layout="row" flex ng-if="main.isOpenLeftMenu">
            <md-content layout="column" flex layout-align="center center" ng-if="leftMenu.loading">
                <md-progress-circular md-mode="indeterminate"></md-progress-circular>
            </md-content>
            <md-list>
                <md-list-item ng-repeat="doc in leftMenu.docs | orderBy: 'opened_at':true | limitTo:10" href="@{{ doc.link }}">
                    <md-icon md-svg-icon="history"></md-icon>
                    <p>@{{ doc.title }}</p>
                </md-list-item>
            </md-list>
        </md-content>
    </md-sidenav>
    <md-content flex ng-show="!main.loading">
        {{ $context }}
    </md-content>
</body>
@stop
