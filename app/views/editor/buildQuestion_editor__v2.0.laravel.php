<?php
//------------------------------------------------------------------------------------------------
//------------------------------------產生問卷HTML---------------------------------------------
//--功能說明:
//--產生radio,text,text_phone,textarea,select,checkbox,scale,list的物件
//--------------------------------------------
//--版本:1.0
//--日期:2010-09-03
//------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------
//--功能說明:
//--加入選單上下層連結
//--------------------------------------------
//--版本:1.1
//--日期:2010-09-03
//------------------------------------------------------------------------------------------------
function buildType($question, $layer) {
    echo '<div class="qtype_box">';

        if( ($question->type=='radio' || $question->type=='select' || $question->type=='textarea') ) {
            echo '<h5 class="ui header">' . $question->answer->name . '<div class="sub header">' . $question->id . '</div></h5>';
        }

        if($layer!=0)
            echo '<div style="position:absolute;margin-left:-30px"><img src="/editor/images/link.png" alt="" /></div>';

        //if($question->type=='checkbox')
        //echo '<span style="font-size:10px;background-color:#D4BFFF;width:170px;position:absolute;margin-left:-'.(180+$layer*46).'px">QID:'.$question->id.'</span>';
        echo '<table class="ui very basic very compact table"><tr>';
        echo '<td>';        
        echo '<select class="ui dropdown" name="qtype" qtype_org="'.$question->type.'">';
        echo '<option'.($question->type=='select'?' selected':'').' value="select">題型 : 單選題(下拉式)</option>';
        echo '<option'.($question->type=='radio'?' selected':'').' value="radio">題型 : 單選題(點選)</option>';
        echo '<option'.($question->type=='checkbox'?' selected':'').' value="checkbox">題型 : 複選題</option>';
        echo '<option'.($question->type=='text'?' selected':'').' value="text">題型 : 文字欄位</option>';
        echo '<option'.($question->type=='textarea'?' selected':'').' value="textarea">題型 : 文字欄位(大型欄位)</option>';
        echo '<option'.($question->type=='scale'?' selected':'').' value="scale">題型 : 量表</option>';
        echo '<option'.($question->type=='list'?' selected':'').' value="list">題型 : 題組</option>';
        echo '<option'.($question->type=='explain'?' selected':'').' value="explain">題型 : 說明文字</option>';
        echo '</select>'.'<span style="font-size:10px">QID:'.$question->id.'</span>';
        echo '</td>';
        echo '<td width="16px"><span class="rulladd" title="增加跳答條件" /></td>';
        echo '<td width="16px"><span class="deletequestion" title="刪除題目" /></td>'; 
        echo '</tr></table>';
    echo '</div>';  
}
function buildQuestion($question,$question_array,$layer,$parrent) {

    $is_hint = false;
    !isset($culume_count) && $culume_count = 1;

    global $q_allsub;

    array_push($q_allsub,(string)$question->id);
    Session::push('buildQuestion.q_allsub', (string)$question->id);

    echo '<div class="question_box ui green segment" id="'.$question->id.'" uqid="'.$question->id.'" parrent="'.$parrent.'" layer="'.$layer.'" style="' . ($layer!=0 ? 'margin-left:45px' : '') . '">';
   
    $title = $question->title;
   
    if( $is_hint && $question->ruletip!='' )
    $title .= '<p><span class="small-purple">'.$question->ruletip.'</span></p>';

    $answer_box = $question->answer;
    $answer_attr = $answer_box->attributes();

    buildType($question, $layer);

    if( count($question->rulls)>0 ){
        echo '<div class="rull_box" style="background-color:#FFBF55;font-size:10px">';
        if( count($question->rulls->rull)>0 )
        foreach($question->rulls->rull as $rull){
            echo '<table class="ui very basic very compact table"><tr>';

            $rullAttr = $rull->attributes();
            $q_data_array = $question_array->xpath("data/column[@qid='".$rullAttr['id']."']");
            //print_r($q_data_array);
            $q_dataAttr = $q_data_array[0]->attributes();
            echo '<td width="90px">跳答條件：<img class="data_edit" src="images/q_data_edit.png" title="修改跳答欄位" alt="修改跳答欄位" /></td>';
            echo '<td width="400px" class="rull_data_text">('.$q_dataAttr['table'].','.$q_dataAttr['name'].') - '.$q_dataAttr['text'].'</td>';

            echo '<td>';
            echo '<select name="condition"><option>=</option></select>';
            echo '<select name="value">';
            foreach($q_data_array[0]->ans as $q_ans){
                $q_ansAttr = $q_ans->attributes();
                echo '<option'.((string)$q_ansAttr['value']==(string)$rullAttr['value']?' selected="selected"':'').'>'.$q_ans.'</option>';
            }
            echo '</select>';
            echo '時，本題跳答';

            echo '</td>';
            echo '<td width="16px"><img class="rulldelete" src="images/q_rull_delete.png" title="刪除規則" alt="刪除規則" /></td>';
            echo '</tr></table>';
        }
        echo '</div>'; 
    }

    echo '<div class="title_box">';
        echo '<table class="ui very basic very compact table"><tr>';
        echo '<td width="65px" valign="top"><div class="ui fluid mini input"><input type="text" name="qlab" placeholder="題號" value="' . (string)$question->idlab . '" /></div></td>';
        echo '<td><div class="editor title ui segment" contenteditable="true" target="title">'.$title.'</div></td>';
        echo '</tr></table>';
    echo '</div>';   



    echo '<div class="fieldA" variable_changed="fasle" auto_hide="'.($answer_attr['auto_hide']==''?'false':$answer_attr['auto_hide']).'" style="">';
   
   $option = "";
   $table = '';
    

        echo '<div class="initv_box '.$question->type.'">';
        echo '<table class="ui very basic very compact table"><tr>';
        if( $question->type=='select' ) {
            if( $answer_attr['auto_hide']=='true' ){
                echo '<td width="16px"><img class="toggle_show" title="展開選項" alt="展開選項" /></td>';
            }else{
                echo '<td width="16px"><img class="toggle_hide" title="隱藏選項" alt="隱藏選項" /></td>';
            }  
        }

        if( $question->type=='text' )
        echo '<td width="86px"><span style="font-size:13px">填答欄位</span></td>';

        $enable_autocode = ($question->type!='textarea' && $question->type!='text' && $question->type!='explain')?'':'display:none';
        echo '<td width="16px" style="'.$enable_autocode.'"><select name="code" '.(( $question->type=='checkbox' || $question->type=='text' )?' disabled="disabled"':'').'>';
            echo '<option'.(( $answer_attr['code']=='auto' || $question->type=='checkbox' || $question->type=='text' )?' selected':'').' value="auto">自動編碼</option>';
            echo '<option'.( $answer_attr['code']=='manual'?' selected':'' ).' value="manual">手動編碼</option>';
        echo '</select></td>';

        echo '<td></td>';

        if ($question->type!='textarea' && $question->type!='explain') {
            echo '<td width="16px"><span class="addvar" anchor="var" addlayer="'.$layer.'" title="加入選項"><i class="add icon"></i></span></td>';
        }
        
        if ($question->type=='radio' || $question->type=='select') {
            echo '<td width="16px"><span class="addvar_list" anchor="var" addlayer="'.$layer.'" title="匯入選項"><i class="table icon"></i></span></td>';
        }
        
        $enable_delectall = ($question->type!='textarea' && $question->type!='explain')?'':'display:none';
        echo '<td width="16px" style="'.$enable_delectall.'"><span class="deletevar_list" anchor="var" addlayer="'.$layer.'" title="刪除全部選項" /></td>';
        echo '</tr></table>';
        echo '</div>';


    $item_count = 1;
    $value_count =1;
    $index_item = 1;
    foreach($question->answer->item as $answer){
        $attr = $answer->attributes();
        switch($question->type){
       
        //------------------------------------------------radio
        case "radio":
            echo '<div class="var_box radio">';
            echo '<table class="ui very basic very compact table"><tr>';
            echo '<td width="30px"><input name="v_value" type="text" size="6" '.($answer_attr['code']=='manual'?'':'disabled="disabled"').' value="'.$attr['value'].'" index="'.$index_item.'" /></td>';
            echo '<td><div class="editor item" qid="'.$question->id.'" contenteditable="true" target="item">'.(string)$answer.'</div></td>';
            echo '<td width="16px"><span class="skipq" title="設定跳題" /></td>';
            echo '<td width="16px"><span class="addvar" anchor="var" addlayer="'.$layer.'" title="加入選項"><i class="add icon"></i></span></td>';
            echo '<td width="16px"><span class="deletevar" title="刪除選項"><i class="minus icon"></i></span></td>';
            echo '<td width="16px"><span class="addquestion" anchor="var" addlayer="'.($layer+1).'" title="加入題目"><i class="dropdown icon"></i></span></td>';
            echo '</tr>';

            if (isset($attr['pageskip']))
            echo '<div class="ruletip"><input type="text" size="20" name="pageskip" target="'.(string)$question->answer->name.'" targetV="'.$attr['value'].'" value="'.$attr['pageskip'].'" /></div>';

            //-----------------skip_box
            if ($attr["skip"]!='') {
                $sub_array = explode(",", $attr["skip"]);
                echo '<tr>';
                echo '<td width="30px"></td>';
                echo '<td><div class="skipbox" target="item" style="border:1px dashed #A0A0A4;background-color:#FFAC55">';
                foreach($sub_array as $attr_i){
                    echo '<span class="skipq_lab" style="margin-left:2px;" title="跳題('.$attr_i.')" target="'.$attr_i.'"></span>';
                }
                echo '</div></td>';
                echo '</tr>';
            }

            echo '</table>';

            //-----------------sub_box
            if ($attr["sub"]!='') {
                $sub_array = explode(",", $attr["sub"]);
                echo '<div class="sub">'; 
                foreach($sub_array as $attr_i){
                    $sub = $question_array->xpath("/page/question_sub/id[.='".$attr_i."']/parent::*");

                    if( isset($sub[0]) && is_array($sub) )
                        buildQuestion($sub[0],$question_array,$layer+1,(string)$question->type);
                }
                echo '</div>'; 
            }

            echo '</div>';
            $value_count++;
            $index_item++;
            break;
    
        //------------------------------------------------text
        case "text":
            echo '<div class="var_box text">';

            //表單名稱
            echo '<span style="font-size:10px;background-color:#D4BFFF;width:170px;position:absolute;margin-left:-'.(180+$layer*46).'px">'.$attr["name"].'</span>';

            echo '<table class="ui very basic very compact table"><tr>';
            echo '<td style="display:none"><input name="v_value" type="hidden" size="1" disabled="disabled" value="'.$attr['value'].'" index="'.$index_item.'" /></td>';
            echo '<td width="1px">';
            echo '<label name="qlab_label" for="textsize_'.$question->id.$index_item.'" style="position:absolute;'.($attr['size']==''?'':'display:none').';color:#666;padding:5px;padding-left:7px;font-size:14px">字數</label>';
            echo '<input id="textsize_'.$question->id.$index_item.'" name="tablesize" type="text" size="2" value="'.$attr['size'].'" /></td>';
            echo '<td><div class="editor item" target="item" contenteditable="true">'.(string)$answer.'</div></td>';
            echo '<td width="300px"><div class="editor item" target="item_sub" contenteditable="true">'.(string)$attr['sub_title'].'</div></td>';
            echo '<td width="16px"><span class="addvar" anchor="var" addlayer="'.$layer.'" title="加入選項"><i class="add icon"></i></span></td>';
            echo '<td width="16px"><span class="deletevar" title="刪除選項"><i class="minus icon"></i></span></td>';
            echo '</tr></table>'; 
            echo '</div>';   

            $index_item++;
            break;

        //------------------------------------------------scale
        case "scale":
        echo '<span style="background-color:#b3e373;width:170px;position:absolute;left:800px">'.$attr["name"].'</span>';  
            echo '<div class="var_box scale">';       
            
            echo '<table class="ui very basic very compact table"><tr>';  
            echo '<td style="display:none"><input name="v_value" type="hidden" size="1" disabled="disabled" value="'.$attr['value'].'" index="'.$index_item.'" /></td>';
            echo '<td><div class="editor item" qid="'.$question->id.'" target="item" contenteditable="true">'.(string)$answer.'</div></td>';    
            echo '<td width="16px"><span class="addvar" anchor="var" addlayer="'.$layer.'" title="加入量表子題"><i class="add icon"></i></span></td>';
            echo '<td width="16px"><span class="deletevar" title="刪除量表子題"><i class="minus icon"></i></span></td>';
            echo '</tr></table>';            
            echo '</div>';  
            
            $item_count++;
            $index_item++;
            break;

        //------------------------------------------------select
        case "select":
            $value_input = (string)$attr['value'];
            $value_input_length = strlen($value_input);

            echo '<div class="var_box select" style="'.($answer_attr['auto_hide']=='true'?';display:none':'').'">';
            echo '<table class="ui very basic very compact table"><tr>';
            echo '<td width="30px"><input name="v_value" type="text" size="'.$value_input_length.'" '.($answer_attr['code']=='manual'?'':'disabled="disabled"').' value="'.$attr['value'].'" index="'.$index_item.'" /></td>';
            echo '<td><div class="editor item" target="item" contenteditable="true">'.(string)$answer.'</div></td>';
            echo '<td width="16px"><span class="skipq" title="設定跳題" /></td>';
            echo '<td width="16px"><span class="addvar" anchor="var" addlayer="'.$layer.'" title="加入選項"><i class="add icon"></i></span></td>';
            echo '<td width="16px"><span class="deletevar" title="刪除選項"><i class="minus icon"></i></span></td>';
            echo '<td width="16px"><span class="addquestion" anchor="var" addlayer="'.($layer+1).'" title="加入題目"><i class="dropdown icon"></i></span></td>';
            echo '</tr>';

            //-----------------skip_box
            if ($attr["skip"]!='') {
                $sub_array = explode(",", $attr["skip"]);
                echo '<tr>';
                echo '<td width="30px"></td>';
                echo '<td><div class="skipbox" target="item" style="border:1px dashed #A0A0A4;background-color:#FFAC55">';
                foreach($sub_array as $attr_i){
                    echo '<img class="skipq_lab" src="images/qtag.png" style="margin-left:2px;" title="跳題('.$attr_i.')" alt="跳題('.$attr_i.')" target="'.$attr_i.'" />';
                }
                echo '</div></td>';
                echo '</tr>';
            }

            echo '</table>';

            if ($attr["sub"]!='') {
                $sub_array = explode(",", $attr["sub"]);
                echo '<div class="sub">'; 
                foreach($sub_array as $attr_i){
                   $sub = $question_array->xpath("/page/question_sub/id[.='".$attr_i."']/parent::*");
                   if(isset($sub[0]))
                       buildQuestion($sub[0],$question_array,$layer+1,(string)$question->type);
                }
                echo '</div>'; 
            }

            echo '</div>';
            $value_count++;
            $index_item++;
            break;

        //------------------------------------------------checkbox
        case "checkbox":
            echo '<div class="var_box checkbox">';
            echo '<span style="font-size:10px;background-color:#b3e373;width:170px;position:absolute;margin-left:-'.(180+$layer*46).'px">'.$attr["name"].'</span>';
            echo '<span style="font-size:10px;background-color:#b3e373;width:18px;position:absolute;margin-left:-'.(28+$layer*46).'px;text-align:center">'.$culume_count.'</span>';
            echo '<table class="ui very basic very compact table"><tr>';
            echo '<td width="30px"><input name="v_value" type="text" size="1" disabled="disabled" value="0,1" index="'.$index_item.'" /></td>';
            echo '<td><div class="editor item" target="item" contenteditable="true">'.(string)$answer.'</div></td>';
            echo '<td width="16px"><span class="ccheckbox'.((string)$attr['reset']=='all'?' enable':'').'" anchor="var" title="清除勾選項目" /></td>';
            echo '<td width="16px"><span class="addvar" anchor="var" addlayer="'.$layer.'" title="加入選項"><i class="add icon"></i></span></td>';
            echo '<td width="16px"><span class="deletevar" title="刪除選項"><i class="minus icon"></i></span></td>';
            echo '<td width="16px"><span class="addquestion" anchor="var" addlayer="'.($layer+1).'" title="加入題目"><i class="dropdown icon"></i></span></td>';
            echo '</tr></table>';

            if ($attr["sub"]!='') {
                $sub_array = explode(",", $attr["sub"]);
                echo '<div class="sub">'; 
                foreach($sub_array as $attr_i){
                    $sub = $question_array->xpath("/page/question_sub/id[.='".$attr_i."']/parent::*");
                    if( isset($sub[0]) && $sub[0] )
                        buildQuestion($sub[0],$question_array,$layer+1,(string)$question->type);
                }
                echo '</div>';
            }

            echo '</div>';            
            $index_item++;
            $culume_count++;
            break;

        //------------------------------------------------scale_text
        case "scale_text":
            $table .= '<tr class="scale">';
            $table .= '<td><p class="scale" style="margin-left:1em;text-indent:-1em">'.$answer.'</p></td>';
            foreach($question->answer->degree as $degree){
                $attr_degree = $degree->attributes();
                $table .= "<td class=\"scale\"><input type=\"text\" size=\"10\" name=\"".$attr["name"]."\" value=\"\" /></td>";
            }
            $table .= "</tr>";
            break;

        //------------------------------------------------list
        case "list":
            echo '<div class="var_box list">';
            echo '<table class="ui very basic very compact table"><tr>';
            echo '<td style="display:none"><input name="v_value" type="hidden" size="1" disabled="disabled" value="'.$attr['value'].'" index="'.$index_item.'" /></td>';
            echo '<td><div class="editor item" qid="'.$question->id.'" target="item" contenteditable="true">'.(string)$answer.'</div></td>';
            echo '<td width="16px"><span class="addvar" anchor="var" addlayer="'.$layer.'" title="加入選項"><i class="add icon"></i></span></td>';
            echo '<td width="16px"><span class="deletevar" title="刪除選項"><i class="minus icon"></i></span></td>';
            echo '<td width="16px"><span class="addquestion" anchor="var" addlayer="'.($layer+1).'" title="加入題目"><i class="dropdown icon"></i></span></td>';
            echo '</tr>';
            echo '</table>';

            //-----------------sub_box
            if ($attr["sub"]!='') {
                $sub_array = explode(",", $attr["sub"]);
                echo '<div class="sub">'; 
                foreach($sub_array as $attr_i){
                    $sub = $question_array->xpath("/page/question_sub/id[.='".$attr_i."']/parent::*");
                    if($sub[0])
                        buildQuestion($sub[0],$question_array,$layer+1,(string)$question->type);
                }
                echo '</div>'; 
            }

            echo isset($attr['ruletip']) ? '<div class="ruletip">'.$attr['ruletip'].'</div>' : '';

            echo '</div>';
            $value_count++;
            $index_item++; 
            break;

        //------------------------------------------------text_phone
        case "text_phone":
            echo $answer.'<input type="text" name="'.$attr["name"].
                '" value="" maxlength="'.$attr["size"].'" textsize="'.$attr["size"].'" size="'.$attr["size"].'" filter="'.$attr["filter"].'" />';
            break;

        //------------------------------------------------textarea
        case "textarea":
            echo '<div class="var_textarea_box" style="margin-right:0px;border:0px dashed #A0A0A4">';
            echo '<table class="ui very basic very compact table"><tr>';
            echo '<td width="1px"><input name="v_value" type="hidden" size="1" disabled="disabled" value="1" index="1" /></td>';
            echo '<td width="86px"><span style="font-size:13px">字數</span><input name="tablesize" type="text" size="2" value="'.$attr['size'].'" /></td>';
            echo '<td width="86px"><span style="font-size:13px">高</span><input name="tableheight" type="text" size="2" value="'.$attr['rows'].'" /></td>';
            echo '<td width="86px"><span style="font-size:13px">寬</span><input name="tablewidth" type="text" size="2" value="'.$attr['cols'].'" /></td>';
            echo '<td><div class="" target="" style="border:0px solid #A0A0A4;min-height:22px"></td>';
            echo '</tr></table>';

            echo isset($attr['ruletip']) ? '<div class="ruletip">'.$attr['ruletip'].'</div>' : '';

            echo '</div>';   

            $index_item++;
            break;
   
        //------------------------------------------------extra
        case "extra":
            echo '<p style="line-height:1.8em">';
            $subs_array = NULL;
            if($attr['sub']!='')
            $subs_array = array_map( create_function('$id', 'return "#".$id;'),explode(",",$attr["sub"]) );
            $subs_string = '';
            if(is_array($subs_array))
            $subs_string = 'sub="'.implode(",",$subs_array).'"';
            echo '<div name="'.$attr["name"].'"></div></p>';
            break;
  
       }

    }
    

    if($question->type == 'scale'){
        echo '<div class="var_scale_box_init">';
        echo '<table class="ui very basic very compact table"><tr>';
        echo '<td><div class="title" style=";border-top:1px dashed #aaa;background-color:#D7E6FC"></div></td>'; 
        echo '<td width="16px"><span class="adddegree" anchor="var" addlayer="'.$layer.'" title="加入選項"><i class="add circle icon"></i></span></td>';
        echo '<td width="29px"></td>';
        echo '</tr></table>';
        echo '</div>';

        foreach($question->answer->degree as $degree){
            $attr_degree = $degree->attributes();
            echo '<div class="var_scale_box">';
            echo '<table class="ui very basic very compact table"><tr>';
            echo '<td width="30px"><input name="v_value" type="text" size="1" disabled="disabled" value="'.$attr_degree['value'].'" /></td>';
            echo '<td><div class="editor item" target="degree" contenteditable="true">'.(string)$degree.'</div></td>';
            echo '<td width="16px"><span class="adddegree" anchor="var" addlayer="'.$layer.'" title="加入選項"><i class="add circle icon"></i></span></td>';
            echo '<td width="16px"><span class="deletevar scale" title="刪除選項"><i class="minus circle icon"></i></span></td>';
            echo '</tr></table>';
            echo '</div>';
            $value_count++;
        }
    }
    if($question->type == "scale_text"){
        $tableHead = '';
        foreach($question->answer->degree as $degree){
            $attr_degree = $degree->attributes();
            $tableHead .= '<th style="font-size:0.8em;width:'.$attr_degree["width"].'"><b>'.$degree.'</b></th>';
        }
        echo "<table><thead><tr><th></th>".$tableHead."</tr></thead><tbody>".$table."</tbody></table>";
    }
   
    echo "</div>";


    echo "</div>";
   

    echo '<div class="addq_box '.($layer==0?'root':'sub').'" append="false">';
    echo '<div class="ui horizontal divider addquestion" anchor="' . ($layer==0?'ques':'var') . '" addlayer="' . $layer . '"><i class="add icon"></i> 加入題目 </div>';
    echo '</div>';
   
}