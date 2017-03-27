<div ng-cloak ng-controller="quesController" style="position: absolute;top: 10px;bottom: 10px;left: 10px; right: 10px;max-width: 800px">

    <div class="ui segment" ng-class="{loading: questionLoading}" style="position:absolute;left:0;right:0;top:0;bottom:0;overflow: auto">
      
        <form class="ui form">
            <div class="field" ng-repeat="question in questions">
                <!-- <div question="question" layer="0" step="step"></div> -->

                <div class="ui segment">
                    
                    <h4 class="ui header" ng-bind-html="question.title"></h4>

                    <div ng-if="question.type==='radio'">
                        
                        <div ng-repeat="answer in question.answers">
                            
                            <div class="ui radio checkbox">
                                <input type="radio" id="ques-{{ question.id }}-ans-{{ answer.value }}" name="ques-{{ question.id }}" ng-model="answers[question.id]" ng-value="answer.value" />
                                <label for="ques-{{ question.id }}-ans-{{ answer.value }}" ng-bind-html="answer.title"></label>
                            </div>
                            
                        </div>

                    </div>
                </div>    

            </div> 

            <div class="ui positive button" ng-click="save_answers()">送出</div> 
        </form>

    </div>

</div>

<script>
app.controller('quesController', function quesController($scope, $http, $filter, $window) {
    $scope.questions = [];
    $scope.answers = [];
    $scope.questionLoading = false;
    
    $scope.get_ques = function() {
        $scope.questionLoading = true;
        $http({method: 'POST', url: 'get_ques_from_db_new', data:{} })
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.questions = data; 
            $scope.questionLoading = false;
        }).error(function(e){
            
        });
        
    }

    $scope.get_ques();   

    $scope.save_answers = function(question) {
        
        console.log($scope.answers);
        
        $http({method: 'POST', url: 'save_answers', data:{answers: $scope.answers} })
        .success(function(data, status, headers, config) {
            console.log(data);
        }).error(function(e){
            console.log(e);
        });
    };

})
.directive('question', function(){
    return {
        restrict: 'A',
        replace: true,
        transclude: false,
        scope: {question: '=question', layer: '=layer', step: '='},
        template: '<div ng-include src="\'template_demo\'"></div>',
        link: function(scope, element, attrs) {
            //console.log(scope);
        },
        controller: function($scope, $http, $window, $filter) {
        }
    };    
});
</script>
<style>

</style>
