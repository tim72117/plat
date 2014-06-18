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

$dataroot = ques_path().'/ques/data/'.$config['rootdir'];

$pageinfo_file = $dataroot.'/data/pageinfo.xml';
if( !file_exists($pageinfo_file) ){
	header('Location: ./');
	echo 'error pageinfo.xml';
	exit; 
};
$pageinfo = simplexml_load_file($pageinfo_file);
$page_array = $pageinfo->p;


$item_text_array = array();


$page = 0;
foreach($page_array as $xmlfile) {
	$xmlfile_real = $dataroot.'/data/'.$xmlfile->xmlfile;
	$item_text_array[$page] = readTextTemp($xmlfile_real);
	$page++;
}	


$table_array = array();
$page = 0;
foreach($page_array as $xmlfile) {
	$tdobj = new tdobj();
	$tdobj->objtype = "title";
	$tdobj->page = $page+1;
	array_push($table_array,$tdobj);
	$xmlfile_real = $dataroot.'/data/'.$xmlfile->xmlfile;

	$item_text_array_temp = $item_text_array[$page];
	readSPSS($xmlfile_real,$table_array,$item_text_array_temp,$page);
	$page++;
}	


function readSPSS($xmlfile,&$table_array,$item_text_array_temp,$page) {
	if( !is_file($xmlfile) )
		return false;
	
	$question_array = simplexml_load_file($xmlfile); 
	
	foreach($question_array as $question){
		$id = $question->id;
		$subText = "";
		$name = $question->answer->name;
				
		if( $question->getName()=="explain" ){
			$tdobj = new tdobj();
			$tdobj->objtype = "question";
			$tdobj->name = "";
			$tdobj->id = "";
			$tdobj->question_type = "explain";
			$tdobj->nullvalue = "";
			$tdobj->tablesize = "";
			$tdobj->title = (string)$question;
			$tdobj->ruletip = "";
			//array_push($table_array,$tdobj);			
		}
		
		if ($question->getName()=="question" || $question->getName()=="question_sub")
		switch($question->type){
			case "checkbox":
			$subText = get_parent_text($question_array,$item_text_array_temp,(string)$question->id);
			
			$tdobj = new tdobj();
			$tdobj->objtype = "question";
			$tdobj->name = "";
			$tdobj->id = $id;
			$tdobj->question_type = "checkbox_head";
			$tdobj->nullvalue = "";
			$tdobj->tablesize = "";
			$tdobj->title = (string)$question->title;
			$tdobj->ruletip = (string)$question->ruletip;
			//array_push($table_array,$tdobj);
			
			foreach($question->answer->item as $item){
				$attr = $item->attributes();				
				$name = $attr["name"];
				
				$tdobj = new tdobj();
				$tdobj->objtype = "question";
				$tdobj->name = $name;
				$tdobj->id = "";
				$tdobj->question_type = $question->type;
				if( !isset($nullValue[$page][(string)$name]) )
					$nullValue[$page][(string)$name] = '';
				$tdobj->nullvalue = $nullValue[$page][(string)$name];
				$tdobj->tablesize = !isset($tablesize[$page][(string)$name]) ?: $tablesize[$page][(string)$name];
				$tdobj->title = $subText.'『'.(string)$item.'』';
				$tdobj->varible = simplexml_load_string("<?xml version='1.0'?><answer><item value='0'>沒填答</item><item value='1'>有填答</item></answer>");
				
				array_push($table_array,$tdobj);
				
			}			
			break;
			case "scale":
			$subText = get_parent_text($question_array,$item_text_array_temp,(string)$question->id);
			
			$tdobj = new tdobj();
			$tdobj->objtype = "question";
			$tdobj->name = "";
			$tdobj->id = $id;
			$tdobj->question_type = "scale_head";
			$tdobj->nullvalue = "";
			$tdobj->tablesize = "";
			$tdobj->title = $subText;
			$tdobj->ruletip = (string)$question->ruletip;
			//array_push($table_array,$tdobj);
			
			foreach($question->answer->item as $key => $item){
				$attr = $item->attributes();
				$name = $attr["name"];
				
				$tdobj = new tdobj();
				$tdobj->objtype = "question";
				$tdobj->name = $name;
				$tdobj->id = $id;
				$tdobj->question_type = $question->type;
				if( !isset($nullValue[$page][(string)$name]) )
					$nullValue[$page][(string)$name] = '';
				$tdobj->nullvalue = $nullValue[$page][(string)$name];
				$tdobj->tablesize = !isset($tablesize[$page][(string)$name]) ?: $tablesize[$page][(string)$name];
				$tdobj->title = $subText.'『'.(string)$item.'』';
				$tdobj->varible = $question->answer->degree;
				
				array_push($table_array,$tdobj);
				
			}			
			break;
			case "radio":	
			$subText = get_parent_text($question_array,$item_text_array_temp,(string)$question->id);
			//if(strlen($subText)>150)
			//$subText = substr($subText,0,150);
			
			$tdobj = new tdobj();
			$tdobj->objtype = "question";
			$tdobj->name = $name;
			$tdobj->id = $id;
			$tdobj->question_type = $question->type;
			if( !isset($nullValue[$page][(string)$name]) )
				$nullValue[$page][(string)$name] = '';
			$tdobj->nullvalue = $nullValue[$page][(string)$name];
			$tdobj->tablesize = !isset($tablesize[$page][(string)$name]) ?: $tablesize[$page][(string)$name];
			$tdobj->title = $subText;
			$tdobj->ruletip = (string)$question->ruletip;
			$tdobj->varible = $question->answer->item;
			
			array_push($table_array,$tdobj);
			
			break;
			case "text":			
			case "textarea":
			
			$tdobj = new tdobj();
			$tdobj->objtype = "question";
			$tdobj->name = $name;
			$tdobj->id = $id;
			$tdobj->question_type = $question->type;
			if( !isset($nullValue[$page][(string)$name]) )
				$nullValue[$page][(string)$name] = '';
			$tdobj->nullvalue = $nullValue[$page][(string)$name];
			if( !isset($tablesize[$page][(string)$name]) )
					$tablesize[$page][(string)$name] = '';
			$tdobj->tablesize = $tablesize[$page][(string)$name];
			$tdobj->title = (string)$question->title;
			$tdobj->ruletip = (string)$question->ruletip;
			$tdobj->varible = $question->answer->item;
			
			//array_push($table_array,$tdobj);
			

			break;
			case "select":
			$subText = get_parent_text($question_array,$item_text_array_temp,(string)$question->id);
			
			$tdobj = new tdobj();
			$tdobj->objtype = "question";
			$tdobj->name = $name;
			$tdobj->id = $id;
			$tdobj->question_type = $question->type;
			if( !isset($nullValue[$page][(string)$name]) )
				$nullValue[$page][(string)$name] = '';
			$tdobj->nullvalue = $nullValue[$page][(string)$name];
			$tdobj->tablesize = !isset($tablesize[$page][(string)$name]) ?: $tablesize[$page][(string)$name];
			$tdobj->title = $subText;
			$tdobj->ruletip = (string)$question->ruletip;
			$tdobj->varible = $question->answer->item;
			
			array_push($table_array,$tdobj);			
			
			break;
			case "text_phone":
			foreach($question->answer->item as $item){
				$attr = $item->attributes();
				$name = $attr["name"];
				
				$tdobj = new tdobj();
				$tdobj->objtype = "question";
				$tdobj->name = $name;
				$tdobj->id = $id;
				$tdobj->question_type = $question->type;
				if( !isset($nullValue[$page][(string)$name]) )
					$nullValue[$page][(string)$name] = '';
				$tdobj->nullvalue = $nullValue[$page][(string)$name];
				$tdobj->tablesize = $tablesize[$page][(string)$name];
				$tdobj->title = (string)$question->title." - ".$item;
				$tdobj->ruletip = (string)$question->ruletip;
				
				//array_push($table_array,$tdobj);
				
			}	
			break;
			case "list":
			$subText = get_parent_text($question_array,$item_text_array_temp,(string)$question->id);
			
			$tdobj = new tdobj();
			$tdobj->objtype = "question";
			$tdobj->name = "";
			$tdobj->id = "";
			$tdobj->question_type = "list";
			$tdobj->nullvalue = "";
			$tdobj->tablesize = "";
			$tdobj->title = $subText;
			$tdobj->ruletip = (string)$question->ruletip;
			//array_push($table_array,$tdobj);
			
			break;	
		}
	}
}


