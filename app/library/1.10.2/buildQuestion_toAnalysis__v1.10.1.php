<?php

namespace Plat\Files;
use DB;

class buildQuestionAnalysis {
    
    static function buildVariable($name, $value, $label, $qid){
        $cid = $GLOBALS['CID'];
        $label = str_replace(array("\r", "\n", "\r\n", "\n\r"),"", $label);
        return "INSERT INTO variable VALUES (NULL,NULL,'$name','$value','$label',0,0,'$qid','$cid');\n";
    }
    
    static function build($question,$question_array,$layer,$parrent){	

        $qtree = '';
        $qtree_sub = '';

        if( is_object($parrent) ){		
            $parrent_name = $parrent->name;
            
            if( $parrent->type=='list' )
                $title = $parrent->title.' - '.strip_tags((string)$question->title);
            
            if( $parrent->type=='radio' )
            if( strip_tags((string)$question->title)=='' ){
                $title = strip_tags((string)$parrent->varialbe);
            }else{
                $title = strip_tags((string)$question->title);
            }

        }else{
            $parrent_name = $parrent;
            $title = strip_tags((string)$question->title);
        }
        
        $title = str_replace(array("\r", "\n", "\r\n", "\n\r"),"", $title);

        $show_CID = $GLOBALS['CID'];
        $show_part = $GLOBALS['part'];

        $tablename_insert = 'question';

        if( $question->type!='explain' ){
            switch($question->type){
            case "radio":
            case "select":

                $nameAttr = $question->answer->name->attributes();
                if( !isset($nameAttr['analysis']) || $nameAttr['analysis']==1 ){

                    $GLOBALS['questionSQL'] .= "INSERT INTO $tablename_insert VALUES (NULL,NULL,'$title',NULL,'1', '2','".$question->answer->name."',NULL,NULL,$show_CID,$show_part,0,0,'ques','$parrent_name',$layer,0,1,0);\n";

                    $qtree = '<li><span class="file">'.$title.'</span></li>';
                    
                    $question_db = DB::connection('sqlsrv_analysis')->table('question')->where('CID', $GLOBALS['CID'])->where('spss_name', (string)$question->answer->name)->select('QID')->first();

                    if (isset($question_db))
                    foreach($question->answer->item as $item){
                        if( $item['value']!= '-1' ){
                            $GLOBALS['variableSQL'] .= self::buildVariable((string)$question->answer->name, $item['value'], strip_tags((string)$item), $question_db->QID);
                        }
                    }
                }
            break;
            case "scale":
                $scale_open = false;
                foreach($question->answer->item as $answer){
                    $answerAttr = $answer->attributes();
                    if( !isset($answerAttr['analysis']) || $answerAttr['analysis']==1 )
                    $scale_open = true;	
                }
                if( $scale_open ){
                    $thisClumn_name = $GLOBALS['tablename'].'_SC_H'.$GLOBALS['scale_head_count'];
                    $GLOBALS['scale_head_count']++;

                    $GLOBALS['questionSQL'] .= "INSERT INTO $tablename_insert VALUES (NULL,NULL,'$title',NULL,'1', '2','".$thisClumn_name."',NULL,NULL,$show_CID,$show_part,0,0,'head','$parrent_name',$layer,0,1,0);\n";
                }
            break;
            case "checkbox":
                $checkbox_open = false;
                foreach($question->answer->item as $answer){
                    $answerAttr = $answer->attributes();
                    if( !isset($answerAttr['analysis']) || $answerAttr['analysis']==1 )
                    $checkbox_open = true;	
                }
                if( $checkbox_open ){
                    $thisClumn_name = $GLOBALS['tablename'].'_CK_H'.$GLOBALS['checkbox_head_count'];
                    $GLOBALS['checkbox_head_count']++;

                    $GLOBALS['questionSQL'] .= "INSERT INTO $tablename_insert VALUES (NULL,NULL,'$title',NULL,'1', '2','".$thisClumn_name."',NULL,NULL,$show_CID,$show_part,0,0,'head','$parrent_name',$layer,0,1,0);\n";
                }
            break;	
            }
        }



        $option = '';
        $table = '';
        $sub_array_all = array();



        $item_count = 1;
        foreach($question->answer->item as $answer){
            $attr = $answer->attributes();
            switch($question->type){
            //------------------------------------------------radio
            case "radio":
                $GLOBALS['qOption'] .=$question->id.$question->answer->name.$attr["value"].$answer;
                $thisClumn_name = (string)$question->answer->name;
                $item_count++;			
            break;
            //------------------------------------------------text
            case "explain":
            case "text":
            case "text_phone":
            case "textarea":
            break;
            //------------------------------------------------select
            case "select":
                $thisClumn_name = (string)$question->answer->name;
            break;
            //------------------------------------------------checkbox
            case "checkbox":
                $answerAttr = $answer->attributes();
                if( $checkbox_open )
                if( !isset($answerAttr['analysis']) || $answerAttr['analysis']==1 ){

                    $GLOBALS['questionSQL'] .= "INSERT INTO $tablename_insert VALUES (NULL,NULL,'".strip_tags((string)$answer)."',NULL,'1', '2','".$attr["name"]."',NULL,NULL,$show_CID,$show_part,0,0,'ques','$thisClumn_name',".($layer+1).",0,1,0);\n";

                    $thisClumn_name_checkboxSub = (string)$attr["name"];
                    

                    $question_db = DB::connection('sqlsrv_analysis')->table('question')->where('CID', $GLOBALS['CID'])->where('spss_name', (string)$attr["name"])->select('QID')->first();
                    
                    if (isset($question_db)) {
                        $GLOBALS['variableSQL'] .= self::buildVariable($attr["name"], '1', '勾選', $question_db->QID);
                        $GLOBALS['variableSQL'] .= self::buildVariable($attr["name"], '0', '未勾選', $question_db->QID);
                    }

                    $sub_array = explode(",", $attr["sub"]);
                    $qtree_sub = '';

                    foreach($sub_array as $attr_i){
                        $sub = $question_array->xpath("/page/question_sub/id[.='".$attr_i."']/parent::*");			   
                        if( isset($sub[0]) )			   
                            $qtree_sub .= self::build($sub[0],$question_array,$layer+2,$thisClumn_name_checkboxSub);	
                    }

                    $qtree .= '<li>'.(string)$answer.($qtree_sub==''?'':'<ul>'.$qtree_sub.'</ul>').'</li>';		
                }
                $item_count++;		
            break;
            //------------------------------------------------extra
            case "extra":
            break;
            //------------------------------------------------scale
            case "scale":

                $answerAttr = $answer->attributes();
                if( $scale_open )
                if( !isset($answerAttr['analysis']) || $answerAttr['analysis']==1 ){

                    $GLOBALS['questionSQL'] .= "INSERT INTO $tablename_insert VALUES (NULL,NULL,'".strip_tags((string)$answer)."',NULL,'1', '2','".$attr["name"]."',NULL,NULL,$show_CID,$show_part,0,0,'ques','$thisClumn_name',".($layer+1).",0,1,0);\n";			   


                    $sql = " SELECT QID FROM $tablename_insert WHERE CID='".$GLOBALS['CID']."' AND spss_name='".((string)$attr["name"])."'";
                    //$result_array = $db->getData($sql,'assoc');
                    $question_db = DB::connection('sqlsrv_analysis')->table('question')->where('CID', $GLOBALS['CID'])->where('spss_name', (string)$attr["name"])->select('QID')->first();

                    $degree_key = 0;		   
                    if (isset($question_db))
                    foreach($question->answer->degree as $degree){		
                        $attr_degree = $degree->attributes();
                        $table .= $attr_degree["value"];
                        
                        $GLOBALS['variableSQL'] .= self::buildVariable($attr["name"], $attr_degree["value"], strip_tags((string)$degree), $question_db->QID);

                        $degree_key++;
                    }

                    $qtree_sub .= '<li>'.strip_tags((string)$answer).'</li>';


                }
                $item_count++;		   
            break;
            case "scale_text":

                $GLOBALS['questionSQL'] .= "INSERT INTO $tablename_insert VALUES (NULL,NULL,'".$title.' - '.strip_tags((string)$answer)."',NULL,'1', '2','".$attr["name"]."',NULL,NULL,$show_CID,$show_part,0,0,'ques','$parrent_name',$layer,0,1,0);\n";

                foreach($question->answer->degree as $degree){		
                    $attr_degree = $degree->attributes();
                    $table .= "<td class=\"scale\"><input type=\"text\" size=\"10\" name=\"".$attr["name"]."\" value=\"\" /></td>";
                }
            break;
            case "list":
                $thisClumn_objcet = (object)array('type'=>'list','name'=>$parrent_name, 'title'=>$title);
                $sub_array = explode(",", $attr["sub"]);	
                foreach($sub_array as $attr_i){
                    $sub = $question_array->xpath("/page/question_sub/id[.='".$attr_i."']/parent::*");	
                    if(isset($sub[0]))
                      $qtree_sub .= self::build($sub[0],$question_array,$layer,$thisClumn_objcet);		
                }
                $qtree = '<ul>'.$qtree_sub.'</ul>';

            break;   
            }	

            if( $attr["sub"] && $question->type!="select" && $question->type!="list" && $question->type!="checkbox" && $question->type=="radio" ){
                $sub_array = explode(",", $attr["sub"]);

                $thisClumn_objcet = (object)array('type'=>'radio','name'=>$thisClumn_name, 'title'=>$title,'varialbe'=>(string)$answer);


                foreach($sub_array as $attr_i){
                    $sub = $question_array->xpath("/page/question_sub/id[.='".$attr_i."']/parent::*");			   
                    if($sub[0])			   
                        $qtree_sub .= self::build($sub[0],$question_array,$layer+1,$thisClumn_objcet);	
                }
                if( $qtree_sub=='' ){
                    $qtree = '<li><span class="file">'.$title.'</span></li>';
                }else{
                    $qtree = '<li><span class="folder">'.$title.'</span><ul>'.$qtree_sub.'</ul></li>';
                }

            }elseif( $attr["sub"] && $question->type=="select" ){		   
                $sub_array = explode(",", $attr["sub"]);	
                foreach($sub_array as $attr_i){
                    $sub = $question_array->xpath("/page/question_sub/id[.='".$attr_i."']/parent::*");	
                    if($sub[0])			   
                        array_push($sub_array_all,$sub[0]);	
                }
            }

      }

        if( $question->type == 'checkbox' ){
            $qtree = '<li><span class="folder">'.$title.'</span><ul>'.$qtree.'</ul></li>';
        }
        if( $question->type == 'scale' ){
            $qtree = '<li><span class="folder">'.$title.'</span><ul>'.$qtree_sub.'</ul></li>';
        }

        if($question->type == "select"){
            if(count($sub_array_all)>0){
                foreach($sub_array_all as $sub){
                    self::build($sub,$question_array,$layer+1,$thisClumn_name);
                }
            }
        }

        return $qtree;



    }
}
?>