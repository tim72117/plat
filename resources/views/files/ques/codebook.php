<?php

class tdobj{
	var $objtype = '';
	var $name = '';
	var $id = '';
	var $question_type = '';
	var $nullvalue = '';
	var $tablesize = '';
	var $title = '';
	var $varible = '';	
	var $page = '';
	var $input = '';
	var $ruletip = '';
	var $length = '';
}

function read_sql($doc_page, &$table_array) {
    $question_array = simplexml_load_string($doc_page->xml);

    foreach($question_array as $question){

        if( $question->getName()=="explain" ){
            $tdobj = new tdobj();
            $tdobj->objtype = "question";
            $tdobj->name = '';
            $tdobj->id = '';
            $tdobj->question_type = "explain";
            $tdobj->nullvalue = '';
            $tdobj->tablesize = '';
            $tdobj->title = (string)$question;
            $tdobj->ruletip = '';
            array_push($table_array,$tdobj);			
        }

        if( $question->getName()=='question' )
            buildQuestion($question,$question_array,$table_array);
    }
}

function read($xmlfile,&$table_array){
	if( !is_file($xmlfile) )
		return false;
	$question_array = simplexml_load_file($xmlfile); 												
	//-------------------------------------------------------欄位開始
	foreach($question_array as $question){
				
		if( $question->getName()=="explain" ){
			$tdobj = new tdobj();
			$tdobj->objtype = "question";
			$tdobj->name = '';
			$tdobj->id = '';
			$tdobj->question_type = "explain";
			$tdobj->nullvalue = '';
			$tdobj->tablesize = '';
			$tdobj->title = (string)$question;
			$tdobj->ruletip = '';
			array_push($table_array,$tdobj);			
		}
		
		if( $question->getName()=='question' )
			buildQuestion($question,$question_array,$table_array);
	}
}

