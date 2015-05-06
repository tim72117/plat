<div class="main" ng-repeat="question in questions" ng-if="($index===step||layer!==0)" style="">
                
    <div id="'.$question->id.'" parrent="'.$parrent.'" class="layer'.$layer.'" style="">
        
        <div class="main" ng-repeat="head in question.heads">
            <h4 class="title" style="" ng-bind-html="head.title"></h4>
        </div>
        <h4 class="title" style="" ng-bind-html="question.title"></h4>

        <div class="fieldA" init="" style="overflow : auto;position:static">
            
            <div ng-if="question.type==='radio'" ng-repeat="answer in question.answers">
                <p class="radio">
                    <input type="radio" class="qcheck" id="{{ question.id+'_'+$index }}" ng-model="db[question.id]" ng-value="answer.value" ng-change="save_data(question)" />
                    <label for="{{ question.id+'_'+$index }}" class="radio" ng-bind-html="answer.title"></label>
                </p>
                <div ng-if="answer.subs.length>0 && db[question.id]===answer.value">
                    <questions data="answer.subs" layer="layer+1"></questions>
                </div>
            </div>
            
            <div ng-if="question.type==='select'">
                <select type="select-one" class="qcheck" ng-model="db[question.id]" ng-init="db[question.id]='?'" ng-change="save_data(question)">
                    <option value="?">請選擇</option>
                    <option ng-repeat="answer in question.answers" ng-value="answer.value" ng-bind-html="answer.title"></option>
                </select>
            </div>
            
            <div ng-if="question.type==='text'" ng-repeat="answer in question.answers">
                <div style="display:table-row">
                    <div style="display:table-cell;line-height:60px">{{ answer.title }}</div>
                    <div style="display:table-cell;line-height:60px">
                        <input type="text" class="fat qcheck" placeholder="{{ answer.sub_title }}"
                               ng-style="{maxlength: answer.size, textsize: answer.size, size: answer.size}"
                               ng-model="db[question.id][$index]" ng-model-options="{ updateOn: 'blur' }"
                               ng-change="save_data(question)" />
                    </div>
                </div>
            </div>
            
            <div ng-if="question.type==='textarea'" ng-repeat="answer in question.answers">
                <p><textarea placeholder="請勿輸入超過{{ answer.struct.size }}個中文字" type="textarea" class="qcheck"
                             maxlength="{{ answer.struct.size }}"
                             textsize="{{ answer.struct.size }}" cols="{{ answer.struct.cols }}" rows="{{ answer.struct.rows }}"></textarea>
                </p>				 
            </div>
            
            <div ng-if="question.type==='list'">
                <questions data="question.subs" layer="layer+1" step="step"></questions>
            </div>
            
            <div ng-if="question.type==='scale'">
                <table class="scale" cellspacing="0">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th style="text-align:center;font-size:.8em;width:35px" ng-repeat="answer in question.answers"><b ng-bind-html="answer.title"></b></th>
                        </tr>
                    </thead>
                    <tbody>                                    
                        <tr ng-repeat="question_sub in question.subs">
                            <td class="scale scale-title" width="5px"><p class="scale">({{ $index+1 }})</p></td>
                            <td class="scale"><p class="scale" ng-bind-html="question_sub.title"></p></td>
                            <td class="scale" ng-repeat="answer in question.answers">
                                <input type="radio" class="qcheck scale" id="{{ question.id+'_'+$index }}" ng-model="db[question_sub.id]" ng-value="answer.value" ng-change="save_data(question_sub)" />
                                <label for="{{ question.id+'_'+$index }}" class="scale"></label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div ng-if="question.type==='checkbox'">
                <div ng-repeat="question in question.subs">
                    <p class="checkbox">
                        <input type="checkbox" class="qcheck" id="{{ question.id+'_'+$index }}" ng-model="db[question.id]" ng-true-value="1" ng-false-value="0" ng-change="save_data(question)" />
                        <label for="{{ question.id+'_'+$index }}" class="checkbox">{{ question.title }}</label>
                    </p>
                    <div ng-if="answer.subs.length>0" ng-repeat="answer in question.answers">
                        <div ng-if="answer.subs.length>0 && db[question.id]">
                            <questions data="answer.subs" layer="layer+1"></questions>
                        </div> 
                    </div>
                </div>
            </div>
            
            <div ng-if="question.type==='table'"></div>
            
        </div>
        
    </div>                

</div>