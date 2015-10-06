<!DOCTYPE html>
<html xml:lang="zh-TW" lang="zh-TW" ng-app="app">
<head>
<meta charset="utf-8" />
<title><?//=$title?></title>

<!--[if lt IE 9]><script src="/js/html5shiv.js"></script><![endif]-->
<script src="/js/angular-1.3.14/angular.min.js"></script>

<link rel="stylesheet" href="/css/Semantic-UI/2.1.4/semantic.min.css" />

<script>
var app = angular.module('app', []);
app.controller('analysisController', function($scope, $filter, $interval, $http) {
    $scope.docs = [];
    $scope.doc = {};
    $scope.clouds = 'C10';
    $scope.information = {};
    $scope.methods = {census: '普查', sampling: '抽樣調查'};

    $scope.allCensus = function() {
        $http({method: 'POST', url: 'all_census', data:{}})
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.docs = data.docs;
            var docs = $filter('filter')($scope.docs, {selected: true});
            if (docs.length > 0) {
                $scope.selectDoc(docs[0]);
                $scope.clouds = $scope.doc.is_file.analysis.target_people;
            }
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.selectDoc = function(doc) { 
        $scope.information = doc.analysis;
        $scope.doc.selected = false;
        $scope.doc = doc;
        doc.selected = true;
    };

    $scope.enterDoc = function() {
        if ($scope.doc.id) {
            location.replace('menu');
        };
    };

    $scope.allCensus();
});

</script>
</head>

<body ng-controller="analysisController">

    <div class="ui container">

        <div class="ui basic segment">
            <div class="ui small breadcrumb">
                <a class="section" href="/page/project">查詢平台</a>
                <i class="right chevron icon divider"></i>
                <div class="active section">選擇資料庫</div>
            </div>
        </div>

        <div class="ui grid">

            <div class="four wide column">
                <div class="ui fluid vertical pointing menu">
                    <div class="header item">資料庫類型 <div class="ui olive large label"><i class="puzzle icon"></i> 步驟 1 </div></div>
                    <a href="javascript:void(0)" class="item" ng-click="clouds = 'C10'" ng-class="{active: clouds == 'C10'}">高一專一</a>
                    <a href="javascript:void(0)" class="item" ng-click="clouds = 'C11'" ng-class="{active: clouds == 'C11'}">高二專二</a>
                    <a href="javascript:void(0)" class="item" ng-click="clouds = 'CT'" ng-class="{active: clouds == 'CT'}">教師</a>
                    <a href="javascript:void(0)" class="item" ng-click="clouds = 'C11P'" ng-class="{active: clouds == 'C11P'}">高二家長</a>
                </div>
            </div>
            <div class="four wide column">
                <div class="ui fluid vertical menu">
                    <div class="header item">資料庫 <div class="ui olive large label"><i class="puzzle icon"></i> 步驟 2 </div></div>
                    <a href="javascript:void(0)" class="item" ng-repeat="doc in docs | filter: {analysis: {target_people: clouds}}" ng-class="{active: doc.selected}" ng-click="selectDoc(doc)">
                        {{ doc.is_file.title }}
                    </a>
                </div>
            </div>

            <div class="eight wide column" style="min-height:550px">
                <div class="ui top attached segment">
                    <button class="ui button">回上一頁</button>
                    <button class="ui olive button" ng-class="{disabled: (docs | filter: {selected: true}).length < 1}" ng-click="enterDoc()">
                        <i class="puzzle icon"></i> 步驟 3 進入資料庫
                    </button>
                </div>
                <div class="ui attached segment">{{ information.title }}</div>
                <table class="ui attached table">
                    <tbody>
                        <tr>
                            <td class="collapsing">調查開始時間 :</td>
                            <td>{{ information.time_start }}</td>
                        </tr>
                        <tr>
                            <td class="collapsing">調查結束時間 :</td>
                            <td>{{ information.time_end }}</td>
                        </tr>
                        <tr>
                            <td class="collapsing">調查方式 :</td>
                            <td>{{ methods[information.method] }}</td>
                        </tr>
                        <tr>
                            <td class="collapsing">調查對象 :</td>
                            <td>{{ information.target_school }}</td>
                        </tr>
                        <tr>
                            <td class="collapsing">母體數量 :</td>
                            <td>{{ information.quantity_total }}</td>
                        </tr>
                        <tr ng-if="information.quantity_sample">
                            <td class="collapsing">抽樣數 :</td>
                            <td>{{ information.quantity_sample }}</td>
                        </tr>
                        <tr>
                            <td class="collapsing">回收數 :</td>
                            <td id="quantity_gets">{{ information.quantity_gets }}</td>
                        </tr>
                        <tr>
                            <td class="collapsing">回收率 :</td>
                            <td id="quantity_percent">{{ information.quantity_gets/information.quantity_total*100 || 0 }}%</td>
                        </tr>
                        <tr>
                            <td class="collapsing">問卷內容 :</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
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

</body>
</html>