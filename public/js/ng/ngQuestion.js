angular.module('ngQuestion',
    [
        'ngQuestion.services',
        'ngQuestion.directives'
    ]);

angular.module('ngQuestion.services', [])
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
}]);

angular.module('ngQuestion.directives', [])
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
}]);