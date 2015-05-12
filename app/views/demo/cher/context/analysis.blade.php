<?php
//-----------v2.0
//次數分配、交叉表
//新增 平均數比較、相關分析、迴歸分析
session_start();

$QID_selected = '71103';

class history{
	var $filename = array();
	var $ctime = '';
	var $type = 'cross';
	var $type_name = '交叉表';
	var $target = '';
}
$history = new history();
$_SESSION['history_session'] = $history;

//include_once('class/authority.class.php');

//$db = new DBnew();

$CID = 1;
$used_site = 'used';


//$sql = " SELECT census_tablename,census_code_year,census_text_title FROM census_info WHERE CID=$CID AND used_site='$used_site'";
//$resultAry = $db->getData($sql,"assoc");
//$dataBaseName = ($resultAry[0]['census_code_year']-1911).$resultAry[0]['census_text_title'];
//$census_tablename = $resultAry[0]['census_tablename'];
//$census_year3 = sprintf("%03d",$resultAry[0]['census_code_year']-1911);
//
//
//$_SESSION['census_year3'] = $census_year3;
//$_SESSION['census_tablename'] = $census_tablename;
//
//$authority_obj_r = new authority();
//$authority_obj_r->reflash();
	

//if( $_SESSION['userType']=='school' || $_SESSION['userType']=='department' ){
//
//
//	
//	$scid = $_SESSION['scid'];
//	
//	if( isset($_SESSION['school']) ){
//		$sql = " SELECT scid,sname  FROM school_new WHERE uid='".$_SESSION['school']."' AND year='101'";	
//		$resultAry = $db->getData($sql,"assoc");	
//		$scid = $resultAry[0]['scid'];
//	}
//	
//	$sql = " SELECT uid,sname  FROM school_new WHERE scid=$scid AND year='$census_year3'";
//
//	$resultAry = $db->getData($sql,"assoc");
//	if(is_array($resultAry)){
//		$census_uid = $resultAry[0]['uid'];
//		$_SESSION['census_uid'] = $census_uid;
//		
//		$census_school_array = array();		
//		foreach($resultAry as $result){
//			$census_school_obj = new stdClass();;
//			$census_school_obj->id = $result['uid'];
//			$census_school_obj->name = $result['sname'];
//			array_push($census_school_array,$census_school_obj);
//		}
//	}	
//}
//
//
//$_SESSION['census_school_array'] = $census_school_array;



//$partName_array_2 = array();
//$sql = " SELECT * FROM census_part WHERE CID=$CID";
//$resultAry = $db->getData($sql,"assoc");
//if(is_array($resultAry))
//foreach($resultAry as $key => $result){
//	$partName_array_2[$result['part']] = $result['part_name'];
//}
//
//$sql = " SELECT QID,question_label,column_name,part FROM question WHERE QID IN($QID_selected)";
//$resultAry = $db->getData($sql,"assoc");
//
//$question_part_new = array();
//if(is_array($resultAry))
//foreach($resultAry as $key => $result){
//	$column_name = $result['column_name'];
//	$part = $result['part'];
//	if( !isset($question_part_new[$part]) ) $question_part_new[$part] = '';
//	$question_part_new[$part] .= '<li class="selectable select_out" title="'.$result['question_label'].'" variableID="'.$result['QID'].'"><span class="file" style="font-size:12px;text-overflow:ellipsis;overflow:hidden;white-space:nowrap">'.$result['question_label'].'</span></li>';
//}
//$question_new = '';
//foreach($question_part_new as $part => $qi){
//	$question_new .= '<li><span class="folder" style="font-size:14px;font-weight:bold">'.$partName_array_2[$part].'</span>';
//	$question_new .= '<ul>';
//	$question_new .= $qi;
//	$question_new .= '</ul>';
//	$question_new .= '</li>';
//}

