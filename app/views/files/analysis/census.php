<!DOCTYPE html>
<html xml:lang="zh-TW" lang="zh-TW" ng-app="app">
<head>
<meta charset="utf-8" />
<title><?=$doc->isFile->title?></title>

<!--[if lt IE 9]><script src="/js/html5shiv.js"></script><![endif]-->
<script src="/js/angular/1.5.3/angular.min.js"></script>

<link rel="stylesheet" href="/css/Semantic-UI/2.1.8/semantic.min.css" />

<script>
var app = angular.module('app', []);
app.controller('analysisController', function($scope, $filter, $interval, $http) {
    $scope.docs = [];
    $scope.doc = {};
    $scope.clouds = 'C10';
    $scope.information = {};
    $scope.methods = {census: '普查', sampling: '抽樣調查'};
    $scope.types = [{key: 'C10', title: '高一專一學生'}, {key: 'C11', title: '高二專二學生'}, {key: 'C10P', title: '高二專二家長調查'}];
    //$scope.types = [{key: 'GT0', title: '新進師資生調查'}, {key: 'FT', title: '實習師資生調查'}, {key: 'YB', title: '統計年報'}];

    $scope.allCensus = function() {
        $scope.loading = true;
        $http({method: 'POST', url: 'all_census', data:{}})
        .success(function(data, status, headers, config) {
            $scope.docs = data.docs;
            var docs = $filter('filter')($scope.docs, {selected: true});
            if (docs.length > 0) {
                $scope.selectDoc(docs[0]);
                $scope.clouds = $scope.doc.is_file.analysis.target_people;
            }
            $scope.loading = false;
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.selectDoc = function(doc) {
        $scope.file = doc.analysis.file;
        $scope.information = doc.analysis;
        $scope.doc.selected = false;
        $scope.doc = doc;
        doc.selected = true;
    };

    $scope.enterDoc = function() {
        if ($scope.doc.id) {
            location.replace('/doc/' + $scope.doc.id + '/menu');
        };
    };

    $scope.allCensus();
});

</script>
</head>

<body ng-cloak ng-controller="analysisController">

    <div class="ui inverted dimmer" ng-class="{active: loading}">
        <div class="ui text loader">Loading</div>
    </div>

    <div class="ui container">

        <div class="ui basic segment">
            <div class="ui small breadcrumb">
                <a class="section" href="/project/<?=$project->code?>/intro">查詢平台</a>
                <i class="right chevron icon divider"></i>
                <div class="active section">選擇資料庫</div>
            </div>
        </div>

        <div class="ui grid">

            <div class="five wide column">
                <div class="ui vertical fluid large menu">
                    <div class="item" ng-repeat="type in types">
                        <div class="header">{{ type.title }}</div>
                        <div class="menu">
                            <a class="item" ng-repeat="doc in docs | filter: {analysis: {target_people: type.key}}:true | orderBy:'analysis.code_year'" ng-class="{active: doc.selected}" ng-click="selectDoc(doc)">
                                {{ doc.is_file.title }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="eleven wide column" style="min-height:550px">

                <table class="ui table">
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <h3 class="ui header">{{  doc.is_file.title }}</h3>
                                <button class="ui olive button" ng-class="{disabled: (docs | filter: {selected: true}).length < 1}" ng-click="enterDoc()">
                                    <i class="puzzle icon"></i> 進入資料庫
                                </button>
                            </td>
                        </tr>
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
                            <td id="quantity_percent">{{ information.quantity_gets/information.quantity_total*100 || 0 | number : 2 }}%</td>
                        </tr>
                        <tr>
                            <td class="collapsing">問卷內容 :</td>
                            <td><a href="{{file.path}}">{{file.name}}</a></td>
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