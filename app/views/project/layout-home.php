<!DOCTYPE html>
<html xmlns:ng="http://angularjs.org" ng-app="app" xml:lang="zh-TW" lang="zh-TW">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<title><?=$project->name?></title>

<!--[if lt IE 9]><script src="/js/html5shiv.js"></script><![endif]-->
<script src="/js/angular/1.5.3/angular.min.js"></script>
<script src="/js/angular/1.5.3/angular-sanitize.min.js"></script>
<script src="/js/angular/1.5.3/angular-cookies.min.js"></script>
<script src="/js/angular/1.5.3/angular-animate.min.js"></script>
<script src="/js/angular/1.5.3/angular-aria.min.js"></script>
<script src="/js/angular/1.5.3/angular-messages.min.js"></script>
<script src="/js/angular_material/1.1.0/angular-material.min.js"></script>

<link rel="stylesheet" href="/css/Semantic-UI/2.1.8/semantic.min.css" />
<link rel="stylesheet" href="/js/angular_material/1.1.0/angular-material.min.css">

<script>
var app = angular.module('app', ['ngMaterial']);
</script>
</head>

<body ng-cloak layout="column">
    <?=$context?>
</body>
</html>