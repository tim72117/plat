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
.controller('quesController', function quesController($scope, $http, $filter, $window){
    
    $scope.pages = [];
    $scope.page = {};
    $scope.db = {};
    
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
        if( index < $scope.pages.length )
            $scope.page = $scope.pages[++index];
    };
    
    $scope.prev_page = function() {
        var index = $scope.pages.indexOf($scope.page);
        if( index > 0 )
            $scope.page = $scope.pages[--index];
    }; 
    
    if( !navigator.onLine ){
        
        $scope.questions = angular.fromJson(window.localStorage.getItem('questions'));
        
    }else{
        
        $http({method: 'POST', url: 'get_ques_from_db_new', data:{} })
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.pages = data;
            $scope.page = $scope.pages[9];
            window.localStorage.setItem('pages', angular.toJson(data));     
        }).error(function(e){
            console.log(e);
        });
        
    }
    

})
.factory('dbService', [function() {
    return {
        db: {}
    };
}])
.directive('question', ['$compile', 'dbService', function($compile, dbService){
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

                scope.db = dbService.db;
                console.log(dbService);
            };          
        },
        link: function(scope, element, attrs) {
            //console.log(scope);
            
        },
        controller: function($scope, $http, $window, $filter) {
            
            $scope.get_explain = function() {
                var qq = $filter('filter')($scope.questions, {type: '!explain'});
            };              
            
            $scope.getDataFromWebStorage = function() {
                if( $scope.$parent.online ) {   
                    
                }else{
                    $scope.db = angular.fromJson(window.localStorage.getItem('ques_data'));
                }                
            };

            $scope.fromJson = function(json) {
                console.log(json);
                return angular.fromJson(json);
            }
            
            $scope.getDataFromWebStorage();
            
            $scope.save_data = function(question) {
                
                //console.log($scope.db[question.id]);
                
                if( navigator.onLine ) {                    
                
                    // $http({method: 'POST', url: 'save_answer_data', data:{id: question.id, input: $scope.db[question.id]} })
                    // .success(function(data, status, headers, config) {
                    //     console.log(data);
                    // }).error(function(e){
                    //     console.log(e);
                    // });
                    
                }else{
                    
                    //$scope.db[question.id] = input;
                    
                    //var item = window.localStorage ? window.localStorage.setItem('ques_data', angular.toJson($scope.db)) : null;
                    
                    //console.log($scope.db);
                    
                }
            };
                        
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