//include_once('loginbar.php');
//
//if( $_SESSION['site']=='tiped' ){
//	$tb1_inner = 'tb1_inner_tiped.php';
//	$tb2_inner = 'tb2_inner_tiped.php';
//	$tb3_inner = 'tb3_inner_tiped.php';
//	$tb4_inner = 'tb4_inner_tiped.php';
//	$tb5_inner = 'tb5_inner_tiped.php';
//}
//if( $_SESSION['site']=='tted' ){
//	$tb1_inner = 'tb1_inner_tted.php';
//	$tb2_inner = 'tb2_inner_tted.php';
//	$tb3_inner = 'tb3_inner_tted.php';
//	$tb4_inner = 'tb4_inner_tted.php';
//	$tb5_inner = 'tb5_inner_tted.php';
//}
?>
<head>
<title><?//=$title?></title>
<!--<script src="js/jqueryUI/Highcharts-2.3.5/js/highcharts.js"></script>
<script src="js/jqueryUI/Highcharts-2.3.5/js/modules/exporting.src.js"></script>-->
<script src="/js/jquery-ui/tabs/jquery-ui.min.js"></script>
<!--<script src="js/jquery.ui.selectOption.js"></script>
<script src="js/json2.js"></script>
<script src="js/jquery.blockUI.js"></script>
<script src="js/drawfigure.js"></script>
<script src="js/question.js"></script>
<script src="js/block_load.js"></script>
<script src="js/frequence.js"></script>
<script src="js/crosstable.js"></script>
<script src="js/correlation.js"></script>-->



<script src="/analysis/js/jquery.treeview/jquery.treeview.js"></script>


<link rel="stylesheet" href="/analysis/tiped/index.css" />
<link rel="stylesheet" href="/analysis/tiped/page.css" />
<link rel="stylesheet" href="/analysis/tiped/niutuku.css" />
<link rel="stylesheet" href="/analysis/tiped/page-2.css" />
<link rel="stylesheet" href="/analysis/tiped/menu.css" />

<!--<link rel="stylesheet" href="js/jquery.treeview/jquery.treeview.css" />-->
<link rel="stylesheet" href="/js/jquery-ui/tabs/jquery-ui.min.css" />

