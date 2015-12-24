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
            echo '<span class="ui left ribbon label"><i class="level down icon"></i></span>';

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
        echo '</select>';
        echo '</td>';
        echo '<td width="16px"><span class="rulladd" title="增加跳答條件" /></td>';
        echo '<td width="16px"><span class="deletequestion" title="刪除題目" /></td>';
        echo '</tr></table>';
    echo '</div>';
}
function buildQuestion($question,$question_array,$layer,$parrent) {

    $is_hint = false;
    !isset($culume_count) && $culume_count = 1;

    global $q_allsub, $items_text, $items_select, $items_scale, $items_checkbox;

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



    echo '<div class="fieldA" variable_changed="fasle" ng-class="{hide: hide.'.(string)$question->id.'}">';
   
    $table = '';


        echo '<div class="initv_box '.$question->type.'">';
        echo '<table class="ui very basic very compact table"><tr>';
        if( $question->type=='select' ) {
            echo '<td width="16px"><i class="icon" title="{{ hide.'.(string)$question->id.' }}"
                ng-class="{hide: !hide.'.(string)$question->id.', unhide: hide.'.(string)$question->id.'}" 
                ng-init="hide.'.(string)$question->id.'='.var_export($answer_attr['auto_hide']=='true', true).'"
                ng-click="hide.'.(string)$question->id.'=!hide.'.(string)$question->id.';hide($event)"></i>';
            echo '</td>';
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
            echo '<td width="16px"><i class="add icon addvar" anchor="var" addlayer="'.$layer.'" title="加入選項"></i></td>';
        }

        if ($question->type=='radio' || $question->type=='select') {
            echo '<td width="16px"><i class="table icon addvar_list" anchor="var" addlayer="'.$layer.'" title="匯入選項"></i></td>';
        }
        
        $enable_delectall = ($question->type!='textarea' && $question->type!='explain')?'':'display:none';	
        echo '<td width="16px" style="'.$enable_delectall.'"><span class="deletevar_list" anchor="var" addlayer="'.$layer.'" title="刪除全部選項" /></td>';
        echo '</tr></table>';
        echo '</div>';


			if ($question->type=='text') {
				echo '<div class="var_box text" ng-repeat="item in items_text.' . (string)$question->id . '">';
				echo '<span style="font-size:10px;background-color:#D4BFFF;width:170px;position:absolute;margin-left:-{{ 180+item.layer*46 }}px">{{ item.attr.name }}</span>';			
				echo '<table class="ui very basic very compact table"><tr>';	
				echo '<td width="80"><div class="ui fluid mini input"><input type="text" disabled="disabled" ng-model="item.attr.name" /></td>';
				echo '<td width="16"><input name="v_value" type="text" size="1" disabled="disabled" ng-model="item.attr.value" /></td>';
				echo '<td width="65"><div class="ui fluid mini input"><input type="text" name="tablesize" placeholder="字數" ng-model="item.attr.size" /></div></td>';	
				echo '<td><div class="ui fluid input"><input type="text" class="editor item" ng-class="{text_changed: item.changed}" target="item" placeholder="題目" ng-model="item.answer" /></div></td>';
				echo '<td><div class="ui fluid input"><input type="text" class="editor item" target="item_sub" placeholder="註解" ng-model="item.attr.sub_title" /></div></td>';		
				echo '<td width="16px"><i class="add icon" ng-click="addtext(items_text.' . (string)$question->id . ', item, $event)" title="加入選項"></i></td>';
				echo '<td width="16px"><i class="minus icon" ng-click="removetext(items_text.' . (string)$question->id . ', item, $event)" title="刪除選項"></i></td>';
				echo '</tr></table>'; 
				echo '</div>';	
			}

			if($question->type=='scale'){
				echo '<div class="var_box scale" ng-repeat="item in items_scale.' . (string)$question->id . '.ques">';
				echo '<table class="ui very basic very compact table"><tr>';
				echo '<td width="80"><div class="ui fluid mini input"><input type="text" disabled="disabled" ng-model="item.attr.name" /></td>';
				echo '<td width="50"><div class="ui fluid mini input"><input type="text" name="v_value" disabled="disabled" ng-model="item.attr.value" /></td>';
				echo '<td><div class="ui fluid mini input"><input type="text" class="editor item" target="item" placeholder="題目" ng-model="item.answer" /></td>';
				echo '<td width="16"><i class="add icon" ng-click="addQues(items_scale.' . (string)$question->id . '.ques, item, $event)" title="加入量表子題"></i></td>';
				echo '<td width="16"><i class="minus icon" ng-click="removeQues(items_scale.' . (string)$question->id . '.ques, item, $event)" title="刪除量表子題"></i></td>';
				echo '</tr></table>'; 
				echo '</div>';  

				echo '<div class="ui horizontal divider var_scale_box_init" ng-click="addDegree(items_scale.' . (string)$question->id . '.degree, {}, $event)"><i class="add icon"></i>加入選項</div>';

				echo '<div class="var_scale_box" ng-repeat="item in items_scale.' . (string)$question->id . '.degree">';
				echo '<table class="ui very basic very compact table"><tr>';
				echo '<td width="50"><div class="ui fluid mini input"><input type="text" name="v_value" disabled="disabled" ng-model="item.attr.value" /></td>';
				echo '<td><div class="ui fluid mini input"><input type="text" class="editor item" target="degree" placeholder="選項" ng-model="item.degree" ng-change="editDegree(item, \'' . (string)$question->id . '\')" /></td>';
				echo '<td width="16px"><i class="add icon" ng-click="addDegree(items_scale, item, $event, \'' . (string)$question->id . '\')" title="加入選項"></i></td>';
				echo '<td width="16px"><i class="minus icon" ng-click="removeDegree(items_scale, item, $event, \'' . (string)$question->id . '\')" title="刪除選項"></i></td>';
				echo '</tr></table>';
				echo '</div>';
			}

			//code uncomplete
			if ($question->type=='select') {
				echo '<div class="var_box select" ng-repeat="item in items_select.' . (string)$question->id . '" ng-hide="hide.'.(string)$question->id.'">';
				echo '<table class="ui very basic very compact table"><tr>';
				echo '<td width="50"><div class="ui fluid mini input"><input type="text" name="v_value" ng-disabled="item.code==\'auto\'" ng-model="item.attr.value" /></td>';
				echo '<td><div class="ui fluid mini input"><input type="text" class="editor item" target="item" placeholder="題目" ng-model="item.answer" /></td>';
				echo '<td width="16"><i class="add icon" ng-click="addQues(items_select.' . (string)$question->id . ', item, $event)" title="加入選項"></i></td>';
				echo '<td width="16"><i class="minus icon" ng-click="removeQues(items_select.' . (string)$question->id . ', item, $event)" title="刪除選項"></i></td>';
				echo '<td width="16px"><span class="skipq" title="設定跳題" /></td>';
				echo '<td width="16px"><i class="caret down icon addquestion" anchor="var" addlayer="{{ item.layer+1 }}" title="加入題目"></i></td>';
				echo '</tr></table>'; 
				echo '</div>';
			}

			if ($question->type=='checkbox') {
				echo '<div class="var_box checkbox" ng-repeat="item in items_checkbox.' . (string)$question->id . '">';
				echo '<table class="ui very basic very compact table"><tr>';
				echo '<td width="80"><div class="ui fluid mini input"><input type="text" disabled="disabled" ng-model="item.attr.name" /></td>';
				echo '<td width="50"><div class="ui fluid mini input"><input type="text" name="v_value" ng-model="item.attr.value" /></td>';
				echo '<td><div class="ui fluid mini input"><input type="text" class="editor item" target="item" placeholder="題目" ng-model="item.answer" /></td>';
				echo '<td width="16"><i class="add icon" ng-click="addQues(items_checkbox.' . (string)$question->id . ', item, $event)" title="加入選項"></i></td>';
				echo '<td width="16"><i class="minus icon" ng-click="removeQues(items_checkbox.' . (string)$question->id . ', item, $event)" title="刪除選項"></i></td>';
				echo '<td width="16"><span class="skipq" title="清除勾選項目" />{{ item.attr.reset }}</td>';
				echo '<td width="16"><i class="caret down icon addquestion" anchor="var" addlayer="{{ item.layer+1 }}" title="加入題目"></i></td>';
				echo '</tr></table>'; 
				echo '</div>';
			}


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
            echo '<td width="30px"><input name="v_value" type="text" size="1" '.($answer_attr['code']=='manual'?'':'disabled="disabled"').' value="'.$attr['value'].'" index="'.$index_item.'" /></td>';
            echo '<td><div class="editor item" qid="'.$question->id.'" contenteditable="true" target="item">'.(string)$answer.'</div></td>';
            echo '<td width="16px"><span class="skipq" title="設定跳題" /></td>';
            echo '<td width="16px"><span class="addvar" anchor="var" addlayer="'.$layer.'" title="加入選項" /></td>';
            echo '<td width="16px"><span class="deletevar" title="刪除選項" /></td>';
            echo '<td width="16px"><i class="add icon addquestion" anchor="var" addlayer="'.($layer+1).'" title="加入題目"></i></td>';
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
            !isset($items_text[(string)$question->id]) && $items_text[(string)$question->id] = [];
            $attr_array = (array)$attr;
            array_push($items_text[(string)$question->id], ['answer' => (string)$answer, 'attr' => $attr_array["@attributes"], 'layer' => $layer]);

            $index_item++;
            break;

        //------------------------------------------------scale
        case "scale":
            !array_key_exists((string)$question->id, $items_scale) && $items_scale[(string)$question->id] = (object)['id' => (string)$question->id, 'ques' => [], 'degree' => []];
            $attr_ques_array = (array)$attr;
            array_push($items_scale[(string)$question->id]->ques, ['answer' => (string)$answer, 'attr' => $attr_ques_array["@attributes"], 'layer' => $layer]);

            $item_count++;
            break;

        //------------------------------------------------select
        case "select":
            !isset($items_select[(string)$question->id]) && $items_select[(string)$question->id] = [];
            $attr_ques_array = (array)$attr;
            array_push($items_select[(string)$question->id], ['answer' => (string)$answer, 'attr' => $attr_ques_array["@attributes"], 'layer' => $layer, 'code' => $answer_attr['code']]);
            !isset($select) && $select = '';

            //-----------------skip_box
            if ($attr["skip"]!='') {
            	$sub_array = explode(",", $attr["skip"]);
            	$select .= '<tr>';
            	$select .= '<td width="30px"></td>';
            	$select .= '<td><div class="skipbox" target="item" style="border:1px dashed #A0A0A4;background-color:#FFAC55">';
            	foreach($sub_array as $attr_i){	
            		$select .= '<img class="skipq_lab" src="images/qtag.png" style="margin-left:2px;" title="跳題('.$attr_i.')" alt="跳題('.$attr_i.')" target="'.$attr_i.'" />';
            	}
            	$select .= '</div></td>';
            	$select .= '</tr>';
            }			

            if ($attr["sub"]!='') {
            	$sub_array = explode(",", $attr["sub"]);
            	$select .= '<div class="sub">'; 
            	foreach($sub_array as $attr_i){
            		$sub = $question_array->xpath("/page/question_sub/id[.='".$attr_i."']/parent::*");
            		if(isset($sub[0])) 
            			buildQuestion($sub[0],$question_array,$layer+1,(string)$question->type);
            	}
            	echo '</div>'; 
            }	

            $value_count++;
            $index_item++;
            break;

        //------------------------------------------------checkbox
        case "checkbox":
            !array_key_exists((string)$question->id, $items_checkbox) && $items_checkbox[(string)$question->id] = [];
            $attr_ques_array = (array)$attr;
            array_push($items_checkbox[(string)$question->id], ['answer' => (string)$answer, 'attr' => $attr_ques_array["@attributes"], 'layer' => $layer]);

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
            echo '<td width="16px"><i class="add icon addvar" anchor="var" addlayer="'.$layer.'" title="加入子題串"></i></td>';
            echo '<td width="16px"><i class="minus icon deletevar" title="刪除子題串"></i></td>';
            echo '<td width="16px"><i class="add icon addquestion" anchor="var" addlayer="'.($layer+1).'" title="加入題目"></i></td>';
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
    	foreach($question->answer->degree as $degree){		
    		$attr_degree = $degree->attributes();
    		!isset($items_scale[(string)$question->id]->degree) && $items_scale[(string)$question->id]->degree = [];
    		$attr_degree_array = (array)$attr_degree;
    		array_push($items_scale[(string)$question->id]->degree, ['degree' => (string)$degree, 'attr' => $attr_degree_array["@attributes"], 'layer' => $layer]);
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