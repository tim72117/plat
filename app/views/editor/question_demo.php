<div>
<!--         <div ng-repeat="head in question.heads">
            <h1 class="ui header" ng-bind-html="head.title"></h1>
        </div> -->

        <div class="ui form" ng-if="question.type==='explain'">
            <h4 class="ui header" ng-bind-html="question.title" style="max-width: 700px"></h4>
        </div>


        <div class="ui form" ng-if="question.type==='text'">
            <div class="field" >
                <label>{{ question.title }}</label>
                <div class="fields" ng-class="{disabled: question.disabled}">
                    <div class="field" ng-repeat="answer in question.answers | valueToObject">
                        <label>{{ answer.title }}</label>
                        <div class="ui input">
                            <input type="{{ answer.value ? answer.value.type : 'text' }}" placeholder="{{ answer.sub_title }}"
                                   ng-style="{maxlength: answer.size, textsize: answer.size, size: answer.size}"
                                   ng-model="answers[question.id][$index]" ng-model-options="{ updateOn: 'blur' }"
                                   ng-change="save_answers(question)" ng-disabled="question.disabled"  />
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="ui basic segment" ng-if="question.type==='radio'" >

            <!-- <h4 class="ui header" ng-bind-html="question.title" style="max-width: 700px"></h4> -->
<!-- ng-class="{disabled: question.disabled}" -->
            <div class="ui form" >
                <div class="grouped fields">
                    <label>{{ question.title }}</label>
                    <div class="field" ng-class="{disabled: question.disabled}" ng-repeat="answer in question.answers">
                        <div class="ui radio checkbox" >
                            <input type="radio" ng-disabled="question.disabled" id="{{ question.id+'_'+$index }}" name="{{ question.id }}" ng-model="answers[question.id]" ng-value="answer.value" ng-change="save_answers(question)" />
                            <label for="{{ question.id+'_'+$index }}" ng-bind-html="answer.title"></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ui vertical segment" ng-repeat="sub in question.subs | filter: {parent_value: answers[question.id]}" question="sub" layer="layer+1"></div>

        </div>


        <div class="ui form0" ng-if="question.type==='select'">
            <div class="six wide field0">
                <h5 class="ui header" style="max-width: 700px">{{ question.title }}</h5>
                <select class="ui dropdown" ng-class="{disabled: question.disabled}" ng-options="answer.value as answer.title for answer in question.answers" ng-model="answers[question.id]" ng-change="save_answers(question)">
                    <option value="">請選擇</option>
                </select>
            </div>
        </div>


        <div class="ui form" ng-if="question.type==='textarea'">
            <h4 class="ui header" ng-bind-html="question.title" style="max-width: 700px"></h4>
            <div class="field" ng-class="{disabled: question.disabled}" ng-repeat="answer in question.answers | valueToObject" >
                <textarea style="resize: none" ng-disabled="question.disabled" maxlength="{{ answer.value.size }}" placeholder="請勿輸入超過{{ answer.value.size }}個中文字"></textarea>
            </div>
        </div>


        <div class="ui basic segment1" ng-if="question.type==='list'">
            <div class="ui form">
                <div class="field" ng-repeat="sub in question.subs">
                    <!-- <label>{{ sub.title }}</label> -->
                    <div question="sub" layer="layer+1"></div>
                </div>
            </div>
        </div>


        <div class="ui basic segment" ng-if="question.type==='scale'">
            <h4 class="ui header" ng-bind-html="question.title" style="max-width: 700px"></h4>
            <table class="ui collapsing definition compact table">
                <thead>
                    <tr>
                        <th></th>
                        <th ng-repeat="answer in question.answers" style="max-width:2em;vertical-align:bottom"><div ng-bind-html="answer.title"></div></th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="scale in question.subs">
                        <td><div style="max-width:450px">({{ $index+1 }})<span ng-bind-html="scale.title"></span></div></td>
                        <td ng-repeat="answer in question.answers" style="max-width:2em">
                            <div class="ui radio checkbox" >
                                <input type="radio" ng-disabled="question.disabled" id="{{ scale.id+'_'+$index }}" ng-model="answers[scale.id]" ng-value="answer.value" ng-change="save_answers(scale)" />
                                <label for="{{ scale.id+'_'+$index }}" class="scale"></label>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>


        <div class="ui form1" ng-if="question.type==='checkbox'">

            <div class="ui form">
                <div class="grouped fields">
                    <label style="max-width: 700px">{{ question.title }}</label>
                    <div class="field" ng-class="{disabled: question.disabled}" ng-repeat="checkbox in question.subs">
                        <div class="ui checkbox" >
                            <input type="checkbox" ng-disabled="checkbox.disabled" id="{{ checkbox.id+'_'+$index }}" ng-model="answers[checkbox.id]" ng-true-value="'1'" ng-false-value="'0'" ng-change="save_answers(checkbox)" />
                            <label for="{{ checkbox.id+'_'+$index }}">{{ checkbox.title }}</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ui segment" ng-disabled="question.disabled" ng-repeat="checkbox in question.subs" ng-if="checkbox.subs.length > 0 && answers[checkbox.id]">
                <a class="ui top left ribbon label">{{ checkbox.title }}</a>
                <div class="ui vertical segment" ng-repeat="sub in checkbox.subs" question="sub" layer="layer+1"></div>
            </div>

        </div>


        <div ng-if="question.type==='table'"></div>
</div>