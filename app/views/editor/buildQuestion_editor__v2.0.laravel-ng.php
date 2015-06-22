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
function buildQuestion_ng($question, $question_array, $layer, $parrent){	

    //global $question_box;
    $question_box = array();
    $option = "";
    $table = '';
    
	$answer_box = $question->answer;
	$answer_attr = $answer_box->attributes();
   
    array_push($question_box, (object)[
        'id' => null,
        'name' => (string)$question->answer->name,
        'type' => (string)$question->type,
        'layer' => $layer,
        'parrent' => $parrent,
        'auto_hide' => $answer_attr['auto_hide']=='true'
    ]);
    
    $question_new = $question_box[count($question_box)-1];
    
    $question_new->title = (string)$question->title;
    
    $question_new->label = (string)$question->idlab;
    
    $question_new->answers = [];
    
    $question_new->code = (string)$answer_attr['code'];	
    


	foreach($question->answer->item as $answer){
		$attr = $answer->attributes();
        $skips = [];
        switch($question->type){
            case "radio":
                if( $attr["skip"]!='' ){
                    $skips = explode(",", $attr["skip"]);
                }
                
                $sub_questions = [];
                
                $sub_array = explode(",", $attr["sub"]);    
                if( isset($sub_array) && is_array($sub_array) ){
                    foreach($sub_array as $attr_i){
                        $sub = $question_array->xpath("/page/question_sub/id[.='".$attr_i."']/parent::*");	   
                        if( isset($sub[0]) )			   
                            array_push($sub_questions, buildQuestion_ng($sub[0], $question_array,$layer+1, (string)$question->type));	
                    }
                }
                
                array_push($question_new->answers, ['value'=>(string)$attr['value'], 'title'=>strip_tags((string)$answer), 'skips'=>$skips, 'subs'=>$sub_questions]);
                
            break; 
            case "select":
                if( $attr["skip"]!='' ){
                    $skips = explode(",", $attr["skip"]);
                }

                $sub_questions = [];
                
                $sub_array = explode(",", $attr["sub"]);
                if( isset($sub_array) && is_array($sub_array) ){
                    foreach($sub_array as $attr_i){
                        $sub = $question_array->xpath("/page/question_sub/id[.='".$attr_i."']/parent::*");			   
                        if( isset($sub[0]) )		
                            array_push($sub_questions, buildQuestion_ng($sub[0], $question_array,$layer+1, (string)$question->type));	
                    }
                } 
                
                array_push($question_new->answers, ['value'=>(string)$attr['value'], 'title'=>strip_tags((string)$answer), 'skips'=>$skips, 'subs'=>$sub_questions]);
                
            break;    
            case "text":
                array_push($question_new->answers, ['value'=>(string)$attr['value'], 'name'=>(string)$attr['name'], 'title'=>strip_tags((string)$answer), 'size'=>(string)$attr['size'], 'sub_title'=>(string)$attr['sub_title']]);
            break; 
        }

		switch($question->type){

		//------------------------------------------------select
		case "select":
		   if($attr["type"]=="range"){
				$range = explode(",",$attr["value"]);
				for($i=$range[0];$i<=$range[1];$i++){
					$option .= '<option value="'.$i.'">'.$i.'</option>';
				}
		   }elseif($attr["type"]=="list"){
				$list = file_get_contents('question/'.$attr['value']);
				$option .= $list;
		   }else{
			   	if( isset($attr["uplv"]) ){
					$option .= '<option value="'.$attr["value"].'" uplv="'.$attr["uplv"].'">'.strip_tags((string)$answer).'</option>';
				}else{
					$option .= '<option value="'.$attr["value"].'">'.strip_tags((string)$answer).'</option>';
				}
		   }			

		break;
		//------------------------------------------------checkbox
		case "checkbox":
            $reset = (string)$attr['reset']=='all';

			$subs_array = NULL;
			if($attr['sub']!='')
			$subs_array = array_map( create_function('$id', 'return "#".$id;'),explode(",",$attr["sub"]) );
			$subs_string = '';
			if(is_array($subs_array))
			$subs_string = 'sub="'.implode(",",$subs_array).'"';
            
            $sub_questions = [];
			
            $sub_array = explode(",", $attr["sub"]);
			if( isset($sub_array) && is_array($sub_array) ){
				foreach($sub_array as $attr_i){
					$sub = $question_array->xpath("/page/question_sub/id[.='".$attr_i."']/parent::*");
					if( isset($sub[0]) )			   
						array_push($sub_questions, buildQuestion_ng($sub[0], $question_array, $layer+1, (string)$question->type));	
				}
			}
            
            !isset($question_new->subs) && $question_new->subs = [];
            array_push($question_new->subs, (object)[
                'id' => null,
                'name' => (string)$attr["name"],
                'type' => 'checkbox_i',
                'title' => strip_tags((string)$answer),
                'layer' => $layer+1,
                'answers' => [(object)['subs'=>$sub_questions]],
            ]);

		break;

		//------------------------------------------------scale
		case "scale":
            $sub_questions = [];
            
            !isset($question_new->subs) && $question_new->subs = [];
            array_push($question_new->subs, (object)[
                'id' => null,
                'name' => (string)$attr["name"],
                'type' => 'scale_i',
                'title' => strip_tags((string)$answer),
                'layer' => $layer+1,
                'answers' => [(object)['subs'=>$sub_questions]],
            ]);

            //array_push($question_new->answers, ['value'=>(string)$attr['value'], 'title'=>(string)$answer, 'name'=>(string)$attr["name"]]);	
		break;
		//------------------------------------------------list
		case "list":            
            $sub_questions = [];

            $sub_array = explode(",", $attr["sub"]);
			if( isset($sub_array) && is_array($sub_array) ){
				foreach($sub_array as $attr_i){
					$sub = $question_array->xpath("/page/question_sub/id[.='".$attr_i."']/parent::*");			   
					if( isset($sub[0]) )		   
						array_push($sub_questions, buildQuestion_ng($sub[0], $question_array, $layer+1, (string)$question->type));
				}
			}
            
            $question_new->subs = $sub_questions; 

		break;
		//------------------------------------------------textarea
		case "textarea":
            $struct = (object)['size'=>(string)$attr['size'], 'rows'=>(string)$attr['rows'], 'cols'=>(string)$attr['cols']];
			array_push($question_new->answers, ['struct'=>$struct, 'title'=>(string)$answer]);
		break;		   
        //------------------------------------------------extra
        case "extra":
        '<p style="line-height:1.8em">';
        $subs_array = NULL;
        if($attr['sub']!='')
        $subs_array = array_map( create_function('$id', 'return "#".$id;'),explode(",",$attr["sub"]) );
        $subs_string = '';
        if(is_array($subs_array))
        $subs_string = 'sub="'.implode(",",$subs_array).'"';
        '<div name="'.$attr["name"].'"></div></p>';
        break;
        //------------------------------------------------scale_text
        case "scale_text":
            $table .= "<tr class=\"scale\">";
            $table .= "<td><p class=\"scale\" style=\"margin-left:1em;text-indent:-1em\">".$answer."</p></td>";
            foreach($question->answer->degree as $degree){		
              $attr_degree = $degree->attributes();
              $table .= "<td class=\"scale\"><input type=\"text\" size=\"10\" name=\"".$attr["name"]."\" value=\"\" /></td>";
            }
            $table .= "</tr>";	
        break;
		   
        }	
		   
    }  
    
    if($question->type == "scale"){
	    foreach($question->answer->degree as $degree){		
			$attr_degree = $degree->attributes();
            array_push($question_new->answers, ['value'=>(string)$attr_degree['value'], 'title'=>strip_tags((string)$degree)]);
		}
    }  
    

    $tableHead = '';
    if($question->type == "scale_text"){
	   foreach($question->answer->degree as $degree){
		   $attr_degree = $degree->attributes();
		   $tableHead .= '<th style="font-size:0.8em;width:'.$attr_degree["width"].'"><b>'.$degree.'</b></th>';
		}
	   "<table><thead><tr><th></th>".$tableHead."</tr></thead><tbody>".$table."</tbody></table>";
    }
    
    return $question_box[0];

   
}