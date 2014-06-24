<?php
$question_array = simplexml_load_file( ques_path().'/ques/data/newedu101/data/page_n03.xml' );

$quess = array();
foreach($question_array as $question){
    if($question->getName()=="question"){
        $ques = array('title' => $question->title->__toString());
        $ques['qtype'] = $question->type->__toString();
        $ques['answer'] = array();
        foreach($question->answer->item as $answer_item){
            switch($question->type){
                case "radio":
                    array_push( $ques['answer'], array('title' => $answer_item->__toString()) );
                break;
            }
        }
        array_push($quess, $ques);
    }
}
//echo json_encode($quess);
?>
<link href="<?=asset('editor/editor.css')?>" rel="stylesheet" type="text/css" />

<script>

angular.module('app', [])
.filter('startFrom', function() {
    return function(input, start) {
        return input.slice(start);
    };
}).controller('Ctrl', Ctrl);

function Ctrl($scope, $http) {
    $scope.quess = angular.fromJson(<?=json_encode($quess)?>);
    $scope.qtypes = [
        {type:'select', name:'單選題(下拉式)'},
        {type:'radio', name:'單選題(點選)'},
        {type:'checkbox', name:'複選題'},
        {type:'text', name:'文字欄位'},
        {type:'textarea', name:'文字欄位(大型欄位)'},
        {type:'scale', name:'量表'},
        {type:'list', name:'題組'},
        {type:'explain', name:'文字標題'}
    ];
    
    $scope.selectCange = function(type) {

    };
    $scope.add = function() {
        $scope.quess.push({title:'',qtype:'',answer:[]});
    };
}


</script>

<div ng-controller="Ctrl" class="page" style="border:0;z-index:1;padding-left:200px;padding-right:200px;background-color: #eee">    
    <a ng-click="add()">add</a>
    <div ng-repeat="ques in quess" class="main">
        <div class="question_box new" id="newid" uqid="newid" parrent="" layer="0" style="">

            <div class="qtype_box changed" style="padding-right:0px;background-color:#fff">
                <table style="width:100%">
                    <tr>
                        <td>
                            <select ng-model="qtypea" ng-options="qtype.type as qtype.name for qtype in qtypes track by qtype.type" ng-init="qtypea={type:ques.qtype}"></select>
                        </td>
                        <td width="16px"><span class="deletequestion" alt="刪除題目" /></td>
                        <td width="1px"><div style="font-size:10px;color:red;background-color:#D4BFFF;width:170px;position:absolute;margin-left:20px"></div></td>
                    </tr>				
                </table>
            </div> 

            <div class="title_box" style="background-color:#fff">
                <table style="width:100%">
                    <tr>
                        <td width="65px" valign="top"><input name="qlab" type="text" size="4" value="" /></td>
                        <td><div id="title_'+newid+'" class="editor title" contenteditable="true" target="title" style="padding:5px;border:1px dashed #A0A0A4">{{ ques.title }}</div></td>
                        <td width="1px"><div style="font-size:10px;color:red;background-color:#D4BFFF;width:170px;position:absolute;margin-left:20px"></div></td>
                    </tr>
                </table>
            </div>


            <div class="fieldA changed" init="" style="background-color:#b3e373">
                <div class="initv_box" style="margin-right:0px;border:0px dashed #A0A0A4">
                    <table class="nb-tab">
                        <tr>
                            <td width="16px">
                                <select name="code">
                                    <option value="auto">自動編碼</option>
                                    <option value="manual">手動編碼</option>
                                </select>
                            </td>
                            <td><div class="editor title" style=";border:0px dashed #A0A0A4;background-color:#D7E6FC"></div></td>

                            <td width="16px"><span class="addvar" anchor="var" addlayer="'+addlayer+'" title="加入選項" /></td>
                            <td width="16px"><span class="addvar_list" anchor="var" addlayer="1" title="匯入選項" /></td>
                            <td width="16px"><span class="deletevar_list" anchor="var" addlayer="1" title="刪除全部選項" /></td>
                            <td width="1px"></td>
                        </tr>
                    </table>
                </div>
                <div ng-repeat="answer in ques.answer" class="var_box radio">		
                    <table class="nb-tab">
                        <tr>	
                            <td width="30px"><input name="v_value" type="text" size="1" value="" index="" /></td>
                            <td><div class="editor item" qid="'.$question->id.'" contenteditable="true" target="item">{{ answer.title }}</div></td>

                            <td width="16px"><span class="skipq" title="設定跳題" /></td>
                            <td width="16px"><span class="addvar" anchor="var" addlayer="'.$layer.'" title="加入選項" /></td>
                            <td width="16px"><span class="deletevar" title="刪除選項" /></td>
                            <td width="16px"><span class="addquestion" anchor="var" addlayer="'.($layer+1).'" title="加入題目" /></td>
                        </tr>
                    </table>   
                </div>
            </div>
            <p class="contribute"></p>    

        </div>        
    </div>
</div>