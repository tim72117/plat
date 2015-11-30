angular.module('ngQuestion',
    [
        'ngQuestion.services',
        'ngQuestion.directives'
    ]);

angular.module('ngQuestion.services', [])
.factory('questionService', ['$http', function($http) {
    var answers = {};
    var pageInfo;
    var key;
    return {
        answers: answers,
        setPage: function(v) { page = v; },
        setUser: function(v) { user_id = v; },
        setVisit: function(v) { visit = v;},
        setFile_id: function(v) { file_id = v;},
        setAnswers: function(values) { 
            for (var i in answers) delete answers[i];
            angular.forEach(values, function(v, k) {
                answers[k] = v;
            });
        },
        getPage: function(page) { 
            if( !navigator.onLine  ) {
                pages = angular.fromJson(window.localStorage.getItem('pages' + page.doc_id)) || [];
                if( pages.length == 0 )
                    return pages;

                key = '';

                for (var i in page) key += i+page[i];  

                var values = angular.fromJson(window.localStorage.getItem(key)) || {};

                for (var i in answers) delete answers[i];

                angular.forEach(values, function(v, k) {
                    answers[k] = v;
                });

                var pageAnswers = angular.fromJson(window.localStorage.getItem('pageAnswers')) || {};            

                if( !pageAnswers.hasOwnProperty(key) ) {
                    pageAnswers[key] = page;
                    
                    window.localStorage.setItem('pageAnswers', angular.toJson(pageAnswers));
                    console.log(angular.fromJson(window.localStorage.getItem('pageAnswers')));
                }   

                return pages;
            }else{


            }

        },
        save: function(question) {
            
            if( navigator.onLine ) { 
                console.log(file_id);
                $http({method: 'POST', url: 'ajax/save_answers', data:{visit_id:visit.id, baby_id: visit.baby_id, page_id: page.id, file_id: file_id, ques_id: question.id, answer: answers[question.id]} })
                .success(function(data, status, headers, config) {
                    console.log(data);
                }).error(function(e){
                    console.log(e);
                });
                
            }else{              
            if( !navigator.onLine ) {             
                
                var item = window.localStorage ? window.localStorage.setItem(key, angular.toJson(answers)) : null;
                
            }}
        }
    };
}]);

angular.module('ngQuestion.directives', [])
.directive('question', ['$compile', 'questionService', function($compile, questionService) {
    return {
        restrict: 'A',
        replace: true,
        transclude: false,
        scope: {question: '=question', layer: '=layer'},
        templateUrl: 'ajax/template_demo',
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
                
                scope.answers = questionService.answers;
                scope.save_answers = questionService.save;
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