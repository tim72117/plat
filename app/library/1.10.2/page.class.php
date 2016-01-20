<?php

namespace Plat\Files;

class page {
	
	public $inifile = '';
	public $page = '';
	public $node = '';
	public $allpage = 0;
	public $question_array = '';
	public $percent = 0;
	public $title = '';
	public $xmlfile_array = '';
	public $jsfile_array = '';
	public $checkfile_array = '';
	public $tablename = '';	
	public $setFiles = '';
	public $columnName = '';
	public $root = './';
	public $uid = '';
	public $newcid = '';
	public $is_get_session_uid = false;
	public $is_get_session_newcid = false;
	public $is_show_all_question = false;
	public $option = NULL;
	public $question_html = '';
	public $name_array = [];
	public $hide = [];
	
	function init($option) {	
		
		$this->option = $option;		

		isset($this->option["cssfile"]) && $cssfile_array = $this->option["cssfile"];

		$this->tablename = $this->option["tablename"];
		$this->syear = $this->option['syear'];	
		$this->title = $this->option['title'];			

		isset($this->option["randomQuesRoot"]) && $this->randomQuesRoot = $this->option["randomQuesRoot"];
		isset($this->option["randomQuesScale"]) && $this->randomQuesScale = $this->option["randomQuesScale"];
		isset($this->option["isShunt"]) && $this->isShunt = $this->option["isShunt"];
		isset($this->option["voice"]) && $_SESSION['voice'] = $this->option["voice"];		
		isset($this->option["randomQuesScaleControlSessionName"]) && $_SESSION['randomQuesScaleControlSessionName'] = $this->option["randomQuesScaleControlSessionName"];

		$this->is_get_session_uid && $this->get_session_uid();

		file_exists($this->root.'/data/pageinfo.xml') && $this->pageinfo = simplexml_load_file($this->root.'/data/pageinfo.xml');		

		$this->xmlfile_array = $this->pageinfo->p;	
		$this->allpage = count($this->xmlfile_array);
		
	}
	
	function loadxml(){
		//-------------------------------------------------------------------載入XML開始
	
		$xmlfile_path = $this->root.'/data/'.$this->pageinfo->p[(int)$this->page]->xmlfile;
		if( !file_exists($xmlfile_path) ){ exit; }
		
		$this->question_array = simplexml_load_file($xmlfile_path);
		
		if( $this->question_array==false ){ exit; }
		
		//-------------------------------------------------------------------載入XML結束
		$this->percent = floor((($this->page+1)/$this->allpage)*100);		
	}
	
	function creat_add(){
		!$this->checknewcid() && exit;
		
		date_default_timezone_set("Asia/Taipei");	
		$today = date("Y/n/d H:i:s");
		
		$sqlStr = "INSERT INTO ".$this->tablename."_add_pstat (uid,newcid,ctime) VALUES('".$this->uid."','".$this->newcid."','$today')";
		$this->db->query($sqlStr);
		
		$sqlStr = "SELECT page FROM ".$this->tablename."_add_pstat WHERE newcid='".$this->newcid."'";
		$obj = $this->db->objects($sqlStr);
		
		if( !is_object($obj) ){
			header("Location: ./");
			exit;
		}else{
			return $obj;
		}	
	}
	
	function bulidQuestionShow(){
		$buildQuestionShow = 'app\\library\\'.$this->option['buildQuestion'].'\\buildQuestionShow';
		$question_amount = count($this->question_array->question);
		$buildQuestionShow::getData($this->option["tablename"].'_page'.($this->page+1));		
		for($i=0;$i<$question_amount;$i++){
			$question = $this->question_array->question[$i];
			if($question->getName()=="question"){
				$this->question_html .= $buildQuestionShow::build($question,$this->question_array,0,"no");
			}
		}
	}	
	
