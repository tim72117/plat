<!DOCTYPE html>
<html xml:lang="zh-TW" lang="zh-TW" ng-app="app">
<head>
<meta charset="utf-8" />
<title><?//=$title?></title>

<!--[if lt IE 9]><script src="/js/html5shiv.js"></script><![endif]-->
<script src="/js/angular-1.3.14/angular.min.js"></script>
<script src="/js/jquery-1.11.2.min.js"></script>

<link rel="stylesheet" href="/css/Semantic-UI/2.1.4/semantic.min.css" />

<script>
var app = angular.module('app', []);
var full = true;
</script>
</head>
<body>

    @include('files.analysis.analysis')

    <div class="ui container">
        <div class="ui divider"></div>
        <div class="ui center aligned basic segment">
            <div class="ui horizontal bulleted link list">              
                <span class="item">© 2013 國立台灣師範大學 教育研究與評鑑中心</span>
            </div>
        </div>
    </div> 
</body>
</html>