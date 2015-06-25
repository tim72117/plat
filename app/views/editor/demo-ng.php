<!DOCTYPE html>
<html xml:lang="zh-TW" lang="zh-TW" ng-app="app"><!-- manifest="cache_manifest" -->
<head>
<meta charset="utf-8" />
<!--[if lt IE 9]><script src="/js/html5shiv.js"></script><![endif]-->
<script src="/js/angular-1.3.14/angular.min.js"></script>
<script src="/js/angular-1.3.14/angular-sanitize.min.js"></script>

<link rel="stylesheet" href="/css/ui/Semantic-UI-1.12.3/semantic.min.css" />

<script>
angular.module('app', ['ngSanitize'])
.controller('quesController', function quesController($scope, $http, $filter, $window, dbService){
    
    $scope.pages = [];
    $scope.page = {}; 
    
    $scope.online = navigator.onLine;
    
    $window.addEventListener("offline", function () {
        $scope.$apply(function() {
            $scope.online = false;
        });
    }, false);

    $window.addEventListener("online", function () {
        $scope.$apply(function() {
            $scope.online = true;
        });
    }, false);

    $scope.next_page = function() {
        var index = $scope.pages.indexOf($scope.page);
        if( index < $scope.pages.length-1 )
            $scope.page = $scope.pages[++index];
    };
    
    $scope.prev_page = function() {
        var index = $scope.pages.indexOf($scope.page);
        if( index > 0 )
            $scope.page = $scope.pages[--index];
    }; 

    $scope.$watch('page', function() {
        dbService.setPage($scope.page);
    });
    
    if( !navigator.onLine ){
        
        $scope.questions = angular.fromJson(window.localStorage.getItem('questions'));
        
    }else{
        
        $http({method: 'POST', url: 'get_ques_from_db', data:{} })
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.pages = data;
            $scope.page = $scope.pages[2];
            window.localStorage.setItem('pages', angular.toJson(data));     
        }).error(function(e){
            console.log(e);
        });
        
    }

    $scope.getDataFromWebStorage = function() {

        $http({method: 'POST', url: 'get_answers', data:{} })
        .success(function(data, status, headers, config) {
            console.log(data);   
            dbService.setAnswers(data.answers);
            //dbService.answers = data.answers;
        }).error(function(e){
            console.log(e);
        });

        if( $scope.$parent.online ) {   
            console.log('online');
        }else{
            $scope.answers = angular.fromJson(window.localStorage.getItem('ques_data'));
        }                
    };

    $scope.getDataFromWebStorage();

    $scope.answers = dbService.answers;

})
.factory('dbService', ['$http', function($http) {
    var answers = {};
    var page = {};
    return {
        answers: answers,
        setPage: function(v) { page = v; },
        setAnswers: function(values) { 
            //answers = v;
            angular.forEach(values, function(v, k) {
                answers[k] = v;
            });
        },
        save: function(question) {
            if( navigator.onLine ) {        

                console.log(answers);console.log(question.id);
            
                $http({method: 'POST', url: 'save_answers', data:{page_id: page.id, ques_id: question.id, answer: answers[question.id]} })
                .success(function(data, status, headers, config) {
                    console.log(data);
                }).error(function(e){
                    console.log(e);
                });
                
            }else{
                
                //$scope.answers[question.id] = input;
                
                //var item = window.localStorage ? window.localStorage.setItem('ques_data', angular.toJson($scope.answers)) : null;
                
                //console.log($scope.answers);
                
            }
        }
    };
}])
.directive('question', ['$compile', 'dbService', function($compile, dbService) {
    return {
        restrict: 'A',
        replace: true,
        transclude: false,
        scope: {question: '=question', layer: '=layer'},
        templateUrl: 'template_demo',
        //template: '<div ng-include src="\'template_demo\'"></div>',
        compile: function(tElement, tAttr) {            
            var contents = tElement.contents().remove();
            var compiledContents;

            return function(scope, iElement, iAttr) {
                if( !compiledContents )
                    compiledContents = $compile(contents);

                compiledContents(scope, function(clone, scope) {
                    iElement.append(clone); 
                });
                
                scope.answers = dbService.answers;
                scope.save_answers = dbService.save;
            };          
        },
        controller: function($scope, $http, $window, $filter) {
            
            $scope.get_explain = function() {
                var qq = $filter('filter')($scope.questions, {type: '!explain'});
            }; 

            $scope.fromJson = function(json) {
                console.log(json);
                return angular.fromJson(json);
            }
                        
        }
    };    
}])
.filter('valueToObject', function() {
    return function(answers) {
        angular.forEach(answers, function(answer) {
            answer.value = angular.fromJson(answer.value);
        });        
        return answers;
    };
});
</script>
<style>

</style>
</head>
<body>
<div class="ui compact basic segment" ng-controller="quesController" style="margin: 0 auto;min-width: 650px">

    <div class="ui menu">

        <div class="item">
            {{ page.value }}/{{ pages.length }}
        </div>

        <div class="item">
            <div class="ui basic button" ng-click="prev_page()">上一頁</div>
            <div class="ui basic button" ng-click="next_page()">下一頁</div>
        </div>       

        <div class="item">
            <span ng-show="online" style="color:green">online</span>
            <span ng-hide="online" style="color:red">offline</span>
        </div>

    </div>

    <div class="ui segment" ng-repeat="question in page.questions">
        <div class="field">
            <!-- <h4 class="ui header" ng-bind-html="question.title" style="max-width: 700px"></h4> -->
            
            <div question="question" layer="0"></div>
        </div>        
    </div>

</div>
</body>
</html>