function readTextTemp($xmlfile){
	if( !is_file($xmlfile) )
		return false;
	
	$question_array = simplexml_load_file($xmlfile); 	
	$item_text_array_temp = array();
	
	$item_parent_array = $question_array->xpath("//item[@sub]/parent::answer/parent::*");
	
	if( is_array($item_parent_array) )
	foreach($item_parent_array as $item_parent){
		$id_parent = (string)$item_parent->id;
		$item_array = $item_parent->xpath("answer/item[@sub]");
		foreach($item_array as $item){
			$itemAttr = $item->attributes();
			foreach(explode(",",$itemAttr["sub"]) as $itemSub){
				
				$title_this = $question_array->xpath("//id[.='".(string)$itemSub."']/parent::*");
				if( isset($title_this[0]) ){
					$title_text = (string)$title_this[0]->title;
					 
				}else{
					$title_text = '';
				}
				
				switch((string)$item_parent->type){
				case "radio":
				case "select":
					$parent_text = '『'.(string)$item.'』 - '.'<span style="text-decoration:underline">'.$title_text.'</span>';
				break;
				case "list":
					$parent_text = ' - '.'<span style="text-decoration:underline">'.$title_text.'</span>';
				break;
				case "checkbox":
					$parent_text = '『'.(string)$item.'』 - '.'<span style="text-decoration:underline">'.$title_text.'</span>';
				break;
				case "scale":
					$parent_text = '『'.(string)$item.'』 - '.'<span style="text-decoration:underline">'.$title_text.'</span>';
				break;				
				default:
					$parent_text = 'def'.(string)$itemSub.(string)$item_parent->type;
				break;
				}				
				if( !array_key_exists((string)$itemSub, $item_text_array_temp) ){$item_text_array_temp[(string)$itemSub] = array($id_parent,$parent_text);}
			}
		}
	}
	return $item_text_array_temp;
}