function buildQuestion($question,$question_array,&$table_array){
	$id = $question->idlab;
	$name = $question->answer->name;
	
	switch($question->type){
		case "checkbox":

			$tdobj = new tdobj();
			$tdobj->objtype = "question";
			$tdobj->name = '';
			$tdobj->id = $id;
			$tdobj->question_type = "checkbox_head";
			$tdobj->nullvalue = '';
			$tdobj->tablesize = '';
			$tdobj->title = $question->title;
			$tdobj->ruletip = $question->ruletip;
			array_push($table_array,$tdobj);

			$is_first = true;
			foreach($question->answer->item as $item){
				$attr = $item->attributes();				
				$name = $attr["name"];
				
				$tdobj = new tdobj();
				$tdobj->objtype = "question";
				$tdobj->name = $name;
				$tdobj->id = '';
				$tdobj->question_type = $question->type;
				$tdobj->nullvalue = 0;
				$tdobj->tablesize = 2;
				$tdobj->title = $item;
				$tdobj->varible = simplexml_load_string("<?xml version='1.0'?><answer><item value='0'>沒填答</item><item value='1'>有填答</item></answer>");
				$tdobj->input = 0;
				$tdobj->length = count($question->answer->item);
				array_push($table_array,$tdobj);
				
				if( isset($attr["sub"]) && $sub_array = explode(",", $attr["sub"]) ){
					foreach($sub_array as $attr_i){
						$sub = $question_array->xpath("/page/question_sub/id[.='".$attr_i."']/parent::*");			
						if( isset($sub[0]) )			
							buildQuestion($sub[0],$question_array,$table_array);	
					}
				}
			}	
		
		
		break;
		case "scale":

			$tdobj = new tdobj();
			$tdobj->objtype = "question";
			$tdobj->name = '';
			$tdobj->id = $id;
			$tdobj->question_type = "scale_head";
			$tdobj->nullvalue = '';
			$tdobj->tablesize = '';
			$tdobj->title = $question->title;
			$tdobj->ruletip = $question->ruletip;
			array_push($table_array,$tdobj);
			
			$size = strlen(count($question->answer->degree))+1;
			$is_first = true;
			$sub_index = 1;
			foreach($question->answer->item as $key => $item){
				$attr = $item->attributes();
				$name = $attr["name"];
				$degree = $is_first ? $question->answer->degree : '';
				$is_first = false;
				
				$tdobj = new tdobj();
				$tdobj->objtype = "question";
				$tdobj->name = $name;
				$tdobj->id = $id.' - '.$sub_index;
				$tdobj->question_type = $question->type;
				$tdobj->nullvalue = 0;
				$tdobj->tablesize = $size;
				$tdobj->title = $item;
				$tdobj->varible = $degree;
				$tdobj->input = 0;
				$tdobj->length = count($question->answer->item);
				array_push($table_array,$tdobj);
				$sub_index++;
			}			

			
		break;
		case "radio":	

			$size = strlen(count($question->answer->item))+1;
			$tdobj = new tdobj();
			$tdobj->objtype = "question";
			$tdobj->name = $name;
			$tdobj->id = $id;
			$tdobj->question_type = $question->type;
			$tdobj->nullvalue = 0;
			$tdobj->tablesize = $size;
			$tdobj->title = $question->title;
			$tdobj->ruletip = $question->ruletip;
			$tdobj->varible = $question->answer->item;
			$tdobj->input = 0;
			array_push($table_array,$tdobj);			
			
			foreach($question->answer->item as $answer){
				$attr = $answer->attributes();
				if( isset($attr["sub"]) && $sub_array = explode(",", $attr["sub"]) ){
					foreach($sub_array as $attr_i){
						$sub = $question_array->xpath("/page/question_sub/id[.='".$attr_i."']/parent::*");			
						if( isset($sub[0]) )			
							buildQuestion($sub[0],$question_array,$table_array);	
					}
				}
			}

		break;
		case "text":

			$tdobj = new tdobj();
			$tdobj->objtype = "question";
			$tdobj->name = '';
			$tdobj->id = $id;
			$tdobj->question_type = "text_head";
			$tdobj->nullvalue = '';
			$tdobj->tablesize = '';
			$tdobj->title = $question->title;
			$tdobj->ruletip = $question->ruletip;
			//array_push($table_array,$tdobj);

			foreach($question->answer->item as $key => $item){
				$attr = $item->attributes();
				$name = $attr["name"];

				$tdobj = new tdobj();
				$tdobj->objtype = "question";
				$tdobj->name = $name;
				$tdobj->id = '';
				$tdobj->question_type = $question->type;
				$tdobj->nullvalue = 0;
				$tdobj->tablesize = $attr['size'];
				$tdobj->title = $question->title.$item;
				$tdobj->ruletip = $question->ruletip;
				$tdobj->varible = $item;
				$tdobj->input = 0;
				array_push($table_array,$tdobj);				

			}

		break;	
		case "textarea":

			$tdobj = new tdobj();
			$tdobj->objtype = "question";
			$tdobj->name = $name;
			$tdobj->id = $id;
			$tdobj->question_type = $question->type;
			$tdobj->nullvalue = 0;
			$tdobj->tablesize = '';
			$tdobj->title = $question->title;
			$tdobj->ruletip = $question->ruletip;
			$tdobj->varible = '';
			$tdobj->input = 0;
			array_push($table_array,$tdobj);


		break;
		case "select":
			
			$answerAttr = $question->answer->attributes();
			$code = $answerAttr['code'];
			if($code=='auto'){
				$size = strlen(count($question->answer->item))+1;
			}elseif($code=='manual'){
				$size = 0;
				foreach($question->answer->item as $item){
					$itemAttr = $item->attributes();
					if(	strlen($itemAttr['value']) > $size )
						$size = strlen($itemAttr['value']);
				}
				$size++;
			}

			$tdobj = new tdobj();
			$tdobj->objtype = "question";
			$tdobj->name = $name;
			$tdobj->id = $id;
			$tdobj->question_type = $question->type;
			$tdobj->nullvalue = 0;
			$tdobj->tablesize = $size;
			$tdobj->title = $question->title;
			$tdobj->ruletip = $question->ruletip;
			$tdobj->varible = $question->answer->item;
			$tdobj->input = 0;
			array_push($table_array,$tdobj);	
			
			foreach($question->answer->item as $key => $answer){
				$attr = $answer->attributes();
				if( isset($attr["sub"]) && $sub_array = explode(",", $attr["sub"]) ){
					foreach($sub_array as $attr_i){
						$sub = $question_array->xpath("/page/question_sub/id[.='".$attr_i."']/parent::*");			
						if( isset($sub[0]) )			
							buildQuestion($sub[0],$question_array,$table_array);	
					}
				}
			}


		break;

		case "list":

			$tdobj = new tdobj();
			$tdobj->objtype = "question";
			$tdobj->name = '';
			$tdobj->id = $id;
			$tdobj->question_type = "list";
			$tdobj->nullvalue = '';
			$tdobj->tablesize = '';
			$tdobj->title = $question->title;
			$tdobj->ruletip = $question->ruletip;
			array_push($table_array,$tdobj);
			
			foreach($question->answer->item as $key => $answer){
				$attr = $answer->attributes();
				if( isset($attr["sub"]) && $sub_array = explode(",", $attr["sub"]) ){
					foreach($sub_array as $attr_i){
						$sub = $question_array->xpath("/page/question_sub/id[.='".$attr_i."']/parent::*");			
						if( isset($sub[0]) )			
							buildQuestion($sub[0],$question_array,$table_array);	
					}
				}
			}

		break;	
	}
}
		