<script type="text/javascript">
var chart1;
var chartFreqObj;
var tab_panel = null;
$(document).ready(function(){	
	
	var cssfolder = 'css/<?//=$_SESSION['site']?>/images/';
	
	
	$("#tabs").tabs({
		active: 0,
		create: function(event, ui){
			tab_panel = $( "#tabs" ).tabs( "widget" ).find('.ui-tabs-panel:visible');						
		},
		beforeActivate: function(event, ui){
			$('#page7_nav li.ui-selected').removeClass('ui-selected');		

			$('button.selectbtn').removeClass('empty in out full').addClass('empty').attr('state','empty');

			$('ul[name=select_box] li.select_in').remove();
			$('span[name=hints]').show();
			$('#crosstable_preview tr:first td:not(:first)').remove();
			$('#crosstable_preview tr:not(:first)').remove();
	
		},
		activate: function(event, ui){
			tab_panel = $( "#tabs" ).tabs( "widget" ).find('.ui-tabs-panel:not(:hidden)');
		}
	});	

	$('#dialog-modal').dialog({
		width:'80%',
		height:760,
		resizable:true,
		draggable:true,
		position:'top',
		modal:true,
		autoOpen: false,
		open: function(){ $(window).scrollTop(0); }
	});
	$('#dialog-modal').before('<div id="progressbar" style="height:8px;margin-top:1px"></div>');
	$('#progressbar').progressbar( { value: 0 } );
	
	
	$('input[name=ext5]').click(function(){ 
		var typeN = $(this).val();
		
		$('div[name=3type]:not([type='+typeN+'])').fadeOut(0,function(){
			//alert(typeN);
			
		});	
		$('div[name=3type][type='+typeN+']').fadeIn("slow");
	});
	$('input[name=ext5]:checked').triggerHandler('click');
	
	
	
	var udep_4 = $('#udep_4 option');
	$('#udep_2').change(function(){
		$('#udep_4 option').remove('[udep_2]');
		var udep_2 = $('#udep_2').val();
		var filter_text = '[udep_2='+udep_2+']';
		udep_4.filter(filter_text).each(function(){
			$('#udep_4 option:eq(0)').after(this);
		});
	});
	
	
	$("ul.filetree").treeview({
		toggle: function() {
		}
	});	
	
	$(document).ajaxError(function(e,xhr,options){ 
		valInObj = {
			responseText: xhr.responseText,
			url: options.url
		}
		//alert(options.url);
		$.post('class/write_ajaxerror.php',valInObj);
	});
	
	$.ajaxPrefilter( function( options, originalOptions, jqXHR ) {
		//alert(originalOptions.url);
	});
	
					


	$("ul[name=select_box]").on("click", "li.selectable", function() {

		var select_box_img = tab_panel.find('button.selectbtn:not(.full)');
					
		if( $(this).is('.select_out') ){
			if(select_box_img.length>0){
				select_box_img.removeClass('empty in out').addClass('in').attr('state','in');
				$('ul[name=select_box]:not([max_select=1]) li').removeClass('ui-selected');
				$(this).addClass('ui-selected');
			}			
		}
		
		if( $(this).is('.select_in') ){			
			var group = $(this).parent('ul').attr('group');
			$('ul[name=select_box][group='+group+'] li').removeClass('ui-selected');
			$(this).addClass('ui-selected');
			if( $('ul[name=select_box][group='+group+'] li').data('space')!=0 ){
				$('button.selectbtn[group='+group+']').removeClass('empty in out').addClass('out').attr('state','out');
			}
		}

	});
	
	
	$("#page7_main").on("click", "button.selectbtn", function() {
	//$('button.selectbtn').click(function(){
		
		var QID = $('#page7_nav li.ui-selected').attr('variableID');
		var group = $(this).attr('group');
		
		
		switch( $(this).attr('state') ){
			case 'in':
			
				$('ul[name=select_box][group='+group+']').append( $('<li class="selectable select_in ui-selected" QID="'+QID+'"></li>').text($('#page7_nav ul li.ui-selected').text()) );
				$('ul[name=select_box] li.select_out').removeClass('ui-selected');
				
				$('ul[name=select_box][group='+group+']').data('space',$('ul[name=select_box][group='+group+']').attr('max_select')-$('ul[name=select_box][group='+group+']').find('li.select_in').length);
				if( $('ul[name=select_box][group='+group+']').data('space')==0 ){
					$(this).addClass('full');	 
				}
				
				get_variable(QID,group);
				if( group==7 )
				get_variable_compareMeans(QID,group);
	
				$('button.selectbtn.in').removeClass('empty in out').addClass('empty').attr('state','empty');
				$(this).removeClass('empty in out').addClass('out').attr('state','out');

				
			break;
			case 'out':

				$('ul[name=select_box][group='+group+']').find('li.select_in.ui-selected').remove();
				
				if($('ul[name=select_box][group='+group+']').find('li.select_in').length!=0){
					$('ul[name=select_box][group='+group+']').find('li.select_in:last').addClass('ui-selected');
				}else{
					$(this).removeClass('empty in out').addClass('empty').attr('state','empty');
				}
				
				if( group==7 )
				$('#compareMeans_variable_box').hide();
				
				$('#crosstable_preview tr td.context').remove();
				if(group==1){ $('#crosstable_preview tr:first td:not(:first)').remove(); }
				if(group==2){ $('#crosstable_preview tr:not(:first)').remove(); }
				
				$('ul[name=select_box][group='+group+']').data('space',$('ul[name=select_box][group='+group+']').attr('max_select')-$('ul[name=select_box][group='+group+']').find('li.select_in').length);
				
				$(this).removeClass('full');
				
				
				

			break;
		}
		
		if( tab_panel.find('ul[name=select_box]:has(li.select_in)').length==0 ){
			$('span[name=hints]').show();
		}else{
			$('span[name=hints]').hide();
		}
	});
	
		
	

	
	$('#btn_next').mousedown(function(){	
		var tabe = $("#tabs").tabs('option','active');

		var sent_go = true;
			
		
		var QID_map = $.map( $('ul[name=select_box] li[QID]:visible'),function(n,i){return $(n).attr('QID');} );
		var QID_map_string = QID_map.join(",");
		
		
		switch( tabe ){
			case 0:	
				
			
			if( QID_map.length==0 ){
				alert('請選擇分析題目');
				sent_go = false;	
			}
			
			var ext5 = tab_panel.find('input[name=ext5]:checked').val();	
			var get_array = Array();

			switch( ext5 ){
				case '1':
				case '2':
				case '3':
				case '4':				
					
					get_array = $.map(tab_panel.find('input[name=ext3][ext5='+ext5+']:checked'),function(a){ 
						obj = {
							ext4: $(a).val(),
							ext_a1: $(a).attr('ext_a1')
						}
						return obj; 
					});	
					
				break;
			}
			
			if( get_array.length==0 ){ sent_go = false; }			

			valInObj = {
				variableID: QID_map[0],
				dotmount: tab_panel.find('select[name=ext_digit]').val(),
				ext2 :tab_panel.find('input[name_same=ext_weight]:checked').val()
			};

			if(sent_go){
				get_json_combine_frequence(valInObj,get_array,0);
			}
										
			
			break;
			case 1:
				
			if( QID_map.length<2 ){
				alert('請選擇分析變項');
				sent_go = false;	
			}				
			
			var get_array = Array();
			
			get_array = $.map(tab_panel.find('input[name=ext3][ext5=1]:checked'),function(a){ 
				obj = {
					ext4: $(a).val(),
					ext_a1: $(a).attr('ext_a1')
				}
				return obj; 
			});				
			
			
							
			if(get_array.length==0)	sent_go = false;	
			
	
			valInObj = {
				variableID1: QID_map[0],
				variableID2: QID_map[1],
				dotmount: tab_panel.find('select[name=ext_digit]').val(),
				ext2 :tab_panel.find('input[name_same=ext_weight]:checked').val()
			};
			
							
			if(sent_go){
				get_json_combine_cross(0,get_array,valInObj);
			}
				
			break;
			case 2:			
				start_compareMeans(QID_map,tab_panel);			
			break;
			
			case 3:
			
			if( QID_map.length<2 ){
				alert('請選擇兩題以上題目');
				sent_go = false;	
			}
			
			var get_array = Array();
			
			get_array = $.map(tab_panel.find('input[name=target_group]:checked'),function(a){ 
				obj = {
					ext4: $(a).val(),
					ext_a1: $(a).attr('ext_a1')
				};
				return obj; 
			});	
			
			
			valInObj = {
				variableID :QID_map,
				dotmount :tab_panel.find('select[name=dotmount]').val(),
				is_weight :tab_panel.find('input[name=is_weight]:checked').val(),
				isinit :'init'
			};
			
			if(sent_go){
				get_json_combine_correlation(get_array,valInObj,0);	
			}
			
			break;
			case 4:
			
			if( QID_map.length<2 ){
				alert('請選擇兩題以上題目');
				sent_go = false;	
			}
						
			
			var get_array = Array();			
			get_array = $.map(tab_panel.find('input[name=target_group]:checked'),function(a){ 
				obj = {
					input_target: $(a).val(),
					ext_a1:($(a).attr('ext_a1')?$(a).attr('ext_a1'):'')
				}
				return obj; 
			});
			
			
			
			valInObj = {
				variableID :QID_map,
				dotmount :tab_panel.find('select[name=dotmount]').val(),
				ext2 :tab_panel.find('input[name=ext_pg_regression]:checked').val(),
				isinit :'init'
			};
			
			if(sent_go){
				get_json_combine_regression(get_array,valInObj,0);
			}
			
			
			break;
		}
	});
	
	
	$('#btn_prev').click(function(){
		 location.replace('menu.php');
	});

	
	
	
});





