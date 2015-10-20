<div>
    
    <div class="ui small secondary menu">        
<!--         <a class="item" href="javascript:void(0)">{{ question.label }}</a> -->
        <div class="fitted item">
            <select class="ui dropdown" ng-model="question.type" ng-change="typeChange(question)">
                <option ng-repeat="type in quesTypes" value="{{ type.type }}" ng-selected="{{type.type === question.type}}">題型 : {{ type.name }}</option>
            </select>
            <select class="ui dropdown" ng-model="question.code" ng-if="question.type=='select'">
                <option value="auto">編碼 : 自動</option>
                <option value="manual">編碼 : 手動</option>
            </select>
        </div>

        <div class="right menu">
            <a class="item" href="javascript:void(0)" ng-click="removeQues(question)"><i class="close icon"></i></a>
        </div>
    </div>

    <div class="ui form">
        <div class="field">
            <div class="ui segment" contenteditable="true" ng-model="question.title"></div>
<!--             <textarea ng-model="question.title" ng-model-options="{ updateOn: 'blur' }" ng-change="update(question)" placeholder="輸入題目標題..." style="resize: none"></textarea> -->
        </div>
    </div>
    
    <div class="ui accordion field" ng-if="question.type==='checkbox'">
        
        <div class="title" ng-class="{active: question.open.checkboxs}" ng-click="question.open.checkboxs=!question.open.checkboxs">
            <i class="dropdown icon"></i>題目({{ question.questions.length }})
            <a href="javascript:void(0)" ng-click="$event.stopPropagation();addQues(question, 0);question.open.checkboxs=true" title="新增題目"><i class="add icon"></i></a>
        </div>
    
        <div class="content" ng-if="question.questions.length > 0" ng-class="{active: question.open.checkboxs}">            
            <div class="ui selection list">
                <div class="item" ng-repeat="checkbox in question.questions">
                    <i class=" icon" ng-class="{'red warning': !checkbox.title, move: !!checkbox.title}"></i>
                    <div class="middle aligned ">
                        <div class="ui fluid mini input" ng-mouseenter="question.open.button[$index]=true" ng-mouseleave="question.open.button[$index]=false">
<!--                             <div class="ui label">{{ checkbox.title }} </div> -->
                            <input type="text" ng-model="checkbox.title" placeholder="輸入選項...">
<!--                             <div class="ui icon basic buttons">
                                <div class="ui button" ng-click="addQues(checkbox, 0);question.open.subs=true" ng-if="question.open.button[$index]" title="新增子題">
                                    <i class="vertically flipped fork icon"></i>
                                </div>
                                <div class="ui button" ng-click="checkbox.reset=!checkbox.reset" ng-if="question.open.button[$index] || checkbox.reset" title="清除勾選項目">
                                    <i class="refresh red icon"></i>
                                </div>
                                <div class="ui button" ng-click="removeQuestion(question.questions, $index)" title="刪除題目"><i class="close icon"></i></div>                                
                            </div> -->
                        </div>
<!--                         <a href="javascript:void(0)" ng-click="addQues(checkbox, 0);question.open.subs=true" title="新增子題"><i class="add icon"></i></a>  -->
                    </div>    
                </div>
            </div>                    
        </div>

        <div class="title" ng-class="{active: question.open.subs}" ng-click="question.open.subs=!question.open.subs" ng-if="layer < 2">
            <i class="dropdown icon"></i>子題
        </div>

        <div class="content" ng-class="{active: question.open.subs}">
            <div class="ui tertiary segment" ng-repeat="checkbox in question.questions" ng-if="checkbox.subs.length > 0">                
                <h4 class="ui header">{{ checkbox.title }}</h4>
                <div class="ui vertical segment" ng-repeat="sub in checkbox.subs" question="sub" layer="layer+2" update="update"></div>      
            </div>
        </div>
        
    </div>


            
    <div class="ui accordion field" ng-if="question.type==='radio' || question.type==='select'">
        <div class="title" ng-class="{active: question.open.answers}" ng-click="question.open.answers = !question.open.answers">
            <i class="dropdown icon"></i>選項 ({{ question.answers.length }})
            <a href="javascript:void(0)" ng-click="$event.stopPropagation();addAns(question.answers, 0);question.open.answers=true" title="新增選項">
                <i class="add icon"></i>
            </a>
        </div>
        <div class="content" ng-if="question.answers.length > 0" ng-class="{active: question.open.answers}">
            <div class="ui selection list">
                <div class="item" ng-repeat="answer in question.answers">
                    <div class="header">
                        <div class="ui transparent fluid action left icon input">
                            <i class="icon" ng-class="{'red warning': !answer.title, move: !!answer.title}"></i>
                            <input type="text" ng-model="answer.title" placeholder="輸入選項..." ng-focus="question.open.button[$index]=true" ng-blur="hideOptions(question, $index)" /> 
                            <div class="ui icon basic buttons">                                
                                <div class="ui button" ng-click="addQues(answer, 0, answer);question.open.subs=true" title="新增子題" ng-if="question.open.button[$index]"><i class="vertically flipped fork icon"></i></div>
                                <div class="ui button" ng-click="addAns(question.answers, $index+1)" title="設定跳題" ng-if="question.open.button[$index]"><i class="refresh icon"></i></div>
                                <div class="ui button" ng-click="removeAns(question.answers, $index)" title="刪除選項"><i class="close icon"></i></div>                                
                            </div>
                        </div>
