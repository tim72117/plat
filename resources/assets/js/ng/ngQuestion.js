angular.module('ngQuestion', [
    'ngQuestion.services',
    'ngQuestion.directives'
]);

angular.module('ngQuestion.services', [])

.factory('questionService', ['$http', function($http) {
    var answers = {};
    var book = {};
    var record = {};
    return {
        answers: answers,
        setBook: function(v) { book = v; },
        setRecord: function(v) { record = v; },
        setAnswers: function(values) {
            for (var i in answers) delete answers[i];
            angular.forEach(values, function(answer, k) {
                answers[k] = (answer.string || answer.string=='') ? answer.string : answer.id;
            });
        },
        save: function(question, callback) {
            if (question.is.type=='text') {
                var answer = {id: question.answers[0].id, string: answers[question.id]};
            } else {
                var answer = {id: answers[question.id], string: null};
            }
            question.saving = true;
            $http({method: 'POST', url: 'saveAnswer', data:{book: book, record: record, question: question, answer: answer}})
            .success(function(data, status, headers, config) {
                angular.forEach(data.deletedAnswers, function(deletedAnswer) {
                    if (answers[deletedAnswer]) {
                        delete answers[deletedAnswer];
                    };
                });
                if (answers[question.id] == data.string || data.id) {
                    question.saving = false;
                };
                (callback) && callback(data);
            }).error(function(e){
                console.log(e);
            });
        }
    };
}]);

angular.module('ngQuestion.directives', [])

.directive('question', function($compile, questionService, $templateCache) {
    return {
        restrict: 'A',
        replace: true,
        transclude: false,
        scope: {question: '=', branchs: '=', childrens: '='},
        compile: function(tElement, tAttr) {
            var contents = tElement.contents().remove();
            var compiledContents;

            return function(scope, iElement, iAttr) {
                if (!compiledContents) {
                    compiledContents = $compile($templateCache.get('ng-question'));
                }
                compiledContents(scope, function(clone, scope) {
                    iElement.append(clone);
                });

                if (scope.question.hiding) {
                    iElement.remove();
                }
            };
        },
        controller: function($scope, $http, $window, $filter, $rootScope) {
            $scope.saveTextNgOptions = {updateOn: 'default blur', debounce:{default: 2000, blur: 0}};
            $scope.answers = questionService.answers;

            $scope.save_answers = function(question) {
                question.error = true;
                questionService.save(question);
            }

            $scope.$on('$destroy', function() {
                $scope.setConfirm(false);
            });

            $scope.setConfirm = function(confirm) {
                if ($scope.question.is.type == 'select' || $scope.question.is.type == 'radio') {
                    $scope.question.confirm = confirm;
                };
                if ($scope.question.is.type == 'scales') {
                    angular.forEach($filter('filter')($scope.branchs, {parent_question_id: $scope.question.id}, true), function(question) {
                        question.confirm = confirm;
                    });
                };
            };

            $scope.compareRule = function(question) {
                var show = true;
                if (question.rules.length > 0) {
                    angular.forEach(question.rules, function(rule){
                        var parameter = rule.is.parameters[0];
                        var keys = Object.keys(parameter);
                        if ($scope.answers[keys[0]] == parameter[keys[0]]) {
                            show = false;
                        }
                    });
                }
                $scope.setConfirm(show);
                question.show = show;
                return show;
            };
        }
    };
});