<!DOCTYPE html>
<html manifest="cache_manifest" xml:lang="zh-TW" lang="zh-TW" ng-app="app">
<head>
<meta charset="utf-8" />
<!--[if lt IE 9]><script src="/js/html5shiv.js"></script><![endif]-->
<script src="/js/angular-1.3.14/angular.min.js"></script>
<script src="/js/angular-1.3.14/angular-sanitize.min.js"></script>

<!--<link rel="stylesheet" href="/editor/css/demo/page_struct.css" />
<link rel="stylesheet" href="/editor/css/demo/page_design.css" />-->
<!--<link rel="stylesheet" href="/editor/css/demo/input.css" />-->

<link rel="stylesheet" href="/css/ui/Semantic-UI-1.11.4/semantic.min.css" />

<script>
angular.module('app', ['ngSanitize'])
.controller('quesController', function quesController($scope, $http, $filter, $window){
    
    $scope.step = 8;
    
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
       
    $scope.next_ques = function() {        
        if( $scope.step+1 < $scope.pages[$scope.page].data.length ) {
            $scope.step++;
        }else{
            $scope.next_page();
        }         
    };
    
    $scope.prev_ques = function() {
        if( $scope.step > 0 ) {
            $scope.step--;
        }else{
            $scope.prev_page();
        }            
    }; 
    
    $scope.next_page = function() {
        if( $scope.page < $scope.pages.length-1 ) {
            $scope.pages[$scope.page].selected = false;
            $scope.pages[++$scope.page].selected = true;
            $scope.step = 0;
        }
    };
    
    $scope.prev_page = function() {
        if( $scope.page > 0 ) {
            $scope.pages[$scope.page].selected = false;
            $scope.pages[--$scope.page].selected = true;
            $scope.step = $scope.pages[$scope.page].data.length-1;
        }
    }; 
    
    if( !navigator.onLine ){
        
        $scope.questions = angular.fromJson(window.localStorage.getItem('questions'));
        
    }else{
        
        $http({method: 'POST', url: 'get_ques_from_db', data:{} })
        .success(function(data, status, headers, config) {
            $scope.pages = data;
            $scope.page = 2;
            $scope.pages[$scope.page].selected = true;
            window.localStorage.setItem('pages', angular.toJson(data));     
        }).error(function(e){
            
        });
        
    }
    

})
.directive('questions', function(){
    return {
        restrict: 'E',
        replace: true,
        transclude: false,
        scope: {questions: '=data', layer: '=layer', step: '='},
        template: '<div ng-include src="\'template_demo\'"></div>',
        link: function(scope, element, attrs) {
            //console.log(scope);
        },
        controller: function($scope, $http, $window, $filter) {
            
            $scope.get_explain = function() {
                var qq = $filter('filter')($scope.questions, {type: '!explain'})[$scope.step];
            };         

            
            $scope.db = {};
            
            $scope.getDataFromWebStorage = function() {
                if( $scope.$parent.online ) {   
                    
                }else{
                    $scope.db = angular.fromJson(window.localStorage.getItem('ques_data'));
                }                
            };
            
            $scope.getDataFromWebStorage();
            
            $scope.save_data = function(question) {
                
                console.log($scope.db[question.id]);
                
                if( navigator.onLine ) {                    
                
                    $http({method: 'POST', url: 'save_answer_data', data:{id: question.id, input: $scope.db[question.id]} })
                    .success(function(data, status, headers, config) {
                        console.log(data);
                    }).error(function(e){
                        console.log(e);
                    });
                    
                }else{
                    
                    //$scope.db[question.id] = input;
                    
                    //var item = window.localStorage ? window.localStorage.setItem('ques_data', angular.toJson($scope.db)) : null;
                    
                    //console.log($scope.db);
                    
                }
            };
                        
        }
    };    
});
</script>
<style>

</style>
</head>
<body>
<div ng-controller="quesController" style="position: absolute;top: 10px;bottom: 10px;left: 10px; right: 10px">

    <div class="ui segment" style="position:absolute;left:0;right:0;top:0;bottom:0;overflow: auto">

        {{ page+1 }}/{{ pages.length }}
                <div class="ui basic button" ng-click="prev_page()">
                    上一部分
                </div>

                <div class="ui basic button" ng-click="next_page()">
                    下一部分
                </div>

                <div class="ui basic button" ng-click="prev_ques()">
                    上一題
                </div>

                <div class="ui basic button" ng-click="next_ques()">
                    下一題{{ step+1 }}
                </div>
        
                <div  class="ui form">
                    <div ng-repeat="page in pages | filter:{selected: true}">
                        <questions data="page.data" layer="0" step="step"></questions>
                    </div>   
                </div>




        <div>
            <span ng-show="online" style="color:green">online</span>
            <span ng-hide="online" style="color:red">offline</span>
        </div>

    </div>


</div>
</body>
</html>