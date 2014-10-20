<!DOCTYPE html>
<html xmlns:ng="http://angularjs.org" ng-app="app">
<head>    
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=10" />
<meta http-equiv="X-UA-Compatible" content="IE=9" />
<meta http-equiv="X-UA-Compatible" content="IE=8" />
@yield('head')    
<script>
var app = angular.module('app', []);
</script>
</head>

<body>    
    @yield('body')
</body>

</html>