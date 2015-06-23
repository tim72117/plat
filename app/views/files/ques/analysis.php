<div ng-cloak ng-controller="analysisController" style="position: absolute;top: 10px;bottom: 10px;left: 10px; right: 10px">

	<div class="ui segment" ng-class="{loading: loading}">

		<h3 class="ui header">{{ title }}</h3>
	    <select class="ui dropdown" ng-options="question.title for question in questions" ng-model="question" ng-change="getFrequence(question)">
            <option value="">請選擇題目</option>
<!--             <option ng-repeat="question in questions" value="{{ type.type }}" ng-selected="{{type.type === question.type}}">{{ question.title }}</option> -->
        </select>

        <div>
        {{ question1 }}
        </div>

        <table class="ui collapsing table">
        	<thead>
        		<tr>
        			<th class="right aligned">選項</th>
        			<th class="right aligned">數量</th>
                    <th class="right aligned">百分比</th>
        		</tr>
        	</thead>
	        <tbody>
	            <tr ng-repeat="answer in question.answers">
	            	<td class="right aligned" style="max-width:800px">{{ answer.title }}</td>
	                <td class="right aligned">{{ frequence[answer.value] ? frequence[answer.value] : 0 }}</td>
                    <td class="right aligned">{{ frequence[answer.value] ? ((100*frequence[answer.value]/total) | number:1) : 0 }}%</td>
	            </tr>
                <tr ng-if="frequence['n']">
                    <td class="right aligned">無須填答</td>
                    <td class="right aligned">{{ frequence['n'] }}</td>
                    <td class="right aligned">{{ (100*frequence['n']/total) | number:1 }}%</td>
                </tr> 
                <tr>
                    <td class="right aligned">總數</td>
                    <td class="right aligned">{{ total }}</td>
                    <td class="right aligned"></td>
                </tr>    
	        </tbody>
    	</table>

	</div>

</div>

<script>
angular.module('app')
.controller('analysisController', function($scope, $http, $filter) {

	$scope.questions = [];
	$scope.loading = false;

        $http({method: 'POST', url: 'get_questions', data:{} })
        .success(function(data, status, headers, config) {      
			//console.log(data);
			$scope.questions = data.questions;
			$scope.title = data.title;
        }).error(function(e){
            console.log(e);
        });

    $scope.getFrequence = function(question) {
    	if( !question )
    		return;

    	$scope.loading = true;
        $http({method: 'POST', url: 'get_frequence', data:{question: {name: question.name, page: question.page}} })
        .success(function(data, status, headers, config) {      
			//console.log(data);
			$scope.loading = false;
			$scope.frequence = data.frequence;
            $scope.total = 0 ;
            angular.forEach($scope.frequence, function(value) {
                $scope.total += value ? value*1 : 0;
            });
        }).error(function(e){
            console.log(e);
        });
    }; 

});
</script>