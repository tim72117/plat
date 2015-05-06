<!DOCTYPE html>
<html manifest="cache_manifest" xml:lang="zh-TW" lang="zh-TW" ng-app="app">
<head>
<meta charset="utf-8" />
<!--[if lt IE 9]><script src="/js/html5shiv.js"></script><![endif]-->
<script src="/js/angular-1.3.14/angular.min.js"></script>
<script src="/js/angular-1.3.14/angular-sanitize.min.js"></script>

<link rel="stylesheet" href="/editor/css/demo/page_struct.css" />
<link rel="stylesheet" href="/editor/css/demo/page_design.css" />
<link rel="stylesheet" href="/editor/css/demo/input.css" />

<script>
angular.module('app', ['ngSanitize'])
.controller('quesController', quesController)
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
function quesController($scope, $http, $filter, $window){
    
    $scope.step = 0;
    
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
            $scope.page = 0;
            $scope.pages[$scope.page].selected = true;
            window.localStorage.setItem('pages', angular.toJson(data));     
        }).error(function(e){
            
        });
        
    }
    

}
</script>
<style>
.file-btn {
    cursor: pointer; 
    text-align: center;
    border: 1px solid #aaa;
    color:#555;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-repeat: no-repeat;
    background-position: center;
    box-sizing: border-box;
}
.file-btn:hover {
    border: 1px solid #888;
    color: #000;
    background-color: #eee;
}
</style>
</head>
<body>
    <div id="building" ng-controller="quesController" style="color:#000">
        
        <div style="position: absolute;top:0;right:0;bottom:0;left:0">

            <div style="position: absolute;top:40px;right:300px;bottom:0;left:0;overflow-y: auto">
                <div id="contents">

                    <div ng-repeat="page in pages | filter:{selected: true}">
                        <questions data="page.data" layer="0" step="step"></questions>
                    </div>              

                </div>
            </div>
            
            <div style="width:300px;position: absolute;top:0;right:0;height:50px">
                <div class="file-btn" style="width:150px;height:50px;position: absolute;left:0;border-width:0 0 0 1px" ng-click="prev_page()">
                    上一部分
                </div>
                <div class="file-btn" style="width:150px;height:50px;position: absolute;right:0;border-width:0 0 0 1px" ng-click="next_page()">
                    下一部分{{ page+1 }}/{{ pages.length }}
                </div>                
            </div>
            <div style="width:300px;position: absolute;top:50px;bottom:0;right:0">
                <div class="file-btn" style="width:300px;position: absolute;top:0;bottom:50%;right:0;border-width:1px 0 0 1px;background-image: url(/images/demo/prev-256.png)" ng-click="prev_ques()">
                    上一題
                </div>
                <div class="file-btn" style="width:300px;position: absolute;top:50%;bottom:0;right:0;border-width:1px 0 0 1px;background-image: url(/images/demo/next-256.png)" ng-click="next_ques()">
                    下一題{{ step+1 }}
                </div>
            </div>
        </div>

        <div style="position: absolute;top:0;right:150px;left:0;height:40px;text-align: center">
            <span ng-show="online" style="color:green">online</span>
            <span ng-hide="online" style="color:red">offline</span>
        </div>

        
    </div>
</body>
</html>