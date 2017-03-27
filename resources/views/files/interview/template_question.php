<div>
<div class="ui basic padded segment" ng-if="compareRule(question)">


    <div ng-if="question.is.type=='explain'">
        <h1 class="ui header" ng-bind-html="question.is.title"></h1>
    </div>


    <div ng-if="question.is.type=='radio'">
        <h1 class="ui header" ng-bind-html="question.is.title"></h1>
        <div class="ui form" ng-class="{loading: question.saving}">
            <div class="grouped fields">
                <div class="field md-display-1" ng-class="{disabled: question.disabled, error: question.saving}">
                    <md-radio-group ng-model="answers[question.id]" ng-change="save_answers(question)">
                        <md-radio-button ng-repeat="answer in question.answers" ng-disabled="!compareRule(answer)" ng-value="answer.id" class="md-primary">{{ answer.is.title }}</md-radio-button>
                    </md-radio-group>
                </div>
            </div>
        </div>
    </div>


    <div ng-if="question.is.type=='select'">
        <h1 class="ui header" ng-bind-html="question.is.title"></h1>
        <div class="ui form">
            <div class="field" ng-class="{disabled: question.disabled, error: question.saving}">
                <select class="ui dropdown" ng-options="answer.id as answer.is.title for answer in question.answers" ng-model="answers[question.id]" ng-change="save_answers(question)">
                    <option value="">請選擇</option>
                </select>
            </div>
        </div>
    </div>


    <div ng-if="question.is.type=='checkboxs'">
        <h1 class="ui header" ng-bind-html="question.is.title"></h1>
        <div ng-repeat="bQuestion in branchs | filter:{parent_question_id:question.id}:true">
            <div ng-repeat="answer in bQuestion.answers">
                <md-checkbox ng-model="answers[bQuestion.id]" ng-true-value="'{{answer.id}}'" ng-false-value="null" ng-change="save_answers(bQuestion)" class="md-primary md-display-1">
                    {{ bQuestion.is.title }}
                </md-checkbox>
            </div>
        </div>
    </div>


    <div ng-if="question.is.type=='scales'">
        <h1 class="ui header" ng-bind-html="question.is.title"></h1>
        <table class="ui collapsing celled table">
            <thead ng-repeat-start="sQuestion in branchs | filter:{parent_question_id:question.id}:true" ng-if="$index%5==0">
                <tr>
                    <th></th>
                    <th></th>
                    <th ng-repeat="answer in question.answers" class="top aligned" ng-bind-html="answer.is.title"></th>
                </tr>
            </thead>
            <tbody ng-repeat-end>
                <tr ng-class="{disabled: sQuestion.disabled}">
                    <td class="collapsing">{{ $index+1 }}</td>
                    <td><h3 class="ui header" ng-bind-html="sQuestion.is.title"></h3></td>
                    <td class="collapsing center aligned" ng-repeat="answer in sQuestion.answers">
                        <div class="ui form">
                            <div class="field" ng-class="{error: sQuestion.saving}">
                                <div class="ui radio fitted checkbox">
                                    <input type="radio" id="{{::$id}}" ng-model="answers[sQuestion.id]" ng-value="answer.id" ng-change="save_answers(sQuestion)" />
                                    <label for="{{::$id}}"></label>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>


    <div ng-if="question.is.type=='texts'">
        <h1 class="ui header" ng-bind-html="question.is.title"></h1>
        <md-input-container class="md-icon-float md-block" ng-repeat="bQuestion in branchs | filter:{parent_question_id:question.id}:true">
            <label ng-bind-html="bQuestion.is.title"></label>
            <md-icon><i class="write icon"></i></md-icon>
            <input type="text" ng-model="answers[bQuestion.id]" ng-model-options="saveTextNgOptions" ng-required="bQuestion.required" ng-disabled="bQuestion.disabled||bQuestion.saving" ng-change="save_answers(bQuestion)">
            <div class="hint" ng-show="showHints">{{ bQuestion.answers[0].is.title }}</div>
        </md-input-container>
    </div>


    <div ng-if="question.is.type=='list'">
        <h1 class="ui header" ng-bind-html="question.is.title"></h1>
        <div ng-repeat="bQuestion in branchs | filter:{parent_question_id:question.id}:true" question="bQuestion" branchs="branchs" childrens="childrens"></div>
    </div>


    <div ng-if="question.is.type=='textarea'"></div>


    <div ng-if="question.is.type=='table'"></div>


    <div ng-repeat="cQuestion in childrens | filter:{parent:{question_id:question.id}}:true">
        <div ng-if="cQuestion.parent.id==answers[question.id]" question="cQuestion" branchs="branchs" childrens="childrens"></div>
    </div>


    <div ng-if="question.is.type=='checkboxs'" ng-repeat="bQuestion in branchs | filter:{parent_question_id:question.id}:true">
        <div ng-repeat="cQuestion in childrens | filter:{parent:{question_id:bQuestion.id}}:true">
            <div ng-if="cQuestion.parent.id==answers[bQuestion.id]" question="cQuestion" branchs="branchs" childrens="childrens"></div>
        </div>
    </div>


</div>
</div>