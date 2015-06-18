<div>        
<!--         <div ng-repeat="head in question.heads">
            <h1 class="ui header" ng-bind-html="head.title"></h1>
        </div> -->

        <div class="ui form" ng-if="question.type==='explain'">
            <h4 class="ui header" ng-bind-html="question.title" style="max-width: 700px"></h4>
        </div>   

        <div class="ui form" ng-if="question.type==='text'">
            <div class="field" ng-repeat="answer in question.answers">
                <label>{{ question.title }}</label>
                <div class="ui input">
                    {{ answer.title }}
                    <input type="text" placeholder="{{ answer.sub_title }}"
                           ng-style="{maxlength: answer.size, textsize: answer.size, size: answer.size}"
                           ng-model="db[question.id][$index]" ng-model-options="{ updateOn: 'blur' }"
                           ng-change="save_data(question)" />
                </div>
            </div>
        </div>


        <div class="ui basic segment" ng-if="question.type==='radio'">

            <!-- <h4 class="ui header" ng-bind-html="question.title" style="max-width: 700px"></h4> -->

            <div class="ui form">  
                <div class="grouped fields">
                    <label>{{ question.title }}</label>
                    <div class="field" ng-repeat="answer in question.answers">
                        <div class="ui radio checkbox">
                            <input type="radio" id="{{ question.id+'_'+$index }}" name="{{ question.id }}" ng-model="db[question.id]" ng-value="answer.value" ng-change="save_data(question)" />
                            <label for="{{ question.id+'_'+$index }}" ng-bind-html="answer.title"></label>
                        </div>                        
                    </div>
                </div>
            </div>

            <div class="ui vertical segment" ng-repeat="sub in question.subs | filter: {parent_value: db[question.id]}" question="sub" layer="layer+1"></div>

        </div>    


        <div class="ui form0" ng-if="question.type==='select'">
            <div class="six wide field0">
                <h5 class="ui header" style="max-width: 700px">{{ question.title }}</h5>
                <select class="ui dropdown" ng-model="db[question.id]" ng-init="db[question.id]='?'" ng-change="save_data(question)">
                    <option value="?">請選擇</option>
                    <option ng-repeat="answer in question.answers" ng-value="answer.value" ng-bind-html="answer.title"></option>
                </select>
            </div>
        </div>  


        <div class="ui form" ng-if="question.type==='textarea'">   
            <h4 class="ui header" ng-bind-html="question.title" style="max-width: 700px"></h4>         
            <div class="field" ng-repeat="answer in question.answers | valueToObject">
                <textarea style="resize: none" maxlength="{{ answer.value.size }}" placeholder="請勿輸入超過{{ answer.value.size }}個中文字"></textarea>
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
                            <div class="ui radio checkbox">
                                <input type="radio" id="{{ scale.id+'_'+$index }}" ng-model="db[scale.id]" ng-value="answer.value" ng-change="save_data(scale)" />
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
                    <div class="field" ng-repeat="checkbox in question.subs">
                        <div class="ui checkbox">
                            <input type="checkbox" id="{{ checkbox.id+'_'+$index }}" ng-model="db[checkbox.id]" ng-true-value="true" ng-false-value="false" ng-change="save_data(checkbox)" />
                            <label for="{{ checkbox.id+'_'+$index }}">{{ checkbox.title }}</label>
                        </div>
                    </div>
                </div>    
            </div> 
                
            <div class="ui segment" ng-repeat="checkbox in question.subs" ng-if="checkbox.subs.length > 0 && db[checkbox.id]">
                <a class="ui top left ribbon label">{{ checkbox.title }}</a>
                <div class="ui vertical segment" ng-repeat="sub in checkbox.subs" question="sub" layer="layer+1"></div>
            </div>
            
        </div> 


        <div ng-if="question.type==='table'"></div>
</div>