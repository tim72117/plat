<div class="main" ng-repeat="question in questions">
    <div class="question_box" parrent="{{ question.parrent }}" ng-style="{'margin-left': layer!==0?'45px':0}">
        
        <div class="qtype_box" style="background-color:#fff">
            <div style="position:absolute;margin-left:-30px" ng-if="layer!==0"><img src="/editor/images/link.png" alt="" /></div>
<!--            <span style="font-size:10px;background-color:#b3e373;width:170px;position:absolute;margin-left:-{{180+layer*82}}px">{{ question.name }}</span>
            <span style="font-size:10px;background-color:#b3e373;width:18px;position:absolute;margin-left:-{{28+layer*82}}px">{{ question.culume_count }}</span>-->
            <table class="nb-tab"><tr>
                <td>    
                    <span style="display: inline-block;width:40px;height:25px;line-height:25px;font-size:13px;font-weight:bold;background-color:#FF7F27;color:#fff;text-align: center">題型 </span>
                    <select name="qtype" ng-model="question.type" ng-init="" ng-change="typeChange(question)" style="padding:2px">
                        <option value="?">請選擇題型</option>
                        <option ng-repeat="type in quesTypes" value="{{ type.type }}" ng-selected="{{type.type === question.type}}">{{ type.name }}</option>
                    </select>
                    <span style="font-size:10px">QID:{{ question.id }}</span>
                </td>
                <td width="16px"><span class="rulladd" title="增加跳答條件" /></td>
                <td width="16px"><span class="deletequestion" title="刪除題目" ng-click="ques_remove(questions, $index)" /></td>
                <td width="1px"></td>
            </tr></table>        
        </div>
        
        <div class="title_box" style="background-color:#fff">
            <table class="nb-tab"><tr>
                <td width="55px" valign="top"><input ng-model="question.label" type="text" class="editor" size="2" placeholder="題號" /></td>
                <td>
                    <div contenteditable="true" class="title-editor" ng-editor ng-model="question.title" ng-model-options="{updateOn:'blur'}" ng-change="updateQuestion(question)"></div>
<!--                    <textarea ng-auto-height class="title-editor" style="width:500px" ng-model="question.title" ng-change="updateQuestion(question)" placeholder="題目說明文字"></textarea>-->
                </td>
                <td width="1px"><div class="ruletip"></div></td>
            </tr></table>
        </div>
        
        <div class="initv_box {{ question.type }}" ng-style="{'background-color':question.auto_hide?'#88cc88':''}">
            <table class="nb-tab"><tr>
                <td width="16px" ng-show="question.type==='select'">
                    <img ng-class="{toggle_hide:!question.auto_hide, toggle_show:question.auto_hide}" title="展開/隱藏選項" alt="展開/隱藏選項" ng-click="question.auto_hide=!question.auto_hide" />
                </td> 
                <td width="86px" ng-show="question.type==='text'"><span style="font-size:13px">填答欄位</span></td>
                <td width="16px" ng-show="question.type!=='textarea'&&question.type!=='text'&&question.type!=='explain'&&question.type!=='list'">
                    <select ng-disabled="question.type==='checkbox'||question.type==='text'" ng-model="question.code">
                        <option value="auto">自動編碼</option>
                        <option value="manual">手動編碼</option>
                    </select>
                </td>
                <td></td>
            </tr></table>
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
        
        <div class="fieldA" variable_changed="fasle" auto_hide="{{ question.auto_hide }}" ng-style="{display:question.auto_hide?'none':''}" style="">
            
            <div ng-if="question.subs.length>0" class="var_box">                

                <div class="addq_box" align="left" style="padding: 2px;background-color:#fff;margin-top:0;margin-left:45px">
                    <span class="addquestion" title="加入題目" ng-click="ques_add_ques(question.subs, 0, question.layer)" style="display: inline-block"></span>
                </div>

                <div ng-if="question.type==='scale'">
                    <div ng-repeat="question_sub in question.subs">
                        <table class="nb-tab">    
                            <tr>
                                <td width="45px"></td>
                                <td><textarea ng-model="question_sub.title" class="title-editor" style="width:650px"></textarea></td>  
                                <td width="16px"><span class="addquestion" title="加入量表子題" ng-click="ques_add_ques(question.subs, $index+1)" /></td>
                                <td width="16px"><span class="deletequestion" title="刪除題目" ng-click="ques_remove(question.subs, $index)" /></td>
                                <td width="1px"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div ng-if="question.type==='checkbox'">
                    <div ng-repeat="question_sub in question.subs">
                        <table class="nb-tab">