<!--                         {{ answer.value }} -  {{ answer.title }}
                        <a href="javascript:void(0)" ng-click="removeAns(question.answers, $index)" title="刪除選項"><i class="close icon"></i></a>  
                        <a href="javascript:void(0)" ng-click="addAns(question.answers, $index+1)" title="設定跳題"><i class="linkify icon"></i></a> 
                        <span ng-repeat="skip in answer.skips" title="跳題({{ skip }})" target="{{ skip }}" ng-click="removeAns(answer.skips, $index)"></span>
                        <a href="javascript:void(0)" ng-click="addQues(question, 0, answer);question.open.subs=true" title="新增子題"><i class="add icon"></i></a> -->
                    </div>
                </div>
            </div>                
        </div>

        <div class="title" ng-class="{active: question.open.subs}" ng-click="question.open.subs = !question.open.subs" ng-if="question.subs.length > 0 || layer < 2">
            <i class="dropdown icon"></i>子題
        </div>
        <div class="content" ng-if="question.subs.length > 0" ng-class="{active: question.open.subs}">
            <div class="ui tertiary segment" ng-repeat="sub in question.subs">
                <div class="ui vertical segment" question="sub" layer="0" update="update"></div>
            </div>
        </div>
        <div class="content" ng-if="question.answers.length > 0" ng-class="{active: question.open.subs}">
            <div class="ui tertiary segment" ng-repeat="answer in question.answers" ng-if="answer.subs.length > 0">
                <a class="ui green top left attached label">{{ answer.title }}</a>
                <div class="ui vertical segment" ng-repeat="sub in answer.subs" question="sub" update="update"></div>
            </div>
        </div>
    </div>


    <div class="ui accordion field" ng-if="question.type==='scale'">
        <div class="title" ng-class="{active: question.open.questions}" ng-click="question.open.questions = !question.open.questions">
            <i class="dropdown icon"></i>題目({{ question.questions.length }})
            <a href="javascript:void(0)" ng-click="$event.stopPropagation();addSub(question.questions, 0);question.open.questions=true" title="新增題目">
                <i class="add icon"></i>
            </a>
        </div>
        <div class="content" ng-if="question.questions.length > 0" ng-class="{active: question.open.questions}">
            <div class="ui bulleted list">
                <div class="item" ng-repeat="q in question.questions">
                    {{ q.name }} - {{ q.title }}
                    <a href="javascript:void(0)" ng-click="removeQues(question.questions, $index)" title="刪除子題"><i class="close icon"></i></a>
                </div>
            </div>
        </div>
        
        <div class="title" ng-class="{active: question.open.answers}" ng-click="question.open.answers=!question.open.answers">
            <i class="dropdown icon"></i>選項 <a href="javascript:void(0)" ng-click="$event.stopPropagation();addAns(question.answers, 0);question.open.answers=true" title="加入選項"><i class="add icon"></i></a>
        </div>
        
        <div class="content" ng-if="question.answers.length > 0" ng-class="{active: question.open.answers}">
            <div class="ui selection list">
                <div class="item" ng-repeat="answer in question.answers">    
                    <div class="header">
                        <div class="ui transparent fluid action left icon mini input">
                            <i class="icon" ng-class="{'red warning': !answer.title, move: !!answer.title}"></i>
                            <input type="text" ng-model="answer.title" placeholder="輸入選項..." /> 
                            <div class="ui icon basic buttons">
                                <div class="ui button" ng-click="removeAns(question.answers, $index)" title="刪除選項"><i class="close icon"></i></div>
                            </div>
                        </div>         
                    </div>  
                </div>    
            </div>
        </div>
    </div>


    <div class="ui basic segment" ng-if="question.type==='text'">
        <div class="ui selection list">
            <div class="item" ng-repeat="answer in question.answers">    
                <div class="header">                            
                    <div class="ui transparent fluid action left icon input">
                        <i class="icon" ng-class="{'red warning': !answer.title, move: !!answer.title}"></i>
                        <input type="text" ng-model="answer.title" ng-model-options="{ updateOn: 'blur' }" ng-change="update(question)" placeholder="輸入欄位描述..." /> 
                        <div class="ui icon basic buttons">                                
                            <div class="ui button" ng-click="removeAns(question.answers, $index)" title="刪除欄位"><i class="close icon"></i></div>                                
                        </div>
                    </div>         
                </div>  
            </div>    
        </div>
    </div>


    <div class="ui basic segment" ng-if="question.type==='textarea'">
            字數 {{ answer.struct.size }}
            高 {{ answer.struct.rows }}
            寬 {{ answer.struct.cols }} {{ question.answers }} 未紀錄
    </div>


    <div class="ui basic segment" ng-if="question.type==='table'">
        
        <table ng-if="question.type==='table' && !question.complete" class="nb-tab">
            <span style="font-size:10px;background-color:#b3e373;width:170px;position:absolute;margin-left:-{{ 180+layer*82 }}px">{{ answer.name }}</span>
            <tr>
                <td>
                    <div style="display: inline-block;height: 30px;line-height: 30px">第{{ $index+1 }}欄標題</div>
                    <textarea ng-model="answer.title" class="title-editor"></textarea>
                </td>
                <td width="16px"><span class="addvar" title="加入量表子題" ng-click="addAns(question.answers, $index+1)" /></td>
                <td width="16px"><span class="deletevar" title="刪除量表子題" ng-click="removeAns(question.answers, $index)" /></td>
                <td width="1px"></td>
            </tr>
        </table>
            
        <div ng-if="question.type==='table' && !question.complete" class="var_scale_box_init" style="margin-right:0px;border:0px dashed #A0A0A4">
            <table class="nb-tab max"><tr>
            <td><div class="title" style=";border-top:1px dashed #aaa;background-color:#D7E6FC"></div></td>
            <td width="16px"></td>
            <td width="16px"><span class="adddegree" title="加入量表選項" ng-click="addAns(question.degrees, 0)" /></td>
            <td width="16px"></td>
            <td width="1px"></td>
            </tr></table>
        </div>
                        
        <div ng-repeat="degree in question.degrees" ng-if="question.type==='table' && !question.complete">
            <table class="nb-tab">
                <tr>
                    <td>
                        <div style="display: inline-block;height: 30px;line-height: 30px">第{{ $index+1 }}列標題</div>
                        <textarea ng-model="degree.title" class="title-editor" style=""></textarea>
                    </td>
                    <td width="16px"><span class="addvar" title="加入量表子題" ng-click="addAns(question.degrees, $index+1)" /></td>
                    <td width="16px"><span class="deletevar" title="刪除量表子題" ng-click="removeAns(question.degrees, $index)" /></td>
                    <td width="1px"></td>
                </tr>
            </table>
        </div>
        
        <table class="nb-tab" ng-if="question.type==='table' && question.complete" ng-click="question.complete=false">
            <tr>
                <th style="width:50px"></th>
                <th ng-repeat="answer in question.answers" style="width:50px">{{ answer.title }}</th> 
            </tr>
            <tr ng-repeat="degree in question.degrees">
                <th>{{ degree.title }}</th>
                <th ng-repeat="answer in question.answers"><input type="text" style="width:45px" /></th>
            </tr>
        </table>
        
    </div>    
    
       
    
    <div class="addvarlist_box" ng-if="question.is_import" style="margin-right:0px;border:0px dashed #A0A0A4">
        <table style="width:100%">
            <tr><td>
                <span style="color:red">請貼入表格，格式如下</span>
                <table cellspacing="3" cellpadding="3">
                <tr><td style="">第1行--</td><td style="border:1px dashed #A0A0A4">value</td><td style="border:1px dashed #A0A0A4">text</td></tr>
                <tr><td style="">第2行--</td><td style="border:1px dashed #A0A0A4">值(匯入的欄位)</td><td style="border:1px dashed #A0A0A4">文字(匯入的欄位)</td></tr>
                <tr><td style="">第3行--</td><td style="border:1px dashed #A0A0A4">值(匯入的欄位)</td><td style="border:1px dashed #A0A0A4">文字(匯入的欄位)</td></tr>
                </table>
                <input type="button" value="完成" ng-click="" />
            </td></tr>
            <tr>
                <td><textarea rows="5" cols="60" id="packagein" ng-model="question.importText" ng-change="ques_import_var(question)"></textarea></td>
                <td width="1px"><div class="ruletip" style="font-size:10px;color:red;background-color:#D4BFFF;width:170px;position:absolute;margin-left:20px"></div></td>
            </tr>
        </table>
    </div>

    

</div>