	function bulidQuestion($num){
		
		$buildQuestion = 'app\\library\\'.$this->option['buildQuestion'].'\\buildQuestion';
		$buildQuestion::$hide = $this->hide;
		
		$question_amount = count($this->question_array->question);
		
				
		$isfixedQArray = array();		
		$nofixedQArray = array();
		
		for($qi=0;$qi<$question_amount;$qi++){
			
			$qAttr = $this->question_array->question[$qi]->attributes();
			if( isset($qAttr['fixed']) ){
				array_push($isfixedQArray,$qi);
			}else{
				array_push($nofixedQArray,$qi);
			}
		}
		
		if( isset($this->isShunt) && $this->isShunt!='' ){
			$_SESSION['isShuntArray'] = explode(',',$this->isShunt);
		}
		
		
		$_SESSION['randomQuesRoot'] = $this->randomQuesRoot;
		$_SESSION['randomQuesScale'] = $this->randomQuesScale;
		

		
		if( $this->randomQuesRoot==1 ){
			shuffle($nofixedQArray);			
		}
		//var_dump($a);
		$count_nofixedQ_i = 0;
		
		$start = 0;
		$amount = $num==0 ? $question_amount : $num;
		
		//$num_limit = ($this->node+$num>$question_amount) ? $question_amount : $this->node+$num-1;
		
		for($i=$start;$i<$amount;$i++){
			
			
			if( in_array($i,$isfixedQArray) || true ){//test
				$randQi = $i;
			}else{				
				
				$randQi = $nofixedQArray[$count_nofixedQ_i];
				$count_nofixedQ_i++;
			}
			
			
			$question = $this->question_array->question[$randQi];

			if($question->getName()=="question"){
				$this->question_html .= $buildQuestion::build($question,$this->question_array,0,"no");
			}
			
			
		}
		$this->name_array = $buildQuestion::$name;
		
	}
	
	function buildQuestionEvent(){
		$buildQuestionEvent = 'app\\library\\'.$this->option['buildQuestion'].'\\buildQuestionEvent';
		$javascript = $buildQuestionEvent::buildEvent($this->question_array);
		//-------------------------------------------------------------------載入額外事件JS開始
		$xmlfile = $this->pageinfo->p[(int)$this->page]->xmlfile;
		if( $xmlfile!='' ){				
			if (false !== $pos = strripos($xmlfile, '.')) {
				$fileName = substr($xmlfile, 0, $pos);
			}
			$jsfile = $fileName.'.js';
			if( file_exists($this->root.'/'.$jsfile) ){
				 $javascript .= file_get_contents($this->root.'/'.$jsfile, FILE_USE_INCLUDE_PATH);
			}
		}
		//-------------------------------------------------------------------載入額外事件JS
		
		return $javascript;
		
		$question_answer = $this->question_array->xpath("//answer[@link]");
		
		include_once($this->root.'../../class/JSON.php');
		$json = new Services_JSON();
		
		
		foreach($question_answer as $answer){
			$attr = $answer->attributes();
			$linkObj = $json->decode($attr['link']);
			
			foreach($linkObj as $key => $linkRull){
				echo "\$('select[name=$key]').change(function(){";	
			
							
				echo "\$('select[name=".$answer->name."]').children('option[value!=-1]:not([orgin])').remove();";
								
				echo "if( \$(this).val()!='-1' ){";				
				echo "var xml = $.ajax({";
				echo "url: 'data/".$answer->xmlfile."',";
				echo "async: false";
				echo "}).responseXML;";	
							
				echo "tpl = [];";
				echo "i=0;";
				
				
				
				
				echo "\$(xml).children('answer').children('item[".$linkRull."='+\$(this).val()+']').each(function(){";
				
				echo "var \$item = \$(this);";
				echo "tpl[i++] = '<option value=\"';";
				echo "tpl[i++] = \$item.attr('value');";
				echo "tpl[i++] = '\">';";
				echo "tpl[i++] = \$item.text();";
				echo "tpl[i++] = '</option>';";
				
				echo "});";
				echo "$(tpl.join('')).insertAfter($('select[name=".$answer->name."] option[value=-1]'));";
				
				
				
				echo "}";
				
				echo "$('select[name=".$answer->name."]').triggerHandler('change');";
								
				echo "});";
			}
		}
		
		

	}
	
	function buildQuestionEvent_check(){
		$javascript = '';
		//-------------------------------------------------------------------載入額外事件JS開始
		$xmlfile = $this->pageinfo->p[(int)$this->page]->xmlfile;
		if( $xmlfile!='' ){				
			if (false !== $pos = strripos($xmlfile, '.')) {
				$fileName = substr($xmlfile, 0, $pos);
			}
			
			$jsfile = $fileName.'_check.js';
			if( file_exists($this->root.'/'.$jsfile) ){
				 $javascript .= file_get_contents($this->root.'/'.$jsfile, FILE_USE_INCLUDE_PATH);
			}
		}
		//-------------------------------------------------------------------載入額外事件JS
		return $javascript;
		
		//-------------------------------------------------------------------載入檢查值JS開始
		
		if($this->pageinfo->p[(int)$this->page]->checkfile!="")
		include_once('./js/'.$this->pageinfo->p[(int)$this->page]->checkfile);
		//-------------------------------------------------------------------載入檢查值JS
	}
}