$table_array = array();
foreach($census->pages->sortBy('page') as $page){
    $tdobj = new tdobj();
    $tdobj->objtype = "title";
    $tdobj->page = $page->page;
    array_push($table_array, $tdobj);
    read_sql($page, $table_array);
}

// old xml file
// $dataroot = ques_path().'/ques/data/'.$config['rootdir'];

// $pageinfo_file = $dataroot.'/data/pageinfo.xml';
// if( !file_exists($pageinfo_file) ){
//     header('Location: ./');
//     echo 'error pageinfo.xml';
//     exit; 
// };
// $pageinfo = simplexml_load_file($pageinfo_file);
// $page_array = $pageinfo->p;


// $table_array = array();

// $page = 0;
// foreach($page_array as $xmlfile){
//     $tdobj = new tdobj();
//     $tdobj->objtype = "title";
//     $tdobj->page = $page+1;
//     array_push($table_array,$tdobj);
//     $xmlfile_real = $dataroot.'/data/'.$xmlfile->xmlfile;
//     read($xmlfile_real,$table_array);
//     $page++;
// }

$q_count = 0;
$tr_color_array = array('text_head'=>'#ccffcc','checkbox_head'=>'#ccffcc','list'=>'#ccffcc','scale_head'=>'#ccffcc','scale'=>'#f0f0f0','text'=>'#ddd');

$outtable = '';
foreach($table_array as $table){
	if($table->objtype=="question"){
		$q_count = $q_count+1;

		$tr_color = '';
		if( $table->question_type!='' )
		if( array_key_exists((string)$table->question_type,$tr_color_array) )
		$tr_color = 'background-color:'.$tr_color_array[(string)$table->question_type];

		$outtable .= '<tr style="'.$tr_color.'">';
		$outtable .= '<td><nobr>'.$table->id.'</nobr></td>';
		$outtable .= '<td style="width:45%;padding:1px"><div id="title_text_'.$q_count.'" style="border:0px solid #000000;padding:2px">'.$table->title.'</div>';

		$outtable .= '<td style="text-align:center">'.$table->question_type.'</td>';
		$outtable .= '<td style="text-align:right;background-color:#fff">'.$table->tablesize."</td>";	
		$outtable .= '<td style="text-align:center">'.$table->name.'</td>';
	
		if( is_object($table->varible) ){
			if( $table->question_type=='scale' ){
				$outtable .= '<td rowspan="'.$table->length.'" style="width:30%">';
			}else{
				$outtable .= '<td style="width:30%">';
			}
			$outtable .= '<div style="line-height:25px;max-height:200px;overflow-y:auto">';
			if( $table->question_type=='checkbox' ){
				$outtable .= '<div>1 : 有 &nbsp;&nbsp; 0 : 無</div>';
			}else{
				foreach($table->varible as $item){
					$itemAttr = $item->attributes();
					$outtable .= '<div>'.$itemAttr["value"].'  :  '.$item.'</div>';
				}
			}
			$outtable .= '</div>';
			$outtable .= '</td>';
		}else{
			if( $table->question_type!='scale' ){
				$outtable .= '<td style="width:30%;height:100%;min-height:100%"></td>';
			}
		}

		$outtable .= '<td><p class="ruletip">'.$table->ruletip.'</p></td>';
		$outtable .= '</tr>';
	}else{
		$page = $table->page+1;
		$outtable .= '<tr><td colspan="7" style="text-align:center;background-color:#c00000;color:#ffffff">第'.$table->page.'頁</td></tr>';
		$outtable .= '<tr><th>題號</th><th>標題</th><th>題目類型</th><th>欄位大小</th><th>資料庫欄位</th><th>選項名稱</th><th>填答說明</th></tr>'; 		
	}
}

?>
<head>
<style>
.ruletip {
	color:#ff80ff;
}
</style>
</head>

<div class="ui basic segment">
<table class="ui celled table">
	<?=$outtable?>
</table>
</div>