function get_parent_text($question_array,$item_text_array_temp,$id_this){
	if( array_key_exists($id_this,$item_text_array_temp) ){		
		$id_parent = $item_text_array_temp[$id_this][0];	
		return get_parent_text($question_array,$item_text_array_temp,$id_parent).$item_text_array_temp[$id_this][1];			
	}else{			
		$target = $question_array->xpath("//id[.='".$id_this."']/parent::*");			
		return '<span style="text-decoration:underline">'.$target[0]->title.'</span>';			
	}
}


		





?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-TW" lang="zh-TW">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=10" />
<meta http-equiv="X-UA-Compatible" content="IE=9" />
<meta http-equiv="X-UA-Compatible" content="IE=8" />
<title>問卷spss</title>

<script type="text/javascript" src="<?=asset('jquery-1.10.2.min.js')?>"></script>
<!--[if lt IE 9]><script src="js/html5shiv.js"></script><![endif]-->

<link href="../css/text.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?=asset('css/onepcssgrid.css')?>" />
<link rel="stylesheet" href="<?=asset('css/management/share.css')?>" />
<link rel="stylesheet" href="<?=asset('css/management/share.index.css')?>" />

<style type="text/css">
<!--
td { border:1px solid #000; }
th { border:1px solid #000; }
-->
body {
	font-family: '微軟正黑體';
	font-size: 14px;
}
.ruletip {
	color:#ff80ff;
}
</style>
<script language='javascript'>
$(document).ready(function(){

	

});		
</script>
</head>
<body>
	
<div class="onepcssgrid-1000" style="margin-top:0">
	<div class="onerow" style="border-top: 0px solid #bebebe;border-bottom: 0px solid #fff; background-color:#fff">
		<div class="colfull">
			<?=$child_tab?>
		</div>
	</div>
	<div style="clear:both"></div>
</div>

<table cellspacing="0" cellpadding="0" border="0">
<?
$q_count = 0;
$question_text_array = array();
$question_text = '';
$varible_text = '';
foreach($table_array as $table){
	
	if($table->objtype=="question"){
		$q_count = $q_count+1;
	
	array_push($question_text_array,$table->name.' \'<span style="font-size:11px">'.strip_tags($table->title).'</span>\'');
	//$question_text .= implode('/',$question_text_array);

	$varible_text .= 'VALUE LABELS<br />';
	$varible_text .= $table->name.'<br />';
	
		$varible_text_array = array();
		if(is_object($table->varible))
		foreach($table->varible as $item){
			$itemAttr = $item->attributes();			
			array_push($varible_text_array,$itemAttr["value"].' \''.(string)$item.'\'');			
		}
		if($table->nullvalue)
		array_push($varible_text_array,$table->nullvalue.' \'無須填答\'');			
				
		$varible_text .= implode('<br />',$varible_text_array);
		
		$varible_text .= '.<br />';
		$varible_text .= '<br />';
		$varible_text .= '<br />';
		
	}
}

echo 'VARIABLE LABELS<br />';
echo $question_text;
echo implode('<br />/',$question_text_array);
echo '<br />.<br />';
echo 'EXECUTE.<br />';
echo '<br />';
echo $varible_text;
echo '<br />';
?>
</table>
</body>
</html>