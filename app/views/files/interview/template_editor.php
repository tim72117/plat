
<div>

    <div style="position:absolute;left:-200px"></div>

    <div class="ui small secondary menu" ng-if="false">
        <div class="fitted item" ng-if="question.hover">
            <label ng-if="false" for="{{::$id}}" class="ui basic mini button" ng-class="{disabled: false}" ng-click="clicked = true; uploader.clearQueue()">
                <i class="icon upload"></i>上傳圖片
            </label>
            <input type="file" id="{{::$id}}" nv-file-select="" uploader="uploader" style="display:none" />
            <select ng-if="false" class="ui dropdown" ng-model="question.code" ng-if="question.is.type=='select'">
                <option value="auto">編碼 : 自動</option>
                <option value="manual">編碼 : 手動</option>
            </select>
        </div>
    </div>

    <div class="ui icon fluid input" ng-if="!question.is.id">
        <input type="text" ng-model="searchText.is.title" placeholder="搜尋題庫..." ng-focus="getPoolQuestions('question')" ng-change="getPoolQuestions('question')">
        <i class="search icon"></i>
    </div>

    <div class="ui small fluid vertical accordion menu" ng-if="question.searching=='question'">
        <div class="item">
            <div class="list" style="overflow-y: auto;max-height:350px">
                <div class="header item" ng-if="(pQuestions | filter:{parent_question_id:false} | filter:searchText).length==0">找不到題目</div>
                <div class="item" ng-repeat="pQuestion in pQuestions | filter:{parent_question_id:false} | filter:searchText">
                    <i class="icon {{icons[pQuestion.is.type].icon}}"></i>
                    <div class="ui checkbox">
                        <input id="{{::$id}}" type="checkbox" class="hidden" ng-click="setPoolQuestion(pQuestion)" />
                        <label for="{{::$id}}" ng-bind-html="pQuestion.parent.is.title + ' - ' + pQuestion.is.title"></label>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="ui form" ng-if="question.is.type&&question.is.type!='?'">
        <div class="ui dividing small header" contenteditable="true" ng-model="question.is.title" ng-change="saveQuestionTitle(question)"></div>
    </div>


    <div class="ui small green progress" ng-if="clicked == true && error != true && overSize == null">
        <div class="bar" style="width: {{ progress }}%">
            <div class="progress">{{ progress + '%'}}</div>
        </div>
        <div class="label" ng-if="success == true" >{{item.file.name}}-上傳成功</div>
    </div>
    <div class="ui small progress error" ng-if="error == true || overSize != null">
        <div class="bar" style="width: {{ progress }}%">
            <div class="progress">{{ progress + '%'}}</div>
        </div>
        <div class="label" ng-if="error == true || overSize != null">上傳失敗-{{overSize}}</div>
    </div>


    <div class="ui accordion" ng-if="question.is.type=='checkboxs'">
        <div class="title" ng-class="{active: question.open.questions}" ng-click="question.open.questions=!question.open.questions">
            <i class="dropdown icon"></i>問項 ({{ (questions | filter:{parent_question_id:question.id}:true).length }})
        </div>
        <div class="content" ng-class="{active: question.open.questions}">
            <div class="ui selection list">
                <div class="item" ng-repeat="bQuestion in questions | filter:{parent_question_id:question.id}:true">
                    <div class="content">
                        <div class="ui transparent fluid action left icon input" ng-class="{loading: bQuestion.saving}">
                            <i class="icon" ng-class="{'red warning':!bQuestion.is.title, 'checkmark box icon':!!bQuestion.is.title}"></i>
                            <input type="text" placeholder="輸入問項標題..." ng-model="bQuestion.is.title" ng-model-options="saveTitleNgOptions" ng-change="saveQuestionTitle(bQuestion)" />
                            <div class="ui icon mini buttons" ng-repeat="answer in bQuestion.answers">
                                <div class="ui button" ng-click="addcQuestion(answer)" title="新增子題"><i class="vertically flipped fork icon"></i></div>
