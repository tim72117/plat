<?php
$_SESSION['route'] = 'census';
$_SESSION['is_test'] = true;
$_SESSION['account_name'] = '';
$_SESSION['site'] = 'used';
$login_text1 = '';

$census = DB::reconnect('sqlsrv_analysis')->table('census_info')->where('used_site', 'used')->get();

//if( $_SESSION['session_logined'] && isset($_SESSION['session_lid']) ){
//	$sql_update = " UPDATE log_login SET rtime=NOW() WHERE lid='".$_SESSION['session_lid']."'";
//	$db->Query($sql_update);
//}

$clouds = array();
//$sql = ' SELECT census_code_year,census_text_title,census_target_people,isready,CID FROM census_info WHERE used_site="'.$_SESSION['site'].'" AND is_edit=0 ORDER BY census_code_year DESC,census_code_title';

foreach($census as $result){
   $dataBaseName = ($result->census_code_year-1911).$result->census_text_title;
   !array_key_exists($result->census_target_people, $clouds) && $clouds[$result->census_target_people] = '';
   $clouds[$result->census_target_people] .= '<li isready="'.$result->isready.'" CID="'.$result->CID.'"><a href="#">'.$dataBaseName.'</a></li>'; 
}

include_once('loginbar.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$title?></title>
<script type="text/javascript" src="/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="/analysis/js/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="/analysis/use/js/SpryTabbedPanels.js"></script>
<script type="text/javascript" src="/analysis/use/js/timer.js"></script>
<script type="text/javascript" src="/analysis/use/js/question.js"></script>

<link href="/analysis/js/jquery-ui-1.10.3.custom/css/smoothness/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" type="text/css" />

<link href="/analysis/use/css/used/index.css" rel="stylesheet" type="text/css" />
<link href="/analysis/use/css/used/page.css" rel="stylesheet" type="text/css" />
<link href="/analysis/use/css/used/niutuku.css" rel="stylesheet" type="text/css" />
<link href="/analysis/use/css/used/page-2.css" rel="stylesheet" type="text/css" />

<link href="/analysis/use/css/used/SpryTabbedPanels.css" rel="stylesheet" type="text/css" />


<script type="text/javascript">
var is_test = <?=$_SESSION['is_test']?'true':'false'?>;
$(document).ready(function(){	

	$( "#down_box" ).dialog({
		width:420,
		modal: true,
		resizable:false,
		position:['center',250],
		autoOpen: false
	});	

	var time_count = new timer1.count({
		msg1:'還剩 [curmin] 分 [cursec] 秒自動登出',
		msg2:'還剩 [cursec] 秒自動登出',
		timeout_1:function(){

		},
		timeout:function(){ location.replace('./logout.php') },
		reset1:function(css){

		}
	});

	var TabbedPanels1 = new Spry.Widget.TabbedPanels("TabbedPanels1");
	
	$('#btn_next').mousedown(function(){
		var census_selected_obj = $('ul.page5_list li');
		if(census_selected_obj.is('[selected]')){
			if( census_selected_obj.filter('[selected][CID]').attr('isready')!=1 && !is_test ){
				alert('資料測試中，如有問題請回報。');
				return false;
			}			
			$('#intoDataBase').children('input[name=CID]').val(census_selected_obj.filter('[selected][CID]').attr('CID'));
			
			$('#intoDataBase').submit();
		}
	});
	$('#btn_prev').mousedown(function(){
		location.replace("intro.php");
	});
	$('#down_t0').mousedown(function(){
		$( "#down_box" ).dialog('open');
	});
	
	
	$('ul.page5_list li').mousedown(function(){
		$('ul.page5_list li').removeAttr('selected').removeClass('selected');;
		$(this).attr('selected','selected').addClass('selected');
					
		$.getJSON('/analysis/get/get_census_info?CID='+$(this).attr('CID'),function(data) {
				console.log(data);	
				
				$('#text_title').text(data.dataBaseName);
				$('#census_time_start').text(data.census_time_start);
				$('#census_time_end').text(data.census_time_end);
				$('#census_method_name').text(data.census_method_name);				
				$('#census_target').text(data.census_target);
				$('#census_quantity_total').text(data.census_quantity_total);
				$('#census_quantity_sample').text(data.census_quantity_sample);
				$('#census_quantity_gets').text(data.census_quantity_gets);
				$('#census_quantity_percent').text(data.census_quantity_percent+'%');
				
				if(data.census_method=="sampling"){
					$('#census_quantity_sample_line').show();
				}else{
					$('#census_quantity_sample_line').hide();
				}
								
				if(data.link_questionaire!=''){
					$('#down_t1').removeAttr('disabled').attr('href',data.link_questionaire);
				}else{
					$('#down_t1').attr('disabled','disabled');
				}
				if(data.link_report!=''){
					$('#down_t2').removeAttr('disabled').attr('href',data.link_report);
				}else{
					$('#down_t2').attr('disabled','disabled');
				}

				
				$('.download_new').show();		

				if(data.isready!=1){
					$('#text_title').append('<span style="color:red;font-size:.8em">(資料測試中，如有問題請回報。)</span>');
				}
				
				$('#q_contextlist tbody').empty();
				for(i=0;i<data.part_inf.length;i++)
				{
					$('#q_contextlist tbody').append('<tr><td align="right">第'+(data.part_inf[i][0])+'部分:</td><td>'+data.part_inf[i][1]+'</td></tr>');					
				}		
				
				console.log(data.pointer_array);
				for(i in data.pointer_array){
					$('#pointer').append('<div>'+data.pointer_array[i]['quesStat']+'</div>');
				}
				
		}).error(function(e){ console.log(e); });	
	});
	
	$('#down_t2').click(function(){
		if($(this).is('[disabled]')){
			alert('全國性分析報告尚未釋出，請稍後再進行下載。');
			return false;
		}
	});
	$('.download a').click(function(){
		if($(this).is('[disabled]')){
			//alert('尚未提供下載。');
			return false;
		}
	});

	/*
	$.getJSON('get_CID.php',
		function(data) {
			if(data.CID!='empty'){
				var clouds = $('ul.page5_list li[CID='+data.CID+']').parent('ul').attr('clouds');
				TabbedPanels1.showPanel(clouds);
				$('ul.page5_list li[CID='+data.CID+']').mousedown();
			}
	});
	*/
	
});

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}
</script>
<style>
.page5_list li{
	font-family:微軟正黑體;
}
.page5_list li.selected{
	background-color:#ccc;	
}
#btn_next{
	width:200px;
	height:40px;
	margin-left:10px
}
#way {
	text-align: center;
	margin-top: 10px;
}
#btn_prev{
	width:200px;
	height:40px;
}
.download_new {
	height: 29px;
	width: 161px;
	background-image: url(/analysis/use/css/used/images/page-5_btn1.jpg);
	margin: 0 0px 0px 0px;
	line-height: 29px;
	text-align: left;
	font-family: "微軟正黑體";
	font-size: 14px;
	color: #000;
}
</style>
</head>