<!--                                <span style="font-size:10px;background-color:#b3e373;width:170px;position:absolute;margin-left:-{{ 180+layer*82 }}px">{{ answer.name }}</span>-->
                            <tr>
                                <td width="30px"><input type="text" class="editor" size="1" disabled="disabled" value="0,1" /></td>
                                <td width="250px"><textarea ng-model="question_sub.title" class="title-editor"></textarea></td>
                                <td width="16px"><span class="addvar" title="加入選項" ng-click="ques_add_var(question.answers, $index+1)" /></td>
                                <td width="16px"><span class="deletevar" title="刪除選項" ng-click="ques_remove_var(question.answers, $index)" /></td>
                                <td width="16px"><span class="addquestion" title="加入題目" ng-click="ques_add_ques(answer.subs, 0, question.layer+1)" /></td>
                                <td width="16px"><span class="ccheckbox" ng-class="{enable:answer.reset}" title="清除勾選項目" ng-click="answer.reset=!answer.reset" /></td>
                                <td></td>
                            </tr>
                        </table>
                        <div class="var_box" ng-repeat="answer in question_sub.answers">
                            <div ng-if="answer.subs.length>0">
                                <questions data="answer.subs" layer=""></questions>
                            </div>
                        </div>
                    </div>                    
                </div>   
                
                <div ng-if="question.type==='list'">
                    <questions data="question.subs" layer="layer+1"></questions>
                </div>
                
            </div>
            
            <div ng-if="question.type==='scale'">
                <div class="var_scale_box_init" style="margin-right:0px;border:0px dashed #A0A0A4">
                    <table class="nb-tab max"><tr>
                    <td><div class="title" style=";border-top:1px dashed #aaa;background-color:#D7E6FC"></div></td>
                    <td width="16px"></td>
                    <td width="16px"><span class="adddegree" title="加入量表選項" ng-click="ques_add_var(question.degrees, 0)" /></td>
                    <td width="16px"></td>
                    <td width="1px"></td>
                    </tr></table>
                </div>
            </div>                
            
            <div class="var_box" ng-repeat="answer in question.answers">
                
                <table ng-if="$index===0">
                    <tr>
                        <td width="16px" ng-hide="question.type==='explain' || question.type==='textarea'"><span class="addvar" title="加入選項" ng-click="ques_add_var(question.answers, 0)"></span></td>
                        <td width="16px" ng-show="question.type==='radio' || question.type==='select'"><span class="addvar_list" title="匯入選項" ng-click="question.is_import=true" /></td>
                        <td width="16px" ng-hide="question.type==='explain' || question.type==='textarea'"><span class="deletevar_list" anchor="var" addlayer="" title="刪除全部選項" /></td>
                    </tr>
                </table>
                
                <table ng-if="question.type==='radio' && !question.complete" class="nb-tab">
                    <tr>
                        <td width="30px"><input type="text" class="editor" size="1" ng-model="answer.value" ng-disabled="question.code==='auto'" /></td>
                        <td width="250px"><textarea ng-model="answer.title" class="title-editor"></textarea></td>                        
                        <td width="16px"><span class="addvar" title="加入選項" ng-click="ques_add_var(question.answers, $index+1)" /></td>
                        <td width="16px"><span class="deletevar" title="刪除選項" ng-click="ques_remove_var(question.answers, $index)" /></td>
                        <td width="16px"><span class="addquestion" title="加入題目" ng-click="ques_add_ques(answer.subs, 0, question.layer+1)" /></td>
                        <td width="16px"><span class="skipq" title="設定跳題" /></td>
                        <td></td>
                    </tr>
                    <tr ng-if="answer.skips.length>0">
                        <td width="30px"></td>
                        <td><div class="skipbox" target="item" style="border:1px dashed #A0A0A4;background-color:#FFAC55">					
                            <span ng-repeat="skip in answer.skips" class="skipq_lab" style="margin-left:2px;" title="跳題({{ skip }})" target="{{ skip }}" ng-click="ques_remove_var(answer.skips, $index)"></span>
                        </div></td>
                    </tr>
                </table>  
                
                <table ng-if="question.type==='select'" class="nb-tab">
                    <tr>
                        <td width="30px"><input type="text" class="editor" size="1" ng-model="answer.value" ng-disabled="question.code==='auto'" /></td>
                        <td width="250px"><textarea ng-model="answer.title" class="title-editor"></textarea></td>                            
                        <td width="16px"><span class="addvar" title="加入選項" ng-click="ques_add_var(question.answers, $index+1)" /></td>
                        <td width="16px"><span class="deletevar" title="刪除選項" ng-click="ques_remove_var(question.answers, $index)" /></td>
                        <td width="16px"><span class="addquestion" title="加入題目" ng-click="ques_add_ques(answer.subs, 0, question.layer+1)" /></td>
                        <td width="16px"><span class="skipq" title="設定跳題" /></td>
                        <td></td>
                    </tr>
                    <tr ng-if="answer.skips.length>0">
                        <td width="30px"></td>
                        <td><div class="skipbox" target="item" style="border:1px dashed #A0A0A4;background-color:#FFAC55">					
                            <span ng-repeat="skip in answer.skips" class="skipq_lab" style="margin-left:2px;" title="跳題({{ skip }})" target="{{ skip }}"></span>
                        </div></td>
                    </tr>
                </table>    
                
                <table ng-if="question.type==='text'" class="nb-tab">
                    <tr>
                        <td width="16px"><input ng-model="answer.size" type="text" class="editor" size="2" placeholder="字數" /></td>
                        <td width="250px"><textarea ng-model="answer.title" class="title-editor" placeholder="欄位名稱"></textarea></td>
                        <td width="150px"><textarea ng-model="answer.sub_title" class="title-editor" placeholder="填答注意事項" style="width:250px"></textarea></td>		
                        <td width="16px"><span class="addvar" title="加入選項" ng-click="ques_add_var(question.answers, $index+1)" /></td>
                        <td width="16px"><span class="deletevar" title="刪除選項" ng-click="ques_remove_var(question.answers, $index)" /></td>
                        <td></td>                            
                    </tr>
                </table>
                
                <table ng-if="question.type==='textarea'" class="nb-tab">
                    <tr>
                        <td width="100px"><span style="font-size:13px">字數</span><input type="text" class="editor" size="3" ng-model="answer.struct.size" /></td>
                        <td width="100px"><span style="font-size:13px">高</span><input type="text" class="editor" size="3" ng-model="answer.struct.rows" /></td>
                        <td width="100px"><span style="font-size:13px">寬</span><input type="text" class="editor" size="3" ng-model="answer.struct.cols" /></td>
                        <td></td>
                    </tr>
                </table>              
             
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
                
                <table ng-if="question.type==='scale'" class="nb-tab">
                    <tr>
                        <td width="30px"><input type="text" class="editor" size="1" ng-model="answer.value" ng-disabled="question.code==='auto'" /></td>
                        <td><textarea ng-model="answer.title" class="title-editor"></textarea></td>
                        <td width="16px"><span class="adddegree" title="加入量表選項" ng-click="ques_add_var(question.degrees, $index+1)" /></td>
                        <td width="16px"><span class="deletevar" title="刪除量表選項" ng-click="ques_remove_var(question.degrees, $index)" /></td>
                        <td width="1px"></td>
                    </tr>
                </table>
                
                <div ng-if="question.type!=='checkbox' && question.type!=='scale' && answer.subs.length>0">
                    <questions data="answer.subs" layer="layer+1"></questions>
                </div>
                
            </div>
                            

                            

                            
            <div ng-if="question.type==='table' && !question.complete" class="var_scale_box_init" style="margin-right:0px;border:0px dashed #A0A0A4">
                <table class="nb-tab max"><tr>
                <td><div class="title" style=";border-top:1px dashed #aaa;background-color:#D7E6FC"></div></td>
                <td width="16px"></td>
                <td width="16px"><span class="adddegree" title="加入量表選項" ng-click="ques_add_var(question.degrees, 0)" /></td>
                <td width="16px"></td>
                <td width="1px"></td>
                </tr></table>
            </div>
                            
            <div ng-repeat="degree in question.degrees" ng-if="question.type==='table' && !question.complete" class="var_scale_box" style="margin-right:0px;border:0px dashed #A0A0A4">
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
                            
<!--            <div ng-if="!question.complete" ng-click="question.complete=true">完成</div>     -->
            
        </div>
            
            
        <div class="fieldA-complete">
            <div class="var_box" >
                
                <table class="nb-tab" ng-if="question.type==='radio' && question.complete" ng-click="question.complete=false">
                    <tr>
                        <th style="width:50px"></th>
                        <th ng-repeat="answer in question.answers" style="width:50px"><input type="radio" />{{ answer.title }}</th> 
                    </tr>
                </table>
                
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
        </div>

    </div>
    
    <div class="addq_box" align="left" style="padding: 2px;background-color:#fff" ng-style="{'margin-left': layer!==0?'45px':0, 'margin-top': layer!==0?0:'30px'}">
        <span class="addquestion" title="加入題目" ng-click="ques_add_ques(questions, $index+1, question.layer)" style="display: inline-block"></span>
    </div>
        
</div>