function getfile(filename){	
	var form = $('<form method="post" action="getfile_freq.php" target="_blank"><input name="filename" value="'+filename+'" /></form>');
	form.submit();
	discardElement(form);
}
	





</script>
<style>


/*li.pointer { border-top:1px dashed #CCC;padding-left:5px }*/

.page7-box2 { background-color:#fff!important;border: 1px solid #9b9b9b; }
.page7_box { background-color:#fff!important;}

#crosstable_inner_table td { text-align:center }

.page7_box_title { 
	background-image:url(/analysis/images/test1.png);
	background-repeat:repeat-x;
	background-position:0 -10px;
	-moz-border-topright-radius:10px;
	font-weight:bold;
	font-family:微軟正黑體;
	font-size:14px;
}
.page7_box td { 
	font-size:12px;
}


#page7_nav ul li {
	background-color:#fff;
	/*line-height: 32px; 
	border-left: 1px solid #9b9b9b;
	border-right: 1px solid #9b9b9b;*/
}
#page7_nav ul li.ui-selected {
	color:#F00;
	font-weight:bold;
}
#page7_nav ul li {
	color:#444;
}
ul {
	list-style:none;
	padding:0;
}
ul.muit {
	list-style:disc ;
	padding-left:20px;
}

ul li.select_in {
	margin:0;
	padding:0;
}
ul li.selectable:hover {
	cursor:pointer;
}
ul li.ui-selected {
	color:#F00;
	font-weight:bold;
}
button.empty {
	background:url(/analysis/tiped/images/selectable_empty.jpg) no-repeat;
	width:44px;
	height:44px
}
button.in {
	background:url(/analysis/tiped/images/selectable_in.jpg) no-repeat;
	width:44px;
	height:44px
}
td.imagine {
	color:#aaa;
}
tr.imagine td.border {
	border:2px dashed #aaa;
	background-color:#fff;
	padding:1px;
}
tr.imagine.alert td.border {
	border:2px dashed #333;
	background-color:#eee;
	padding:1px;
}
tr td.border {
	border:2px dashed #777;
	background-color:#fff;
	padding:1px;
}
tr td.border_no {
	border:2px dashed #fff;
	background-color:#fff;
	padding:1px;
}
button.combine_variable {
	border:1;
	background:url(/analysis/images/combine.png) no-repeat center 3px;
	width:30px;
	height:40px;
	padding:0;
}
button.combine_variable_cancel {
	border:1;
	background:url(/analysis/images/combine_c.png) #ddd no-repeat center 3px;
	width:30px;
	height:40px;
	padding:0;
}
button.bullet_arrow_up {
	border:1;
	background:url(/analysis/images/bullet_arrow_up.png) no-repeat center;
	width:16px;
	height:16px;
}
button.bullet_arrow_down {
	border:1;
	background:url(/analysis/images/bullet_arrow_down.png) no-repeat center;
	width:16px;
	height:16px;
}
button.out {
	background:url(/analysis/tiped/images/selectable_out.jpg) no-repeat;
	width:44px;
	height:44px
}
input[type=checkbox] {
	width:25px;
	height:15px;
	margin:0
}
input[type=radio] {
	width:25px;
	height:15px;
	margin:0
}