<body>

<div class="top_bar">
<div id="path">
  <div id="info"><?=$login_text2?></div><?=$login_text1?>
</div>
</div>

<div id="page">

<div id="banner"><img src="/analysis/use/css/used/banner.png" /></div>
<div id="book_bg">
<div><img src="/analysis/use/css/used/images/page-5-2_05.jpg" width="960" height="49" /></div>
<div id="page5_box">
<div id="page5_left">
<div class="page5-title" style="font-family:微軟正黑體">資料庫類型<span style="color:#999;font-size:.7em"><img src="./images/warning.png" />Step 1</span></div>
<div class="page5-title" style="font-family:微軟正黑體">資料庫<span style="color:#999;font-size:.7em"><img src="./images/warning.png" />Step 2</span></div>

<div id="TabbedPanels1" class="TabbedPanels">
  <ul class="TabbedPanelsTabGroup">
    <li class="TabbedPanelsTab" tabindex="0" id="H1">高一專一</li>
	<li class="TabbedPanelsTab" tabindex="0" id="TE">教師</li>
	<li class="TabbedPanelsTab" tabindex="0" id="H2P">高二家長</li>
</ul>
  <div class="TabbedPanelsContentGroup">
    <div class="TabbedPanelsContent">
     <ul class="page5_list" clouds="H1"><?=$clouds["H1"]?></ul>
    </div>
    <div class="TabbedPanelsContent">
     <ul class="page5_list" clouds="TE"><?=$clouds["TE"]?></ul>
    </div>
    <div class="TabbedPanelsContent">
     <ul class="page5_list" clouds="H2P"><?=$clouds["H2P"]?></ul>
    </div>