<!--                                 <div class="ui button" ng-click="checkbox.reset=!checkbox.reset" ng-if="question.open.button[$index] || checkbox.reset" title="清除勾選項目">
                                    <i class="refresh red icon"></i>
                                </div>
                                <div class="ui button" ng-click="removeQuestion(question.questions, $index)" title="刪除題目"><i class="close icon"></i></div> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="item" ng-click="addrQuestion(question, 'checkbox')">
                    <div class="content"><i class="checkmark box icon"></i>新增問項</div>
                </div>
            </div>
        </div>
    </div>



    <div class="ui accordion field" ng-if="(question.is.type=='radio' || question.is.type=='select')">
        <div class="title" ng-class="{active: question.open.answers}" ng-click="question.open.answers=!question.open.answers">
            <i class="dropdown icon"></i>選項 ({{ question.answers.length }})
        </div>
        <div class="content" ng-class="{active: question.open.answers}">
            <div class="ui selection list">
                <div class="item" ng-repeat="answer in question.answers">
                    <div class="content">
                        <div class="ui transparent fluid action left icon input" ng-class="{loading: answer.saving}">
                            <i class="icon" ng-if="question.is.type=='radio'" ng-class="{'red warning':!answer.is.title, radio:!!answer.is.title}"></i>
                            <i class="icon" ng-if="question.is.type=='select'" ng-class="{'red warning':!answer.is.title, 'ellipsis vertical':!!answer.is.title}"></i>
                            <input type="text" placeholder="輸入選項名稱..." ng-model="answer.is.title" ng-model-options="saveTitleNgOptions" ng-change="saveAnswerTitle(answer)" />
                            <div class="ui icon mini buttons">
                                <div class="ui button" ng-click="addcQuestion(answer)" title="新增子題"><i class="vertically flipped fork icon"></i></div>
<!--                                 <div class="ui button" ng-click="removeAnswer(answer)" title="刪除選項"><i class="close icon"></i></div> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="item" ng-click="createAnswer(question)">
                    <div class="content"><i class="icon" ng-class="{radio: question.is.type=='radio', 'ellipsis vertical':question.is.type=='select'}"></i>新增選項</div>
                </div>
            </div>
        </div>
    </div>


    <div class="ui accordion field" ng-if="question.is.type=='scales'">
        <div class="title" ng-class="{active: question.open.questions}" ng-click="question.open.questions = !question.open.questions">
            <i class="dropdown icon"></i>問題 ({{ (questions | filter:{parent_question_id:question.id}:true).length }})
        </div>
        <div class="content" ng-class="{active: question.open.questions}">
            <div class="ui selection list">
                <div class="item" ng-repeat="cQuestion in questions | filter:{parent_question_id:question.id}:true">
                    <div class="content">{{$index}}
                        <div class="ui transparent fluid left icon input" ng-class="{loading: cQuestion.saving}">
                            <i class="icon" ng-class="{'red warning':!cQuestion.is.title, 'ordered list':!!cQuestion.is.title}"></i>
                            <input type="text" placeholder="輸入問題..." ng-model="cQuestion.is.title" ng-model-options="saveTitleNgOptions" ng-change="saveQuestionTitle(cQuestion)" />
                            <div class="ui icon mini basic buttons">
                                <div class="ui button" ng-click="moveBranchSort(cQuestion, -1)" title="上移"><i class="caret up icon"></i></div>
                                <div class="ui button" ng-click="moveBranchSort(cQuestion, 1)" title="下移"><i class="caret down icon"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="ui basic small button" ng-click="addrQuestion(question, 'scale')"><i class="ordered list icon"></i>新增問題</button>
            <button class="ui basic small button" ng-click="getPoolQuestions('branch')"><i class="counterclockwise rotated reply icon"></i>加入題庫問題</button>
            <div class="ui basic segment" ng-if="question.searching=='branch'">
                <div class="ui icon fluid transparent input">
                    <input type="text" ng-model="searchText.is.title" placeholder="搜尋題庫..." ng-focus="getPoolQuestions('branch')">
                </div>
                <div class="ui small fluid vertical accordion menu">
                    <div class="item">
                        <div class="list" style="overflow-y: auto;max-height:350px">
                            <div class="header item" ng-if="(pQuestions | filter:{is:{type:'scales'}} | filter:searchText).length==0">找不到題目</div>
                            <div class="item" ng-repeat="pQuestion in pQuestions | filter:{is:{type:'scales'}} | filter:searchText">
                                <i class="icon {{icons[pQuestion.is.type].icon}}"></i>
                                <div class="ui checkbox">
                                    <input id="{{::$id}}" type="checkbox" class="hidden" ng-click="setPoolScaleBranchQuestion(pQuestion)" />
                                    <label for="{{::$id}}" ng-bind-html="pQuestion.parent.is.title + ' - ' + pQuestion.is.title"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="title" ng-class="{active: question.open.answers}" ng-click="question.open.answers=!question.open.answers">
            <i class="dropdown icon"></i>選項 ({{ question.answers.length }})
        </div>
        <div class="content" ng-class="{active: question.open.answers}">
            <div class="ui selection list">
                <div class="item" ng-repeat="answer in question.answers">
                    <div class="content">
                        <div class="ui transparent fluid action left icon input" ng-class="{loading: answer.saving}">
                            <i class="icon" ng-class="{'red warning': !answer.is.title, radio: !!answer.is.title}"></i>
                            <input type="text" placeholder="輸入選項..." ng-model="answer.is.title" ng-model-options="saveTitleNgOptions" ng-change="saveAnswerTitle(answer)" />
