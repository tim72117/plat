<div class="ui segment">

            <div class="ui secondary small menu">        
                <a class="item">
                    {{ question.label }}
                </a>
                <div class="item">
                    <select ng-model="question.type" ng-change="typeChange(question)">
                        <option value="?">請選擇題型</option>
                        <option ng-repeat="type in quesTypes" value="{{ type.type }}" ng-selected="{{type.type === question.type}}">{{ type.name }}</option>
                    </select>
                </div>
                <div class="right menu">
                    <div class="item" ng-click="ques_remove(questions, $index)"><i class="close icon"></i></div>
                </div>
            </div>
            
            <div class="ui basic segment">
                <div style="overflow: hidden;white-space:nowrap;">{{ question.title }}</div>
                <!--<div contenteditable="true" ng-model="question.title"></div>-->
            </div>
            
            
            <div class="ui accordion field" ng-if="question.type==='checkbox'">
                
                <div class="title" ng-if="question.subs.length > 0" ng-click="question.open.checkboxs=!question.open.checkboxs">
                    題目<i class="dropdown icon"></i>
                </div>
            
                <div class="content" ng-if="question.subs.length > 0" ng-class="{active: question.open.checkboxs}">
            
                    <div class="ui list" ng-if="question.type==='checkbox'">
                        <div class="item"><i class="add icon" title="新增子題" ng-click="ques_add_ques(answer.subs, 0, question.layer+1)"></i></div>
                        <div class="item"><i class="add icon" title="新增選項" ng-click="ques_add_var(question.answers, $index+1)"></i></div>
                        <div class="item"><i class="add icon" title="設定跳題" ng-click="ques_add_var(question.answers, $index+1)"></i></div>
                        <i class="refresh icon" title="清除勾選項目" ng-click="answer.reset=!answer.reset"></i>
                        <div class="item" ng-repeat="checkbox in question.subs">
                            
                            <!--<i class="close icon" title="刪除" ng-click="ques_remove_var(question.answers, $index)"></i>-->                        
                            <span ng-if="checkbox.subs.length == 0">{{ checkbox.title }}</span>
                            
                            <div class="ui accordion" ng-if="checkbox.subs.length > 0">
                                <div class="title" ng-class="{active: question.open.checkbox.subs}" ng-click="question.open.checkbox.subs=!question.open.checkbox.subs">
                                    {{ checkbox.title }}
                                    <i class="dropdown icon"></i>
                                </div>
                                <div class="content" ng-class="{active: question.open.checkbox.subs}">
                                    <div questions="true" data="checkbox.subs" layer="layer"></div>
                                </div>
                            </div>
    
                        </div>
                    </div>
                    
                </div>
                
            </div>
    
                    
            <div class="ui accordion field" ng-if="question.type==='radio' || question.type==='select'">
                
                <div class="title" ng-if="question.subs.length > 0" ng-click="question.open.subs =! question.open.subs">
                    <i class="dropdown icon"></i>子題目
                </div>
                
                <div class="content" ng-class="{active: question.open.subs}">
                    <div questions="true" data="question.subs" layer="layer"></div>
                </div>    
                
                <div class="title" ng-click="question.open.answers =! question.open.answers">
                    <i class="dropdown icon"></i>選項
                </div>
                
                <div class="content" ng-class="{active: question.open.answers}">
                    
                    <div ng-if="question.type==='select'">
                        <select ng-model="question.code">
                            <option value="auto">自動編碼</option>
                            <option value="manual">手動編碼</option>
                        </select>
                    </div>
                
                    <div class="ui list" ng-if="!question.complete">
                        <div class="item"><i class="add icon" title="新增子題" ng-click="ques_add_ques(answer.subs, 0, question.layer+1)"></i></div>
                        <div class="item"><i class="add icon" title="新增選項" ng-click="ques_add_var(question.answers, $index+1)"></i></div>
                        <div class="item"><i class="add icon" title="設定跳題" ng-click="ques_add_var(question.answers, $index+1)"></i></div>
                        <div class="item" ng-repeat="answer in question.answers">
                            <i class="close icon" title="刪除" ng-click="ques_remove_var(question.answers, $index)"></i>                            
                            {{ answer.value }} - {{ answer.title }}                            
                        </div>
                    </div>           
                    
                    <div ng-if="question.type==='radio' && !question.complete">
    
                        <div class="var_box" ng-repeat="answer in question.answers">
                            <table>
                                <tr ng-if="answer.skips.length>0">
                                    <td width="30px"></td>
                                    <td><div class="skipbox" target="item" style="border:1px dashed #A0A0A4;background-color:#FFAC55">					
                                        <span ng-repeat="skip in answer.skips" class="skipq_lab" style="margin-left:2px;" title="跳題({{ skip }})" target="{{ skip }}" ng-click="ques_remove_var(answer.skips, $index)"></span>
                                    </div></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                
                
                </div>
                
                
            </div>
            
            <div class="ui accordion field" ng-if="question.type==='scale'">
                
                <div class="title" ng-if="question.subs.length > 0" ng-click="question.open.scale.subs=!question.open.scale.subs">
                    <i class="dropdown icon"></i>題目
                </div>
            
                <div class="content" ng-if="question.subs.length > 0" ng-class="{active: question.open.scale.subs}">
                    <div class="item"><i class="add icon" title="新增子題" ng-click="ques_add_ques(question.subs, $index+1)"></i></div>
                    <div class="item"><i class="add icon" title="刪除子題" ng-click="ques_remove(question.subs, $index)"></i></div>
                    <div class="ui bulleted list">
                        <div class="item" ng-repeat="question in question.subs">{{ question.title }}</div>
                    </div>
                </div>
                
                <div class="title" ng-if="question.answers.length > 0" ng-click="question.open.scale.answers=!question.open.scale.answers">
                    <i class="dropdown icon"></i>選項
                </div>
                
                <div class="content" ng-if="question.answers.length > 0" ng-class="{active: question.open.scale.answers}">
                    <div class="item"><i class="add icon" title="加入量表選項" ng-click="ques_add_var(question.degrees, 0)"></i></div>
                    <div class="item"><i class="add icon" title="刪除量表選項" ng-click="ques_remove_var(question.degrees, $index)"></i></div>
                    <div class="ui list">
                        <div class="item" ng-repeat="answer in question.answers">
                            <i class="close icon" title="刪除" ng-click="ques_remove_var(question.answers, $index)"></i>                            
                            {{ answer.value }} - {{ answer.title }}
                        </div>    
                    </div>
                </div>
    
            </div>
            
            
    
            <div class="ui basic segment" ng-if="question.type==='text'">
                    字數 {{ answer.size }}
                    欄位名稱 {{ answer.title }}
                    填答注意事項 {{ answer.sub_title }} {{ question.answers }} 未紀錄
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
                        <td width="16px"><span class="addvar" title="加入量表子題" ng-click="ques_add_var(question.answers, $index+1)" /></td>
                        <td width="16px"><span class="deletevar" title="刪除量表子題" ng-click="ques_remove_var(question.answers, $index)" /></td>
                        <td width="1px"></td>
                    </tr>
                </table>
                    
                <div ng-if="question.type==='table' && !question.complete" class="var_scale_box_init" style="margin-right:0px;border:0px dashed #A0A0A4">
                    <table class="nb-tab max"><tr>
                    <td><div class="title" style=";border-top:1px dashed #aaa;background-color:#D7E6FC"></div></td>
                    <td width="16px"></td>
                    <td width="16px"><span class="adddegree" title="加入量表選項" ng-click="ques_add_var(question.degrees, 0)" /></td>
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
                            <td width="16px"><span class="addvar" title="加入量表子題" ng-click="ques_add_var(question.degrees, $index+1)" /></td>
                            <td width="16px"><span class="deletevar" title="刪除量表子題" ng-click="ques_remove_var(question.degrees, $index)" /></td>
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