</div>
</div>
</div>
<div id="page5-info">
  <div class="page5-title" style="margin-left:10px;font-family:微軟正黑體">資料庫簡介</div>
  <div id="info_text">
  <div id="text_title"><span style="color:#900">請選擇資料庫</span></div>
  <div class="text_list">
    <table width="400" border="0" cellspacing="0" cellpadding="0" style="font-size:.8em;line-height: 20px">
    <tr>
      <td width="140" align="right">調查開始時間 :</td>
      <td width="260" id="census_time_start"></td>
    </tr>
    <tr>
      <td align="right">調查結束時間 :</td>
      <td id="census_time_end"></td>
    </tr>
    <tr>
      <td align="right">調查方式 :</td>
      <td id="census_method_name"></td>
    </tr>
    <tr>
      <td align="right">調查對象 :</td>
      <td id="census_target"></td>
    </tr>
    <tr>
      <td align="right">母體數量 :</td>
      <td id="census_quantity_total"></td>
    </tr>
     <tr id="census_quantity_sample_line" style="display:none">
      <td align="right">抽樣數 :</td>
      <td id="census_quantity_sample"></td>
    </tr>
    <tr>
      <td align="right">回收數 :</td>
      <td id="census_quantity_gets"></td>
    </tr>
    <tr>
      <td align="right">回收率 :</td>
      <td id="census_quantity_percent"></td>
    </tr>
	<tr>
      <td align="right">問卷內容 :</td>
      <td><div class="download_new" style="display:none"><input type="button" style="width:70px"  value="開啟" id="down_t0" /></div></td>
    </tr>
  </table>
	  
	  <div id="pointer" style="font-size:.8em;line-height: 20px;overflow-y: scroll;height:200px">
		  
	  </div>

  </div>

    
<!--
  <div class="text_list" style="max-height:170px;overflow-y:auto">
    <table id="q_contextlist" width="400" border="0" cellspacing="0" cellpadding="0">
    <thead>
      <tr>
        <td width="120" align="right" class="black" style="font-size:.9em">問卷內容 :</td>
        <td width="280">&nbsp;</td>
      </tr>
     </thead>
     <tbody style="font-size:.8em;line-height: 20px"></tbody>
    </table>   
  </div>
 -->
   

  </div>

    <div id="way">
        <input type="button" value="回上一頁" id="btn_prev" />
        <input type="button" value="進入資料庫" id="btn_next" />
    </div>
    </div>
</div>

</div>




</div>

<div id="footer" style="margin:0 auto;width:960px">
 <div id="footer_text"><?=$footbar?></div>
</div>

<div id="down_box" title="問卷內容">
   <table id="q_contextlist" width="400" border="0" cellspacing="0" cellpadding="0">
    <thead>
      <tr>
        <td width="120" align="right" class="black" style="font-size:.9em">問卷內容 :</td>
        <td width="280">&nbsp;</td>
      </tr>
     </thead>
     <tbody style="font-size:.8em;line-height: 20px"></tbody>
    </table>   
	<div class="download" style="margin-left:30px;margin-right:0"><a id="down_t1" disabled="disabled" href="#" target="_blank">問卷下載</a></div>
	<div class="download" style="margin-left:10px;margin-right:0"><a id="down_t2" disabled="disabled" href="#" target="_blank">全國報告</a></div>
</div>

<form id="intoDataBase" action="/analysis/menu" method="get">
   <input name="CID" type="hidden" value="" />
</form>


</body>
</html>