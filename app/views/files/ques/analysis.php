<div ng-cloak ng-controller="analysisController" style="position: absolute;top: 10px;bottom: 10px;left: 10px; right: 10px">

	<div class="ui segment" ng-class="{loading: loading}">

	    <select class="ui dropdown" ng-options="question.title for question in questions" ng-model="question" ng-change="getFrequence(question)">
            <option value="">請選擇題目</option>
<!--             <option ng-repeat="question in questions" value="{{ type.type }}" ng-selected="{{type.type === question.type}}">{{ question.title }}</option> -->
        </select>

        <div>
        {{ question }}
        </div>

	</div>

</div>

<script>
angular.module('app')
.controller('analysisController', function($scope, $http, $filter) {

	$scope.questions = [];

        $http({method: 'POST', url: 'get_questions', data:{} })
        .success(function(data, status, headers, config) {      
			console.log(data);
			$scope.questions = data.questions;
        }).error(function(e){
            console.log(e);
        });

    $scope.getFrequence = function(question) {
        $http({method: 'POST', url: 'get_frequence', data:{question: question} })
        .success(function(data, status, headers, config) {      
			console.log(data);
			//$scope.questions = data.questions;
        }).error(function(e){
            console.log(e);
        });
    }; 

});
</script>