<div>
    <div ng-repeat="question in node.questions">
        <label>{{question.title}}</label>
        <md-select ng-model="answers[question.id]" placeholder="請選擇" ng-change="saveAnswer(question)">
            <md-option ng-repeat="answer in node.answers" ng-value="answer">{{answer.title}}</md-option>
        </md-select>
    </div>
</div>