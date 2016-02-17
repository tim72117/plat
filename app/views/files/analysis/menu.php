<!DOCTYPE html>
<html xml:lang="zh-TW" lang="zh-TW" ng-app="app">
<head>
<meta charset="utf-8" />
<title><?=$title?></title>

<!--[if lt IE 9]><script src="/js/html5shiv.js"></script><![endif]-->
<script src="/js/angular/1.4.7/angular.min.js"></script>

<link rel="stylesheet" href="/css/Semantic-UI/2.1.8/semantic.min.css" />

<script>
var app = angular.module('app', []);
app.controller('analysisController', function($scope, $filter, $interval, $http) {
    $scope.columns = [];
    $scope.question = {};
    $scope.is_variable = false;
    $scope.loading = false;

    $scope.getColumns = function() {
        $scope.loading = true;
        $http({method: 'POST', url: 'get_analysis_questions', data:{} })
        .success(function(data, status, headers, config) {
            $scope.columns = data.questions;
            $scope.title = data.title;
            $scope.loading = false;
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
            column.choosed = $scope.isSelectAll;
        });
    };

    $scope.resetAll = function() {
        $scope.isSelectAll = false;
    };	

    $scope.nextStep = function() {
        var myForm = document.getElementById('form-columns');
        if ($filter('filter')($scope.columns, {choosed: true}).length > 0) {
            myForm.submit();
        }
    };

    $scope.closeVariable = function() {
        $scope.is_variable = false;
    };

    $scope.getColumns();
});

</script>
</head>
<body ng-controller="analysisController" ng-click="closeVariable()">

	<div class="ui container">

        <div class="ui basic segment">
            <div class="ui small breadcrumb">
                <a class="section" href="/project/intro">查詢平台</a>
                <i class="right chevron icon divider"></i>
                <a class="section" href="open">選擇資料庫</a>
                <i class="right chevron icon divider"></i>
                <div class="active section">選擇題目</div>
            </div>
        </div>

        <div class="ui top attached segment">
            <div class="ui icon input"><input type="text" ng-model="searchText.title" placeholder="搜尋關鍵字..."><i class="search icon"></i></div>
            <a class="ui button" href="open">上一步</a>
            <a class="ui olive button" href="javascript:void(0)" ng-click="nextStep()">下一步</a>
        </div>

        <div class="ui attached segment" ng-class="{loading: loading}">

            <div class="ui checkbox">
                <input type="checkbox" id="selectAll" ng-model="isSelectAll" ng-change="selectAll()">
                <label for="selectAll">全選(勾選題目時，建議您參考問卷，以完整瞭解題目原意！)</label>
            </div>

            <div class="ui divider"></div>

            <div class="ui divided list" style="min-height:600px;max-height:600px;overflow-y:scroll">

                <div class="item" ng-repeat="column in columns | filter: searchText">

                    <div class="middle aligned content">
                        <div class="ui checkbox" style="margin-right:120px">
                            <input type="checkbox" class="hidden" id="column-{{ $index }}" ng-model="column.choosed" ng-change="resetAll()" />
                            <label for="column-{{ $index }}">{{ column.title }}</label>
                        </div>
                        <select class="ui right floated mini button">
                            <option value="">變項類型</option>
                            <option value="1">連續變項</option>
                            <option value="0">非連續變項</option>
                        </select>
                        <div class="ui right floated mini button" ng-click="$event.stopPropagation();getAnswers(column)">選項定義</div>
                    </div>

                </div>

            </div>

        </div>

	</div>

    <div class="ui container">
        <div class="ui divider"></div>
        <div class="ui center aligned basic segment">
            <div class="ui horizontal bulleted link list">
                <span class="item">© 2013 國立台灣師範大學 教育研究與評鑑中心</span>
            </div>
        </div>
    </div>

    <div class="ui modal transition visible active" style="margin-top: -300px" ng-if="is_variable" ng-click="$event.stopPropagation()">

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

    <form method="post" action="analysis" id="form-columns" style="display:none">
        <input type="text" name="columns_choosed[]" ng-model="column.name" ng-repeat="column in columns | filter: {choosed: true}" />
    </form>

</body>
</html>