<!--                             <div class="ui icon basic buttons">
                                <div class="ui button" ng-click="removeAns(question.answers, $index)" title="刪除選項"><i class="close icon"></i></div>
                            </div> -->
                        </div>
                    </div>
                </div>
                <div class="item disabled" ng-click="addrQuestion(question, 'scale')">
                    <div class="content"><i class="ordered list icon"></i>新增選項(未完成)</div>
                </div>
            </div>
        </div>
    </div>


    <div class="ui accordion field" ng-if="question.is.type=='texts'">
        <div class="title" ng-class="{active: question.open.questions}" ng-click="question.open.questions=!question.open.questions">
            <i class="dropdown icon"></i>題項 ({{ (questions | filter:{parent_question_id:question.id}:true).length }})
        </div>
        <div class="content" ng-class="{active: question.open.questions}">
            <div class="ui selection list">
                <div class="item" ng-repeat="cQuestion in questions | filter:{parent_question_id:question.id}:true">
                    <div class="content">
                        <div class="ui transparent fluid action left icon input" ng-class="{loading: cQuestion.saving}">
                            <i class="icon" ng-class="{'red warning': !cQuestion.is.title, write: !!cQuestion.is.title}"></i>
                            <input type="text" placeholder="輸入題項..." ng-model="cQuestion.is.title" ng-model-options="saveTitleNgOptions" ng-change="saveQuestionTitle(cQuestion)" />
                        </div>
                    </div>
                </div>
                <div class="item" ng-click="addrQuestion(question, 'text')">
                    <div class="content"><i class="write icon"></i>新增題項</div>
                </div>
            </div>
        </div>
    </div>

    <!-- select, radio subs -->
    <div class="ui basic segment" ng-if="question.id" ng-repeat="cQuestion in questions | filter:{parent:{question_id:question.id}}:true">
        <div class="ui top attached borderless menu">
            <div class="item" ng-if="cQuestion.is.type=='radio'"><i class="selected radio icon"></i>單選題</div>
            <div class="item" ng-if="cQuestion.is.type=='checkboxs'"><i class="checkmark box icon"></i>複選題</div>
            <div class="item" ng-if="cQuestion.is.type=='select'"><i class="arrow circle down icon"></i>下拉選單</div>
            <div class="item" ng-if="cQuestion.is.type=='scales'"><i class="ordered list icon"></i>量表題</div>
            <div class="item" ng-if="cQuestion.is.type=='texts'"><i class="write icon"></i>文字填答</div>
            <div class="right menu">
                <div class="ui dropdown icon item" ng-class="{disabled:question.saving}" ng-if="!$first" ng-click="moveChildrenSort(cQuestion, -1)"><i class="caret up icon"></i></div>
                <div class="ui dropdown icon item" ng-class="{disabled:question.saving}" ng-if="!$last" ng-click="moveChildrenSort(cQuestion, 1)"><i class="caret down icon"></i></div>
                <div class="ui dropdown icon item" ng-click="removeQuestion(cQuestion)"><i class="trash icon"></i></div>
            </div>
        </div>
        <div class="ui bottom attached segment" ng-class="{loading:cQuestion.saving}">
            <div class="ui red basic ribbon label" ng-repeat="answer in question.answers | filter:{id:cQuestion.parent_answer_id}:true" ng-bind-html="question.is.title + ' ' +answer.is.title"></div>
            <p><div question="cQuestion" sbook="sbook" sbooks="sbooks" page="page" questions="questions"></div></p>
        </div>
    </div>


    <!-- checkbox subs -->
    <div ng-if="question.is.type=='checkboxs'&&question.id" ng-repeat="bQuestion in questions | filter:{parent_question_id:question.id}:true">
        <div class="ui top attached borderless menu" ng-repeat-start="cQuestion in questions | filter:{parent:{question_id:bQuestion.id}}"><!-- new question can't create subs -->
            <div class="item" ng-if="cQuestion.is.type=='radio'"><i class="selected radio icon"></i>單選題</div>
            <div class="item" ng-if="cQuestion.is.type=='checkboxs'"><i class="checkmark box icon"></i>複選題</div>
            <div class="item" ng-if="cQuestion.is.type=='select'"><i class="arrow circle down icon"></i>下拉選單</div>
            <div class="item" ng-if="cQuestion.is.type=='scales'"><i class="ordered list icon"></i>量表題</div>
            <div class="item" ng-if="cQuestion.is.type=='texts'"><i class="write icon"></i>文字填答</div>
            <div class="right menu">
                <div class="ui dropdown icon item" ng-click="removeQuestion(cQuestion)"><i class="trash icon"></i></div>
            </div>
        </div>
        <div class="ui bottom attached segment" ng-repeat-end ng-class="{loading:cQuestion.saving}">
            <div class="ui red basic ribbon label" ng-bind-html="question.is.title + ' ' +bQuestion.is.title"></div>
            <p><div question="cQuestion" sbook="sbook" sbooks="sbooks" page="page" questions="questions"></div></p>
        </div>
    </div>


    <div ng-if="question.is.type=='list'&&question.id">
        <div class="ui accordion field">
            <div class="title" ng-class="{active: question.open.questions}" ng-click="question.open.questions=!question.open.questions">
                <i class="dropdown icon"></i>題目 ({{ (questions | filter:{parent_question_id:question.id}:true).length }})
            </div>
            <div class="content" ng-class="{active: question.open.questions}">
                <div class="ui right aligned basic segment">
                    <a class="ui mini label" ng-click="addBranchQuestion(0)"><i class="add icon"></i>加入題目</a>
                 </div>
                <div class="ui basic segment" ng-repeat="bQuestion in questions | filter:{parent_question_id:question.id}:true" ng-mouseenter="bQuestion.open.add=true" ng-mouseleave="bQuestion.open.add=false">
                    <div class="ui top attached borderless menu">
                        <div class="item" ng-if="bQuestion.is.type=='radio'"><i class="selected radio icon"></i>單選題</div>
                        <div class="item" ng-if="bQuestion.is.type=='checkboxs'"><i class="checkmark box icon"></i>複選題</div>
                        <div class="item" ng-if="bQuestion.is.type=='select'"><i class="arrow circle down icon"></i>下拉選單</div>
                        <div class="item" ng-if="bQuestion.is.type=='scales'"><i class="ordered list icon"></i>量表題</div>
                        <div class="item" ng-if="bQuestion.is.type=='texts'"><i class="write icon"></i>文字填答</div>
                        <div class="item" ng-if="bQuestion.is.type=='list'"><i class="sitemap icon"></i>題組</div>
                        <div class="right menu">
                            <div class="ui dropdown icon item" ng-click="removeQuestion(bQuestion)"><i class="trash icon"></i></div>
                        </div>
                    </div>
                    <div class="ui bottom attached segment" ng-class="{loading:bQuestion.saving}">
                        <div question="bQuestion" sbook="sbook" sbooks="sbooks" page="page" questions="questions"></div>
                        <a class="ui mini bottom right attached label" ng-if="bQuestion.open.add" ng-click="addBranchQuestion($index+1)"><i class="add icon"></i>加入題目</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="ui basic segment" ng-if="question.is.type=='textarea'">
            字數 {{ answer.struct.size }}
            高 {{ answer.struct.rows }}
            寬 {{ answer.struct.cols }} {{ question.answers }} 未紀錄
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