#page7_nav ul > div{
	/*background-image: url(/analysis/images/page-6_btn01s.jpg);
	height: 32px;
	background-repeat: no-repeat;
	*/
}

#page7_nav ul {
	/*margin: 0px;
	padding: 0px;
	list-style-type: none;
	border-bottom: 0px solid #9b9b9b;
	*/
}
#page7_nav {
	width: 280px;
	margin: 0 auto;
}

table.frequence th { border-bottom: 1px solid #000; }
table.frequence td { border-bottom: 1px solid #000; }
table.crosstable th { border-bottom: 1px solid #000; }
table.crosstable td { border-bottom: 1px solid #000; }
table.correlation th { border-bottom: 1px solid #000; }
table.correlation td { border-bottom: 1px solid #000; }
table.regression th { border-bottom: 1px solid #000; }
table.regression td.exist { border-bottom: 1px solid #000; }
.ui-progressbar-value { background:#900 }

#crosstable_preview td.title { background-color:#ddd;font-size:.7em }
#crosstable_preview td.context { border:1px dashed #ddd;text-align:center; }
td.percent { border:0px dashed #000!important; }

.inner_title_top {
	background-image:url(/analysis/images/test1.png);
	background-repeat:repeat-x;
	background-position:0 -10px;
	border:1px solid #ddd;
	color:#000;
	text-align:center;
	line-height:25px;
	border-bottom:0;
	/*margin-top:5px;*/
}
.inner_title_middle {
	border-left: 1px solid #ddd;
	border-right: 1px solid #ddd;
	background-color:#fff;
	padding:3px;
}
.inner_title_bottom {
	border: 1px solid #ddd;
	border-top:0;
	background-color:#fff;
}

.shell_hover:hover {
	border-color:#000!important;
	background-color:#ddd;
}

ul.filetree {
 scrollbar-face-color:#FFF;
 SCROLLBAR-TRACK-COLOR:#FFF;
 SCROLLBAR-ARROW-COLOR:#000;
 SCROLLBAR-HIGHLIGHT-COLOR:#000;
 SCROLLBAR-3DLIGHT-COLOR:#FFF;
 SCROLLBAR-SHADOW-COLOR:#000;
 SCROLLBAR-DARKSHADOW-COLOR:#FFF;
	
}
::-webkit-scrollbar {
	width:5px;
	height:5px;
	background:rgba(255,255,255,0.5);
}
::-webkit-scrollbar-thumb {background:#ccc;-webkit-border-radius:10px;}

div.ui-tabs-panel {
	max-height:500px;
	overflow-y: auto;
}
</style>
</head>

<div class="analysis">

<div class="top_bar"><div id="path"><div id="info"><?//=$login_text2?></div><?//=$login_text1?></div></div>

<div id="page">

<div class="page-top-banner"></div>

<div id="book_bg">
<!--左選單-->
<div id="page_left">

<div id="page7_nav">
<div id="page6-banner"><?//=$dataBaseName?>調查問卷</div>
<!--<div id="title" style="border-bottom:0px">選擇題目<span style="color:#999;font-size:.7em"><img src="/analysis/images/warning.png" />Step 1</span></div>-->

<div style="border:1px solid #cBcBcB;border-radius: 5px;margin-top:2px">
<ul name="select_box" class="filetree tooltip" style="height:580px; overflow:scroll; overflow-x: hidden;">
<?//=$question_new?>
</ul>
</div>

</div>


</div>
<!--左選單-->
<!--右內容-->
<div id="page_right">
<!--<div id="R_top"></div>-->
<!--<div id="R_bg">-->
  


<div id="tabs" style="border: 1px solid #aaa;background-color:#eee">
        <!--<div id="page-6_label"> -->
		<ul style="font-size:1.1em">
        	<li style=""><a href="#tabs-1" style="padding:5px;font-weight:bold;font-family:微軟正黑體">次數分配</a></li>
            <li style=""><a href="#tabs-2" style="padding:5px;font-weight:bold;font-family:微軟正黑體">交叉表</a></li>
            <li style=""><a href="<?//='tb/'.$tb5_inner?>" style="padding:5px;font-weight:bold;font-family:微軟正黑體">平均數比較</a></li> 
            <li style=""><a href="#tabs-3" style="padding:5px;font-weight:bold;font-family:微軟正黑體">相關分析</a></li>
            <li style=""><a href="<?//='tb/'.$tb4_inner?>" style="padding:5px;font-weight:bold;font-family:微軟正黑體">迴歸分析</a></li>
            
            <!--<li style=""><a href="#tabs-6" style="padding:5px;font-weight:bold;font-family:微軟正黑體">分析紀錄</a></li>-->
            <!--<li style=""><a href="#tabs-7" style="padding:5px;font-weight:bold;font-family:微軟正黑體">測試</a></li>-->          
		</ul>
       <!--</div> page-6_label-->
       <div id="page7_main">
		<div id="tabs-1" style="max-height:500px;overflow-y: auto;"><div id="tb1_inner" style="">@include('demo.use.context.tb.tb1_inner_tiped')</div></div>
		<div id="tabs-2" style="max-height:500px;overflow-y: auto;"><div id="tb2_inner" style="">@include('demo.use.context.tb.tb2_inner_tiped')<?// include_once('tb/'.$tb2_inner)?></div></div>
		<div id="tabs-3" style="max-height:500px;overflow-y: auto;"><div id="tb3_inner" style="">@include('demo.use.context.tb.tb3_inner_tiped')<?// include_once('tb/'.$tb3_inner)?></div></div>

		<!--<div id="tabs-6" style="max-height:570px;overflow-y: auto"></div>-->
        <!--<div id="tabs-7" style="max-height:570px;overflow-y: auto"></div>-->
      </div><!--page7_main-->
</div>



<!--</div><!--R_bg-->
<!--<div id="R_bot"></div>-->
<div id="btn_BN">
  	<input type="button" value="上一步" class="btn_box" id="btn_prev" />
	<input type="button"  value="開始分析" class="btn_box" id="btn_next" />
</div>
</div><!--page_right-->

</div>


<div id="footer">
 <div id="footer_text"><?//=$footbar?></div>
</div>

</div>


<div id="dialog-modal" title="分析結果" style="overflow:scroll;overflow-y: scroll;display:none">
	<div>
    	<div id="output_area" style="border: 0px solid #000000;background-color:#FFF;height:700px">
    		<div style="border: 0px solid #000000"></div>
		</div>
    </div>
</div>


</div>

<script>
angular.module('app', [])
.controller('analysisController', analysisController);
function analysisController(){
    
}
</script>