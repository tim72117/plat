<?php
$buildQuestion_editor_ver = app_path().'/views/editor/buildQuestion_editor__v2.0.laravel.php';

$editor_save_structure_ver = 'class/editor_save_structure__v1.9.laravel.php';
$editor_save_analysis_ver = 'class/editor_save_analysis__v1.11.php';
$editor_save_pageskip_ver = 'class/editor_save_pageskip__v1.11.php';

$editor_str_id_save_ver = 'class/editor_str_id_save.php';

$qid = $ques_doc->qid;
$can_edit = $ques_doc->edit ? 'enable' : false;

$page = Input::get('page', Session::get('page', 1));

Session::put('page', $page);

$options_page = DB::table('ques_page')->where('qid', $qid)->orderBy('page')->select('page')->lists('page', 'page');

$page_xml = DB::table('ques_page')->where('qid', $qid)->where('page', $page)->select('xml')->first();
if( isset($page_xml) ){
    $question_array = simplexml_load_string($page_xml->xml);
}else{
    $question_array = array();
}
?>
<head>

<script src="/editor/js/jquery-ui-1.9.2.custom.min.js"></script>
<script src="/editor/js/ckeditor_v4.1/ckeditor.js"></script>

<link rel="stylesheet" href="/editor/css/editor/structure_new.css" />

<script type="text/javascript">

$(document).ready(function() {	
	
	$('#contents').on('focus blur','input[name=qlab],input[name=tablesize]',function(){
		if( event.type=='focus' ){
			$('label[for='+$(this).attr('id')+']').css('display','none');
		}
		if( event.type=='blur' && $(this).val()=='' ){
			$('label[for='+$(this).attr('id')+']').css('display','');
		}
	});	
	
	var valInObj = {
		qn:$('#qn').val(),
		page:$('#page').val(),
		obj:[]
	};	
	
	//---------------------------------------------------------------------------------check
	$('#btn_check').click(function(){
		$('div.question_box.removed').each(function(){
			var targetID = $(this).attr('id');
			alert( targetID );
			var addqid_array = $.grep(valInObj.obj,function(n){ return n.id });
			alert(addqid_array[0]);
			alert( $.inArray(targetID,addqid_array) );
		});		
	});
	//---------------------------------------------------------------------------------重編Table
	$('#btn_creattb').click(function(){	
		$.post('<?=$editor_save_structure_ver?>',function(data){
			alert(data);
			location.reload();			
		}).error(function(e){alert(e);});
	});		
	//---------------------------------------------------------------------------------新增一頁
	$('#btn_cpage').click(function(){
		$.post('add_page',function(data){
			//location.reload();
			console.log(data);
		}).error(function(e){
            console.log(e);
        });
	});	
	//---------------------------------------------------------------------------------重編Id
	$('#btn_resetid').click(function(){
		$.post('<?=$editor_str_id_save_ver?>',function(data){
			location.reload();
			//alert(data);
		});
	});	
	//---------------------------------------------------------------------------------線上分析存檔
	$('#btn_analysis').click(function(){
		analysis_name = $('input[name=analysis_name]').map(function(){ return {type:'name',name:$(this).val(),analysis:$(this).is(':checked')} });
		analysis_item = $('input[name=analysis_item]').map(function(){ return {type:'item',name:$(this).val(),analysis:$(this).is(':checked')} });

		valInObj.obj = analysis_name.get().concat(analysis_item.get());
		valInObj.part = $('input[name=part]').val();
		valInObj.part_name = $('input[name=part_name]').val();
		$.post('<?=$editor_save_analysis_ver?>',valInObj,function(data){
			//location.reload();
			valInObj.obj = [];
			alert(data);
		});
	});
	//---------------------------------------------------------------------------------跳頁存檔
	$('#btn_pageskip').click(function(){
		pageskip = $('input[name=pageskip]').map(function(){ return {type:'pageskip',name:$(this).attr('target'),value:$(this).attr('targetV'),skiptext:$(this).val()} });
		
		valInObj.obj = pageskip.get();
		$.post('<?=$editor_save_pageskip_ver?>',valInObj,function(data){
			//location.reload();
			valInObj.obj = [];
			alert(data);
		});
	});
	//---------------------------------------------------------------------------------存檔
	$('#btn_save').click(function(){

		var checkerror = false;
				
		if ( editor )
			editor.destroy();
		
		//-------------------------------------------------------------------------刪除題目
		$('div.question_box.removed').each(function(){
			var valInObj_one = {
				target: 'deleteq',
				quesArray: Array()
			};
			
			var targetID = $(this).attr('id');	
			
			valInObj_one.quesArray.push({ targetID:targetID });

			$(this).children('div.fieldA').children('div.var_box').find('div.sub').each(function(){
				$(this).children('div.question_box').each(function(){
					var subID = $(this).attr('id');
					valInObj_one.quesArray.push({ targetID:subID });
				});
			});
			
			$(this).remove();			
			
			valInObj.obj.push(valInObj_one);
			//alert('刪除題目');
		});	
		//-------------------------------------------------------------------------修改填答身分
		if( $('div.isShunt').hasClass('changed') ){
			var valInObj_one = {
				target: 'isShunt',
				shunt: $('input[name=isShunt]:checked').map(function(){ return $(this).val(); }).get().join(',')
			};
			//alert(valInObj_one.id);
			valInObj.obj.push(valInObj_one);
			//alert('修改文字欄位_標題');
		}
		//-------------------------------------------------------------------------修改隨機排列
		$('input[name=randomQuesRoot].changed').each(function(){
			var valInObj_one = {
				target: 'randomQuesRoot',
				isChecked: $(this).is(':checked')?'y':'n',
				id: $(this).parent('div.question_box').attr('id')
			};
			//alert(valInObj_one.id);
			valInObj.obj.push(valInObj_one);
			//alert('修改文字欄位_標題');
		});
		//-------------------------------------------------------------------------修改文字欄位_標題
		$('div[target=title].text_changed').each(function(){
			var valInObj_one = {
				target: 'title',
				id: $(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.title_box').parent('div.question_box').attr('id'),				
				title: $(this).html()
			};
			valInObj.obj.push(valInObj_one);
			//alert('修改文字欄位_標題');
		});
		//-------------------------------------------------------------------------修改文字欄位_大標題
		$('div[target=explain].text_changed').each(function(){
			var valInObj_one = {
				target: 'explain',
				id: $(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.explain_box').attr('index'),				
				title: $(this).html()
			};
			alert(valInObj_one.id);
			//valInObj.obj.push(valInObj_one);
			//alert('修改文字欄位_標題');
		});
		//-------------------------------------------------------------------------修改文字欄位_選項		
		$('div.fieldA:not(.changed) > div.var_box> table div[target=item].text_changed').each(function(){
			var target = $(this).parent('td').parent('tr').parent('tbody').parent('table');
			var onvalue = target.children('tbody').children('tr:eq(0)').find('input').attr('index');
			var sub_title = target.parent('div.var_box').is('.text')?target.children('tbody').children('tr:eq(0)').find('div[target=item_sub]').text():'';
			
			var valInObj_one = {
				target: 'item',
				id: target.parent('div.var_box').parent('div.fieldA').parent('div.question_box').attr('id'),				
				title: $(this).html(),
				sub_title: sub_title,
				value: onvalue
			};
			//alert( valInObj_one.value );
			valInObj.obj.push(valInObj_one);
			//alert('修改文字欄位_選項');
		});
		//-------------------------------------------------------------------------修改文字欄位_量表選項
		$('div.fieldA:not(.changed) > div.var_scale_box div[target=degree].text_changed').each(function(){

			var target = $(this).parent('td').parent('tr').parent('tbody').parent('table');
			onvalue = target.find('input').is('[index]')?target.find('input').attr('index'):target.find('input').val();
			var valInObj_one = {
				target: 'degree',
				id: target.parent('div.var_scale_box').parent('div.fieldA').parent('div.question_box').attr('id'),				
				title: $(this).html(),
				value: onvalue
			};
			//alert(valInObj_one.value);
			valInObj.obj.push(valInObj_one);
			//alert('修改文字欄位_量表選項');
		});
		//-------------------------------------------------------------------------修改題目類型
		$('div.qtype_box.changed').each(function(){
			if( $(this).children('table').find('tbody > tr > td > select[name=qtype]').val()=='text' ){
				var tablesize = $(this).children('table').find('tbody > tr > td input[name=tablesize]').val();
			}else{
				var tablesize = 0;
			}

			var valInObj_one = {
				target: 'type',
				id: $(this).parent('div.question_box').attr('id'),
				qtype: $(this).children('table').find('tbody > tr > td > select[name=qtype]').val(),
				qlab: $(this).parent('div').children('div.title_box').children('table').find('tbody > tr > td > input[name=qlab]').val(),
				tablesize: tablesize
			};
			//alert(valInObj_one.id+' '+valInObj_one.qtype+' '+valInObj_one.qlab);		
			valInObj.obj.push(valInObj_one);
			//alert('修改題目類型');
		});
		//-------------------------------------------------------------------------修改選項
		$('div.fieldA.changed').each(function(){
			var qid = $(this).parent('div.question_box').attr('id');
			var qtype = $(this).parent('div.question_box').children('div.qtype_box').find('select[name=qtype]').val();
			var code = $(this).parent('div.question_box').find('div.initv_box').find('select[name=code]').val();
			var auto_hide = $(this).attr('auto_hide');

			var itemArray = [];			
			
			$(this).children('div.var_box').each(function(){
				var subid_array = [];
				var skipArray = [];
				var othervArray = [];
				
				subid_array = $.map($(this).children('div.sub').children('div.question_box'),function(n){
					return $(n).attr('id');
				});
				
				skipArray = $.map($(this).children('table').find('span.skipq_lab'),function(n){
					return $(n).attr('target');
				});
				
				if( $(this).is(':has(td[name=otherv])') ){					
					othervArray = $.map($(this).find('td[name=otherv]').children('input'),function(n){
						return {name:$(n).attr('name'),value:$(n).val()};
					});	
				}
			
				var onvalue = $(this).children('table').find('input').val();
				var sub_title = qtype=='text'?$(this).children('table').children('tbody').children('tr:eq(0)').find('div[target=item_sub]').text():'';
				var ccheckbox = $(this).children('table').find('span.ccheckbox').hasClass('enable');


				if( qtype=='text' || qtype=='textarea' ){
					
					var size = $(this).children('table').find('input[name=tablesize]').val();
				}else{
					var size = 0;
				}
				
				
				
				
				if( qtype!='scale' && qtype!='select' )
				itemArray.push({
					value: onvalue,
					subid_array: subid_array,
					skipArray: skipArray,
					othervArray: othervArray,
					ccheckbox: ccheckbox,
					size: size,
					title: $(this).find('div.editor').html(),
					sub_title: sub_title,
					ruletip: ''//$(this).find('div.ruletip').html()
				});		

				if( qtype=='scale' )
				itemArray.push( { value:onvalue ,title:$(this).find('div.editor').html() } );	
				
				if( qtype=='select' )
				itemArray.push( { value:onvalue ,subid_array:subid_array ,skipArray:skipArray ,othervArray:othervArray ,title:$(this).find('div.editor').html() } );
				
				
			});	
			if( qtype!='explain' && qtype!='textarea' )
			if( itemArray.length==0 ){
				alert('沒有加入選項');
				checkerror = true;
			}
			if( qtype=='init' ){
				alert('沒有選擇題目類型');
				checkerror = true;
			}
						
			var degreeArray = [];
			
			if( qtype=='scale' )
			$(this).children('div.var_scale_box').each(function(){
				var onvalue = $(this).children('table').find('input').is('[index]')?$(this).children('table').find('input').attr('index'):$(this).children('table').find('input').val();
				degreeArray.push( { value:onvalue, title:$(this).find('div.editor').html(), ruletip:$(this).find('div.ruletip').html() } );
			});	
			
			var textarea_inf = {};
			if( qtype=='textarea' ){
				itemArray.push( { 
					value:1 ,
					size:$(this).children('div.var_textarea_box').find('input[name=tablesize]').val(),
					height:$(this).children('div.var_textarea_box').find('input[name=tableheight]').val(),
					width:$(this).children('div.var_textarea_box').find('input[name=tablewidth]').val()
				} );				

				if( $(this).children('div.var_textarea_box').find('input[name=tablesize]').val()=='' ){
					alert('請輸入字數');
					checkerror = true;
				}
				if( $(this).children('div.var_textarea_box').find('input[name=tableheight]').val()=='' ){
					alert('請輸入欄高');
					checkerror = true;
				}
				if( $(this).children('div.var_textarea_box').find('input[name=tablewidth]').val()=='' ){
					alert('請輸入欄寬');
					checkerror = true;
				}
			}
			
			var valInObj_one = {
				target: 'item_array',
				id: qid,
				qtype: qtype,
				code: code,
				auto_hide: auto_hide,
				itemArray: itemArray,
				degreeArray: degreeArray
			};				

			valInObj.obj.push(valInObj_one);
			//alert('修改選項');
		});
		//-------------------------------------------------------------------------開始儲存
		if( valInObj.obj.length>0 && !checkerror )
		$.post('save_data', valInObj, function(data){
			alert(data);
			for(var i in valInObj.obj){
				//$('div[qid='+valInObj.obj[i].id+']').attr('text_changed','false');
			}
			$('div.fieldA').removeClass('changed');
			$('div.editor').removeClass('text_changed');
			$('div.qtype_box').removeClass('changed');
			$('input[name=randomQuesRoot].changed').removeClass('changed');
						
			valInObj.obj = [];
			alert('儲存成功');
			
            location.reload();
			
			
		}).error(function(e){
            console.log(e);
        });
		
	});
	
	
	
	//---------------------------------------------------------------------------------事件觸發_修改填寫身分
	$("div.isShunt").on("click", "input[name=isShunt]", function() {
		$(this).parent('div').addClass('changed');
	});	
	//---------------------------------------------------------------------------------事件觸發_修改題號
	$("#contents").on("change", "input[name=qlab]", function() {
		$(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.title_box').parent('div').children('div.qtype_box').addClass('changed');
		//$(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.qtype_box').addClass('changed');
	});	
	//---------------------------------------------------------------------------------事件觸發_隨機排列
	$("#contents").on("click", "input[name=randomQuesRoot]", function() {
		$(this).addClass('changed');
	});	
	//---------------------------------------------------------------------------------事件觸發_修改題目類型
	$("#contents").on("change", "select[name=qtype]", function() {
		var target = $(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.qtype_box');
		
		
		var qtype = target.find('select[name=qtype]').val();
		var qtype_org = target.find('select[name=qtype]').attr('qtype_org');
		
		
		
		var select_bkcolor_array = {};
		select_bkcolor_array.checkbox = '#b3ffb3';
		if( typeof(select_bkcolor_array[qtype])!='undefined' ){
			select_bkcolor = select_bkcolor_array[qtype];
			target.find('select[name=qtype]').css('backgroundColor',select_bkcolor);
		}else{
			target.find('select[name=qtype]').css('backgroundColor','');
		}
		
		if( qtype=='init' )
			return false;
		
		if( qtype=='checkbox' || qtype=='text' || qtype=='scale' || qtype=='list' ){
			//$(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.qtype_box').parent('div.question_box').children('div.fieldA').children('div.initv_box').find('select[name=code]').trigger('change');
			target.siblings('div.fieldA').children('div.initv_box').find('select[name=code]').val('auto').prop('disabled',true);
		}else{
			target.siblings('div.fieldA').children('div.initv_box').find('select[name=code]').prop('disabled',false);
			//$(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.qtype_box').parent('div.question_box').children('div.fieldA').children('div.initv_box').find('select[name=code]').trigger('change');
		}
		
		
		if( qtype_org!=qtype )
		if( (qtype_org!='select' && qtype_org!='radio') || (qtype!='select' && qtype!='radio') )
			target.siblings('div.fieldA').children('div.var_box,div.var_scale_box,div.var_scale_box_init').remove();
		if( qtype!='textarea' )
			target.siblings('div.fieldA').children('div.var_textarea_box').remove();
			
		if( qtype=='scale' )
		target.siblings('div.fieldA').children('div.initv_box').after(
			'<div class="var_scale_box_init" style="margin-right:0px;border:0px dashed #A0A0A4">'+
			'<table class="nb-tab"><tr>'+
			'<td><div class="title" style=";border-top:1px dashed #aaa;background-color:#D7E6FC"></div></td>'+
			'<td width="16px"></td>'+
			'<td width="16px"><span class="adddegree" anchor="var" addlayer="" title="加入選項" /></td>'+
			'<td width="16px"></td>'+
			'<td width="1px"><div style="font-size:10px;color:red;background-color:#D4BFFF;width:170px;position:absolute;margin-left:20px"></div></td>'+
			'</tr></table>'+
			'</div>'
		);
		
		if( qtype=='textarea' )
		target.siblings('div.fieldA').append(
			'<div class="var_textarea_box" style="margin-right:0px;border:0px dashed #A0A0A4">'+
			'<table style="width:100%" cellpadding="1" cellspacing="1"><tr>'+
			'<td width="1px"><input name="v_value" type="hidden" size="1" disabled="disabled" value="1" index="1" /></td>'+
			'<td width="86px"><span style="font-size:13px">字數</span><input name="tablesize" type="text" size="2" value="500" /></td>'+
			'<td width="86px"><span style="font-size:13px">高</span><input name="tableheight" type="text" size="2" value="20" /></td>'+
			'<td width="86px"><span style="font-size:13px">寬</span><input name="tablewidth" type="text" size="2" value="89" /></td>'+
			'<td><div class="" target="" style="border:0px solid #A0A0A4;min-height:22px"></td>'+
			'</tr></table>'+
			'</div>'
		);
		
		target.find('select[name=qtype]').attr('qtype_org',qtype);	
		target.addClass('changed');	
		target.siblings('div.fieldA').addClass('changed');

	});	
	//---------------------------------------------------------------------------------事件觸發_自動編碼
	$("#contents").on("change", "select[name=code]", function() {
		var target = $(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.initv_box').parent('div.fieldA').children('div.var_box');
		var qtype = $(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.initv_box').parent('div.fieldA').parent('div.question_box').children('div.qtype_box').find('select[name=qtype]').val();
		var code = $(this).val();
		
		recodeValue(target,qtype,code);
		
		if( qtype=='scale' )
		recodeValue($(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.initv_box').parent('div.fieldA').children('div.var_scale_box'),qtype,code);
		
		$(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.initv_box').parent('div.fieldA').addClass('changed');
	});	
	//---------------------------------------------------------------------------------事件觸發_文字欄位大小
	$("#contents").on("change", "input[name=tablesize]", function() {
		$(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.var_box,div.var_textarea_box').parent('div.fieldA').addClass('changed');
	});	
	$("#contents").on("change", "input[name=tableheight]", function() {
		$(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.var_box,div.var_textarea_box').parent('div.fieldA').addClass('changed');
	});	
	$("#contents").on("change", "input[name=tablewidth]", function() {
		$(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.var_box,div.var_textarea_box').parent('div.fieldA').addClass('changed');
	});	
	//---------------------------------------------------------------------------------
	$("#contents").on("click", "div.main img.toggle_hide", function() {
		$(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.initv_box').css('backgroundColor','#88cc88');		
		if( $(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.initv_box').parent('div.fieldA').children('div.var_box').length>200 ){
			$(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.initv_box').parent('div.fieldA').children('div.var_box').hide();
		}else{
			$(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.initv_box').parent('div.fieldA').children('div.var_box').slideUp('fast');
		}
		$(this).attr('title','展開選項').attr('alt','展開選項').removeClass('toggle_hide').addClass('toggle_show');
		$(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.initv_box').parent('div.fieldA').attr('auto_hide','true');
		$(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.initv_box').parent('div.fieldA').addClass('changed');
	});
	$("#contents").on("click", "div.main img.toggle_show", function() {
		$(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.initv_box').css('backgroundColor','');
		if( $(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.initv_box').parent('div.fieldA').children('div.var_box').length>200 ){
			$(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.initv_box').parent('div.fieldA').children('div.var_box').show();
		}else{
			$(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.initv_box').parent('div.fieldA').children('div.var_box').slideDown('fast');
		}
		$(this).attr('title','隱藏選項').attr('alt','隱藏選項').removeClass('toggle_show').addClass('toggle_hide');
		$(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.initv_box').parent('div.fieldA').attr('auto_hide','false');
		$(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.initv_box').parent('div.fieldA').addClass('changed');
	});
	//---------------------------------------------------------------------------------事件觸發_開始編輯文字
	$("#contents").on("click", "div.main img.edittext", function() {
		if ( !editor ){
			var target = $(this).parent('td').prev('td').find('div.editor');
			createEditor(target.get(0));
		}else{
			alert('其他題目編輯中');
		}
	});	
	//---------------------------------------------------------------------------------事件觸發_結束編輯文字
	$("#contents").on("click", "div.main img.editfinish", function() {
		editor.destroy();		
	});	
	//---------------------------------------------------------------------------------事件觸發_選取跳答題目
	$("#contents").on("click", "div.main span.skipq", function() {
	

		$(this).attr('title','完成').removeClass('skipq').addClass('skipq_block');
		
		$('#contents').on("mouseover", "div.question_box[layer=0] div.qtype_box", function(){
			
			$(this).animate({ backgroundColor: "#ff0" }, 100);
			
		}).on("mouseout", "div.question_box[layer=0] div.qtype_box", function() {
			
			$(this).animate({ backgroundColor: "#58b5e1" }, 10);
			
		}).on("click", "div.question_box[layer=0] div.qtype_box", function() {
			
			if( $('span.skipq_block').parent('td').parent('tr').is(':only-child') )
			$(
				'<tr>'+
				'<td width="30px"></td>'+
				'<td><div class="skipbox" target="item" style="border:1px dashed #A0A0A4;background-color:#aaeeaa"></div></td>'+
				'</tr>'
			).insertAfter( $('span.skipq_block').parent('td').parent('tr') );
			
			var targetId = $(this).parent('div.question_box').attr('id');

			if( $('span.skipq_block').parent('td').parent('tr').next('tr').find('div.skipbox').is(':not(:has(img[target='+targetId+']))') )
				$('span.skipq_block').parent('td').parent('tr').next('tr').find('div.skipbox').append('<span class="skipq_lab" style="margin-left:2px;" title="" target="'+targetId+'" />');		
			
			$('span.skipq_block').parent('td').parent('tr').parent('tbody').parent('table').parent('div.var_box').parent('div.fieldA').addClass('changed');
			
			
		});
	
	});	
	$("#contents").on("click", "div.main span.skipq_block", function() {
		$('span.skipq_block').attr('title','設定跳題').removeClass('skipq_block').addClass('skipq');
		
		$('#contents').off("mouseover","div.question_box[layer=0] div.qtype_box");
		$('#contents').off("mouseout","div.question_box[layer=0] div.qtype_box");
		$('#contents').off("click","div.question_box[layer=0] div.qtype_box");
	});
	//---------------------------------------------------------------------------------事件觸發_讀取跳答標題
	$("#contents").on("mouseover", "span.skipq_lab", function() {
		var targetId = $(this).attr('target');		
		var targetTitle = $('#'+targetId).children('div.title_box').find('div.editor').text();
		var targetqlab = $('#'+targetId).children('div.title_box').find('input[name=qlab]').val();
		
		if( $('#'+targetId).length==0 ){
			$(this).attr('title','跳題設定錯誤').attr('alt','跳題設定錯誤');
		}else{
			$(this).attr('title','跳題 - ( '+targetqlab+' '+targetTitle+' )').attr('alt','跳題 - ( '+targetqlab+' '+targetTitle+' )');
		}
		
	//---------------------------------------------------------------------------------事件觸發_取消跳答	
	}).on("click", "span.skipq_lab", function() {
		
		$(this).parent('div').parent('td').parent('tr').parent('tbody').parent('table').parent('div.var_box').parent('div.fieldA').addClass('changed');
		
		if( $(this).is(':only-child') ){
			$(this).parent('div.skipbox').parent('td').parent('tr').remove();
		}else{
			$(this).remove();
		}
	});	
	//---------------------------------------------------------------------------------事件觸發_清除勾選項目checkbox
	$("#contents").on("click", "span.ccheckbox", function(e) {
		$(this).toggleClass('enable');
		$(this).parent('td').parent('tr').parent('tbody').parent('table').parent('.var_box').parent('.fieldA').addClass('changed');
	});
	//---------------------------------------------------------------------------------事件觸發_刪除題目
	$("#contents").on("click", "div.question_box span.deletequestion", function() {
		
		if ( editor )
			editor.destroy();
		
		var target = $(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.qtype_box').parent('div.question_box');
		target.addClass('removed');
		
		if( target.attr('layer')!=0 ){
			target.parent('div.sub').parent('div.var_box').parent('div.fieldA').addClass('changed');
		}
		
		target.slideUp('fast',function(){
		});
		target.next('div.addq_box').remove();
	});
	//---------------------------------------------------------------------------------事件觸發_增加量表選項
	$("#contents").on("click", "div.main span.adddegree", function() {
		var target = $(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div');
		var addlayer = Number($(this).attr('addlayer'));
		var qtype = target.parent('div.fieldA').parent('div.question_box').children('div.qtype_box').find('select[name=qtype]').val();
		
		target.after(
			'<div class="var_scale_box">'+
			
			'<table class="nb-tab"><tr>'+
			
			'<td width="30px"><input name="v_value" type="text" size="1" disabled="disabled" value="" /></td>'+
			
			'<td><div class="editor item text_changed" target="degree" contenteditable="true" style="border:1px solid #A0A0A4;min-height:22px"></div></td>'+
						
			//'<td width="16px"><img class="edittext" anchor="var" src="images/edit.png" title="修改文字" alt="修改文字" /></td>'+
			'<td width="16px"><span class="adddegree" anchor="var" addlayer="'+addlayer+'" title="加入量表選項" /></td>'+
			'<td width="16px"><span class="deletevar scale" title="刪除量表選項" /></td>'+
			//'<td width="16px"></td>'+
			//'<td width="1px"><div class="ruletip" style="font-size:10px;color:red;background-color:#D4BFFF;width:170px;position:absolute;margin-left:20px"></div></td>'+
			'<td width="1px"></td>'+
			'</tr></table>'+
			'</div>'
		);				
		
		var code = target.parent('div.fieldA').children('div.initv_box').find('select[name=code]').val();
		recodeValue(target.parent('div').children('div.var_scale_box'),qtype,code);		
		
		target.parent('div.fieldA').addClass('changed');
	});
	//---------------------------------------------------------------------------------事件觸發_增加選項
	$("#contents").on("click", "div.main span.addvar", function() {
		var target = $(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div');
		var addlayer = Number($(this).attr('addlayer'));
		var qtype = target.parent('div.fieldA').parent('div.question_box').children('div.qtype_box').find('select[name=qtype]').val();
		var amountV = target.parent('div.fieldA').children('div.var_box').length;	

		var edittext = qtype=='scale'?'量表子題':'選項';
		
		target.after(
			'<div class="var_box">'+
			
			'<table class="nb-tab"><tr>'+			
			(( qtype=='radio' || qtype=='select' || qtype=='checkbox' )?'<td width="30px"><input name="v_value" type="text" size="1" disabled="disabled" value="" /></td>':'')+
			(( qtype=='text' || qtype=='textarea' || qtype=='scale' || qtype=='list' )?'<td style="display:none"><input name="v_value" type="hidden" size="1" disabled="disabled" value="" /></td>':'')+
			
			(( qtype=='text' )?'<td width="1px"><input name="tablesize" type="text" size="2" value="" /></td>':'')+
			
			
			'<td><div class="editor item text_changed" target="item" contenteditable="true" style="border:1px solid #A0A0A4;min-height:22px"></div></td>'+
					
			//'<td width="16px"><img class="edittext" anchor="var" src="images/edit.png" title="修改文字" alt="修改文字" /></td>'+
			
			(( qtype=='radio' || qtype=='select' )?'<td width="16px"><span class="skipq" title="設定跳題" /></td>':'')+
			
			(( qtype=='text' )?'<td width="300px"><div class="editor item text_changed" target="item_sub" contenteditable="true" style="border:1px solid #A0A0A4"></div></td>':'')+
			
			'<td width="16px"><span class="addvar" anchor="var" addlayer="'+addlayer+'" title="加入'+edittext+'" /></td>'+
			'<td width="16px"><span class="deletevar" title="刪除'+edittext+'" /></td>'+
			(( qtype=='radio' || qtype=='select' || qtype=='checkbox' || qtype=='list' )
				?'<td width="16px"><span class="addquestion" anchor="var" addlayer="'+(addlayer+1)+'" title="加入題目" /></td>'
				:''
			)+
			
			'<td width="1px"></td>'+
			//'<td width="1px"><div class="ruletip" style="font-size:10px;color:red;background-color:#D4BFFF;width:170px;position:absolute;margin-left:20px"></div></td>'+
			'</tr></table>'+
			'</div>'
		);				
		
		var code = target.parent('div.fieldA').children('div.initv_box').find('select[name=code]').val();
		recodeValue(target.parent('div').children('div.var_box'),qtype,code);
		recodeIndex(target.parent('div').children('div.var_box'));
		
		target.parent('div.fieldA').addClass('changed');
	});
	//---------------------------------------------------------------------------------事件觸發_刪除選項
	$("#contents").on("click", "div.main span.deletevar", function() {

		var target = $(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div');
		var target_parent = target.parent('div');
		var qtype = target.parent('div.fieldA').parent('div.question_box').children('div.qtype_box').find('select[name=qtype]').val();
				
		target.parent('div.fieldA').addClass('changed');

		if(target.next().is('.question_box'))
		target.next().remove();
		
		//if(target_parent.children('div.var_box').length>1){		
		target.remove();	

		var code = target_parent.children('div.initv_box').find('select[name=code]').val();			
		recodeValue(target_parent.children('div.var_box'),qtype,code);
		recodeIndex(target_parent.children('div.var_box'));
		//}
	
	});
	//---------------------------------------------------------------------------------事件觸發_匯入選項
	$("#contents").on("click", "div.main span.addvar_list", function() {
		var target = $(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.initv_box');
		var addvarlist_box = $(
			'<div class="addvarlist_box" style="margin-right:0px;border:0px dashed #A0A0A4">'+		
		   	'<table style="width:100%">'+
			'<tr><td>'+
			'<span style="color:red">請貼入表格，格式如下</span>'+
				'<table cellspacing="3" cellpadding="3">'+
				'<tr><td style="">第1行--</td><td style="border:1px dashed #A0A0A4">value</td><td style="border:1px dashed #A0A0A4">text</td></tr>'+
				'<tr><td style="">第2行--</td><td style="border:1px dashed #A0A0A4">值(匯入的欄位)</td><td style="border:1px dashed #A0A0A4">文字(匯入的欄位)</td></tr>'+
				'<tr><td style="">第3行--</td><td style="border:1px dashed #A0A0A4">值(匯入的欄位)</td><td style="border:1px dashed #A0A0A4">文字(匯入的欄位)</td></tr>'+
				'</table>'+
			'</td></tr>'+
			'<tr>'+	
			'<td><textarea rows="5" cols="60" id="packagein"></textarea></td>'+
			
			//'<td width="16px"><img class="data_chooser" anchor="var" src="images/data_chooser.png" title="修改文字" alt="修改文字" /></td>'+
			'<td width="1px"><div class="ruletip" style="font-size:10px;color:red;background-color:#D4BFFF;width:170px;position:absolute;margin-left:20px"></div></td>'+	
			'</tr>'+
			'</table>'+
			'</div>'
		).insertAfter(target);
		
		$(this).attr('title','匯入選項').attr('alt','匯入選項').removeClass('addvar_list').addClass('addvar_list_finish');

	});
	//---------------------------------------------------------------------------------事件觸發_結束匯入選項
	$("#contents").on("click", "div.main span.addvar_list_finish", function() {
		//editor.destroy();
		var btn = $('img.addvar_list_finish');
		btn.attr('src','images/data_chooser.png').attr('title','完成').attr('alt','完成').removeClass('addvar_list_finish').addClass('addvar_list');
		target = $('#packagein');
		var listString = $('#packagein').val();
		var list = listString.split('\n');
		
		var parent_fieldA = target.parent('td').parent('tr').parent('tbody').parent('table').parent('div.addvarlist_box').parent('div.fieldA');
		target.parent('td').parent('tr').parent('tbody').parent('table').parent('div.addvarlist_box').remove();
		
		
		for(var i=0;i<list.length;i++){
			//console.log(list[i].split(' '));
		}
		

		
		
		if( list.length>0 ){
			
			//var listhead = list[0].split(' ');
			
			var addlayer = Number(btn.attr('addlayer'));
			var qtype = parent_fieldA.parent('div.question_box').children('div.qtype_box').find('select[name=qtype]').val();

			var edittext = qtype=='scale'?'量表子題':'選項';
			var newv_string = '';
			
			for(var i=0;i<list.length;i++){				
				
				var itemn = list[i].split('	');
				var value = itemn[0];
				var text = itemn[1];
				
				console.log(value);
				var other = '';
				//if( $(this).children('td:gt(1)').length>0 ){
					//$(this).children('td:gt(1)').each(function(index){
						//other += '<input name="'+listhead.children('td:gt(1)').eq(index).text().trim_hl()+'" type="hidden" size="1" disabled="disabled" value="'+$(this).text().trim_hl()+'" />';
					//});
					//alert(other);
				//}
				newv_string +=
					'<div class="var_box">'+
					'<table class="nb-tab"><tr>'+
					(( qtype=='radio' || qtype=='select' || qtype=='checkbox' )?'<td width="30px"><input name="v_value" type="text" size="1" disabled="disabled" value="'+value+'" /></td>':'')+
					(( other!='' )?'<td width="1px" name="otherv">'+other+'</td>':'')+
					(( qtype=='text' || qtype=='scale' )?'<td width="1px"><input name="v_value" type="hidden" size="1" disabled="disabled" value="" /></td>':'')+
					'<td><div class="editor title" style=";border:1px dashed #A0A0A4;background-color:#D7E6FC;min-height:22px">'+text+'</div></td>'+
								
					//'<td width="16px"><img class="edittext" anchor="var" src="images/edit.png" title="修改文字" alt="修改文字" /></td>'+
					
					(( qtype=='radio' || qtype=='select' )?'<td width="16px"><span class="skipq" title="設定跳題" /></td>':'')+
					
					'<td width="16px"><span class="addvar" anchor="var" addlayer="'+addlayer+'" title="加入'+edittext+'" /></td>'+
					'<td width="16px"><span class="deletevar" title="刪除'+edittext+'" /></td>'+
					(( qtype=='radio' || qtype=='select' || qtype=='checkbox' )
						?'<td width="16px"><span class="addquestion" anchor="var" addlayer="'+(addlayer+1)+'" title="加入題目" /></td>'
						:'<td width="16px"></td>'
					)+
					//'<td width="1px"><div class="ruletip" style="font-size:10px;color:red;background-color:#D4BFFF;width:170px;position:absolute;margin-left:20px"></div></td>'+
					'</tr></table>'+
					'</div>'
			};
			parent_fieldA.append(newv_string);	
			parent_fieldA.children('div.initv_box').find('select[name=code]').val('manual');
		}
		
		
		var target = $(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div');
		target.parent('div.fieldA').addClass('changed');	
	});	
	$("#contents").on("click", "div.main img.rulldelete", function() {
		$(this).parent('td').parent('tr').parent('tbody').parent('table').remove();
	});	
	
	$("#contents").on("click", "div.main img.data_edit", function() {
		$(this).attr('src','images/q_data_empty.png').attr('title','加入跳答欄位').attr('alt','加入跳答欄位').removeClass('data_edit').addClass('data_add');
		$('img.data_add').parent('td').next('td.rull_data_text').text('');
		$('img.data_add').parent('td').next('td.rull_data_text').next('td').find('select[name=value] option').remove();
		 
		$('#database').on("click", "img.addtable", function(){
			$('img.data_add').parent('td').next('td.rull_data_text').text($(this).attr('title'));
			var option = '';
			$.map(database[$(this).attr('qid')],function(n){ option += ('<option value="'+n.value+'">'+n.ans+'</option>'); });
			$('img.data_add').parent('td').next('td.rull_data_text').next('td').find('select[name=value]').append(option);
			$('img.data_add').attr('src','images/q_data_edit.png').attr('title','修改跳答欄位').attr('alt','修改跳答欄位').removeClass('data_add').addClass('data_edit');
		});
	});	
	//---------------------------------------------------------------------------------事件觸發_增加題目
	$("#contents").on("click", "div.main span.addquestion", function() {	
		
		var qanchor =  $(this).attr('anchor');		
		var addlayer = $(this).attr('addlayer');		
		var newid = 'QID_'+getPassword(8);
		var qlab_length = newid.length;
		var question_box_style = 'display:none';

		if( addlayer==0 ){
			var target = $(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.addq_box').parent('div.main');
			var newq = $('<div class="question_box new" id="'+newid+'" uqid="'+newid+'" parrent="" layer="'+addlayer+'" style="'+question_box_style+'">').appendTo( $('<div class="main"></div>').insertAfter( target ) );
		}
		if( addlayer!=0 ){
			var target = $(this).parent('td').parent('tr').parent('tbody').parent('table');
			if( target.parent().is('.addq_box') )
			var newq = $('<div class="question_box new" id="'+newid+'" uqid="'+newid+'" parrent="" layer="'+addlayer+'" style="'+question_box_style+';border-right:0;margin-left:45px">').insertAfter( target.parent('div.addq_box') );
			
			if( target.parent().is('.var_box') )
			if( target.next('div').is('.sub') ){
				var newq = $('<div class="question_box new" id="'+newid+'" uqid="'+newid+'" parrent="" layer="'+addlayer+'" style="'+question_box_style+';border-right:0;margin-left:45px">').prependTo( target.next('div.sub') );
			}else{
				var sub_box = $('<div class="sub"></div>').insertAfter( target );
				var newq = $('<div class="question_box new" id="'+newid+'" uqid="'+newid+'" parrent="" layer="'+addlayer+'" style="'+question_box_style+';border-right:0;margin-left:45px">').prependTo( sub_box );
			}
		}
			
			
			<? if( isset($_SESSION['randomQuesRoot']) && $_SESSION['randomQuesRoot']==1 ){ ?>
			var randomQuesRoot = $('<input name="randomQuesRoot" id="randomQuesRoot'+newid+'" type="checkbox" class="changed" />'+
												 '<label for="randomQuesRoot'+newid+'" style="cursor: pointer;font-size:13px">隨機排列</label>').appendTo(newq);
			<? } ?>
						
			var type_box = $(
				'<div class="qtype_box changed" style="padding-right:0px;background-color:#58b5e1">'+
					(addlayer!=0?'<div style="position:absolute;margin-left:-30px"><img src="images/link.png" alt="" /></div>':'')+
					'<span style="font-size:10px;background-color:#D4BFFF;width:170px;position:absolute;margin-left:-'+(180+addlayer*46)+'px">Table:;QID:'+newid+'</span>'+
					'<table style="width:100%"><tr>'+
					'<td>'+
					'<select name="qtype">'+
						//'<option value="init">請選擇題型..</option>'+
						'<option value="select">單選題(下拉式)</option>'+
						'<option value="radio">單選題(點選)</option>'+
						'<option value="checkbox">複選題</option>'+
						'<option value="text">文字欄位</option>'+
						'<option value="textarea">文字欄位(大型欄位)</option>'+
						'<option value="scale">量表</option>'+
						'<option value="list">題組</option>'+
						'<option value="explain">文字標題</option>'+				
					'</select>'+
					
					'<span></span>'+
					'</td>'+
					'<td width="16px"><span class="deletequestion" alt="刪除題目" /></td>'+
					'<td width="1px"><div style="font-size:10px;color:red;background-color:#D4BFFF;width:170px;position:absolute;margin-left:20px"></div></td>'+
					'</tr>'+				
					'</table>'+
				'</div>'	+
				
				'<div class="title_box" style="background-color:#fff">'+					
					'<table style="width:100%"><tr>'+
					'<td width="65px" valign="top"><input name="qlab" type="text" size="4" value="" /></td>'+
					'<td><div id="title_'+newid+'" class="editor title" contenteditable="true" target="title" style="padding:5px;border:1px dashed #A0A0A4">請修改文字</div></td>'+
	 				//'<td width="16px"><img class="edittext" anchor="ques" src="images/edit.png" title="修改文字" alt="修改文字" /></td>'+
					'<td width="1px"><div style="font-size:10px;color:red;background-color:#D4BFFF;width:170px;position:absolute;margin-left:20px"></div></td>'+
					'</tr></table>'+
				'</div>'
			).appendTo(newq);
			
			var var_box = $(			
				'<div class="fieldA changed" init="" style="background-color:#b3e373">'+
					'<div class="initv_box" style="margin-right:0px;border:0px dashed #A0A0A4">'+
					'<table class="nb-tab"><tr>'+
					'<td width="16px"><select name="code">'+
						'<option value="auto">自動編碼</option>'+
						'<option value="manual">手動編碼</option>'+
					'</select></td>'+
					'<td><div class="editor title" style=";border:0px dashed #A0A0A4;background-color:#D7E6FC"></div></td>'+
							
					'<td width="16px"><span class="addvar" anchor="var" addlayer="'+addlayer+'" title="加入選項" /></td>'+
					'<td width="16px"><span class="addvar_list" anchor="var" addlayer="1" title="匯入選項" /></td>'+
					'<td width="16px"><span class="deletevar_list" anchor="var" addlayer="1" title="刪除全部選項" /></td>'+
					'<td width="1px"></td>'+
					'</tr></table>'+
					'</div>'+
				'</div>'+
				'<p class="contribute"></p>'
			).appendTo(newq);
			
			var addq_box = $(
				'<div class="addq_box '+(addlayer==0?'root':'sub')+'" append="false" align="center" style="">'+
					'<table style="width:100%"><tr>'+
					'<td><div></div></td>'+
					'<td width="16px"><span class="addquestion" anchor="'+qanchor+'" addlayer="'+addlayer+'" title="加入題目" /></td>'+
					'<td width="1px"><div style="font-size:10px;color:red;background-color:#D4BFFF;width:170px;position:absolute;margin-left:20px"></div></td>'+
					'</tr></table>'+
				'</div>'
			).insertAfter(newq);
				
			newq.slideDown('fast');			

			
			if(addlayer==0){
				var qanchor = newq.parent('div.main').prev('div.main').children('div.question_box').attr('id');			
			}
			if(addlayer!=0){
				var qanchor = newq.parent('div.sub').parent('div.var_box').parent('div.fieldA').parent('div.question_box').attr('id');
				newq.parent('div.sub').parent('div.var_box').parent('div.fieldA').addClass('changed');
			}
			//alert(qanchor+' '+qanchor_onvalue);		
						
			var valInObj_one = {
				target: 'newq',
				id: newid,
				layer: addlayer,
				qanchor: qanchor
			};
			valInObj.obj.push(valInObj_one);	
			//alert('增加題目');
			CKEDITOR.inline( document.getElementById( 'title_'+newid ) );
	});
	
	
	$('#contents').on('paste', 'div.editor', function(e){
		
		var oe = e.originalEvent;
		console.log($(this).get(0).contentWindow);
		if( oe && oe.clipboardData && oe.clipboardData.getData ){
			
			e.preventDefault();
			var text = oe.clipboardData.getData('text');
			document.execCommand('insertText', false, text);
			
		}else if( window.clipboardData ){			
			
			e.preventDefault();	
			document.execCommand('InsertInputHidden', false);
			var text = window.clipboardData.getData('text');
			$(this).find('input:hidden').replaceWith(text);
			
		}else{		
			
			setTimeout(function(){				
				e.currentTarget.innerHTML = $(e.currentTarget).text();
			},200);
			
		}		

	});
	
	$('#contents').on('focus', 'div.editor', function(e){	
		var offset = $(this).offset();
		if( $(e.currentTarget).find('.style_edit').length===0 )
			$(e.currentTarget).after('<span class="style_edit" style="position:absolute;z-index:10;left:'+(offset.left+4)+'px;top:'+(offset.top+5)+'px"></span>');
	});
	
	$('#contents').on('dblclick', 'div.editor', function(e){	
		//createEditor( $(this).get(0) );
	});
	
	$('#contents').on('blur', 'div.editor', function(e){	
		textChanged(e.currentTarget);
		setTimeout(function(){
			$(e.currentTarget).next('.style_edit').remove();
		},200);
	});
	
	$('#contents').on('click', 'span.style_edit', function(){		
		createEditor( $(this).prev('div.editor').get(0) );
		$(this).remove();
	});
	

	
	CKEDITOR.disableAutoInline = true;
	
	/*
	CKEDITOR.stylesSet.add( 'my_styles', [
	// Block-level styles
	{ name: 'Blue Title', element: 'strong', styles: { 'color': 'Blue' } },
	{ name: 'Red Title' , element: 'h3', styles: { 'color': 'Red' } },
	// Inline styles
	{ name: 'CSS Style', element: 'span', attributes: { 'class': 'my_style' } },
	{ name: 'Marker: Yellow', element: 'span', styles: { 'background-color': 'Yellow' } }
	]);
	*/
	


});

(function ($) {
	$.fn.editor = function () {
		var main = $(this);
		var target = $(this).parent('td').parent('tr').parent('tbody').parent('table').find('div.editor');
	};	
}(jQuery));

var editor, html = '';
var database = {};


function textChanged(currentTarget) {
	
	if( $(currentTarget).is('[target=item]') ){
			
		$(currentTarget).addClass('text_changed');
		
	}else if( $(currentTarget).is('[target=title]') ){
		
		$(currentTarget).addClass('text_changed');
			
	}else if( $(currentTarget).is('[target=degree]') ){
			
		$(currentTarget).addClass('text_changed');
			
	}else if( $(currentTarget).is('[target=explain]') ){
			
		$(currentTarget).addClass('text_changed');
			
	}else if( $(currentTarget).is('[target=item_sub]') ){
			
		$(currentTarget).parent('td').parent('tr').find('div[target=item]').addClass('text_changed');
			
	}
}



function createEditor(target) {
	
	if ( editor )
		editor.destroy();
	
	// Create a new editor inside the <div id="editor">, setting its value to html
	var config = {};
	
	//config.resize_enabled = false;
	config.toolbar =
		[
			
			{ name: 'document',    items : [ 'Source','-','NewPage' ] },
			//{ name: 'clipboard',   items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
			{ name: 'colors',      items : [ 'TextColor','BGColor' ] },
			{ name: 'styles',      items : [ 'FontSize' ] },
			//'/',
			{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
			//{ name: 'paragraph',   items : [ 'NumberedList','BulletedList','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ] },
			{ name: 'links',       items : [ 'Link','Unlink' ] },
			{ name: 'insert',      items : [ 'Image','SpecialChar' ] }			
		];
	//config.stylesSet = 'my_styles';
		
	/*
	config.keystrokes =[
    	[ CKEDITOR.CTRL + 86, 'pastefromword' ]
	];
	*/
	config.height = 100;
	config.startupFocus = true;
	
	
	//config.width = $(target).parent('td').width();
	//config.height = 80;
	//config.skin = 'moono';
	//config.resize_enabled = true;
	//config.enterMode = CKEDITOR.ENTER_BR; //可選：CKEDITOR.ENTER_BR或CKEDITOR.ENTER_DIV
	//config.toolbarStartupExpanded = true;

	
	//editor = CKEDITOR.replace( target, config );
	editor = CKEDITOR.inline( target, config );
	

	editor.on( 'instanceReady',function(ev){
		ev.editor.on( 'paste', function( evt ) {
			var sData = evt.data['html'];
			// do your thing here
			evt.data['html'] = sData;
			//alert(evt);
		});
		ev.editor.dataProcessor.writer.setRules( 'br',
			{
				indent : true,
				breakBeforeOpen : false,
				breakAfterOpen : false,
				breakBeforeClose : false,
				breakAfterClose : false
			});
	});
	/*
	editor.on( 'dataReady', function( e )
    {
		editor.on( 'blur', function( e )
    	{
        //alert( 'The editor named ' + e.editor.name + ' is now focused' );
    	}); 
    });
	*/
	return;
	
	editor.on( 'destroy', function( e ) {
		
		editor = '';
		
		if( $(target).parent('td').parent('tr').parent('tbody').parent('table').parent('div').is('.title_box') )
		$(target).addClass('text_changed');
		
		if( $(target).parent('td').parent('tr').parent('tbody').parent('table').parent('div').is('.var_box,.var_scale_box') )
		if( !$(target).parent('td').parent('tr').parent('tbody').parent('table').parent('div.var_box,div.var_scale_box').parent('div.fieldA').is('.changed') )
		if( $(target).is('[target=item]') ){
			
			$(target).addClass('text_changed');
			
		}else if( $(target).is('[target=degree]') ){
			
			$(target).addClass('text_changed');
			
		}else if( $(target).is('[target=explain]') ){
			
			$(target).addClass('text_changed');
			
		}else if( $(target).is('[target=item_sub]') ){
			
			$(target).parent('td').parent('tr').find('div[target=item]').addClass('text_changed');
			
		}
		
		$(target).parent('td').next('td').find('img.editfinish').attr('src','images/edit.png').attr('title','修改文字').attr('alt','修改文字').removeClass('editfinish').addClass('edittext');
    });
	
}



String.prototype.trim_hl = function() { return this.replace(/(^\s*)|(\s*$)/g, ""); } //去除頭尾空白

function recodeValue(target,qtype,code){
	var value = 1; 
	
	target.each(function(){
		if( qtype!='checkbox' )
		if( code=='auto' ){
			$(this).children('table').find('tbody > tr > td > input[name=v_value]').val(value);
		}
		if( qtype=='checkbox' ){
			$(this).children('table').find('tbody > tr > td > input[name=v_value]').attr('value','0,1');
		}		
		value++;
	});
	target.children('table').find('tbody > tr > td > input[name=v_value]').prop('disabled',!(code=='manual'));	
}

function recodeIndex(target){
	var value = 1; 
	
	target.each(function(){
		$(this).children('table').find('tbody > tr > td > input[name=v_value]').attr('index',value);
		value++;
	});
}



function getRandomNum(lbound, ubound) {
	return (Math.floor(Math.random() * (ubound - lbound)) + lbound);
}

function getRandomChar() {
	var numberChars = "0123456789";
	var lowerChars = "abcdefghijklmnopqrstuvwxyz";
	//var upperChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	//var otherChars = "`~!@#$%^&*()-_=+[{]}\\|;:'\",<.>/? ";
	
	var charSet = numberChars+lowerChars;	
	return charSet.charAt(getRandomNum(0, charSet.length));
}

function getPassword(length) {	
	var rc = "";
	
	if (length > 0)	
		rc = rc + getRandomChar();
	
	for (var idx = 1; idx < length; ++idx) {	
		rc = rc + getRandomChar();
	}	
	return rc;
}



</script>


</head>
	
<div style="margin:0 auto;width:300px;text-align: left;float:left">
	
	<div style="position:fixed;width:300px;background-color:#fff;border-top:0;z-index:1">
		<div style="background-color:#fff"> 
				
            <form style="display:inline" name="form1" action="" method="post">	
                <div>
                    頁數<input name="pages" type="text" size="2" value="<?=count($options_page)?>" disabled="disabled" />
                    <?=Form::select('page', $options_page, $page, array('style'=>'font-size:15px', 'onChange'=>'this.form.submit()'))?>
                    <input id="btn_cpage" type="button" value="新增一頁" <?=($can_edit=='enable'?'':'disabled="disabled"')?> />
                    <input id="btn_dpage" type="button" value="刪除整頁" disabled="disabled" />
                </div>
                <input id="btn_save" type="button" value="儲存檔案" <?=($can_edit=='enable'?'':'disabled="disabled"')?> />
                
                
                <input id="btn_analysis" type="button" value="線上分析存檔" />
                <input id="btn_pageskip" type="button" value="跳頁存檔" />
            </form>  
            
		</div>
		<div style="background-color:#fff">        
				<input id="btn_check" type="button" value="check" disabled="disabled" />            
				<input id="btn_creattb" type="button" value="重編Table" <?=($can_edit=='enable'?'':'disabled="disabled"')?> />
				<input id="btn_resetid" type="button" value="重編ID" disabled="disabled" />
				
				
		</div>

		<div style="background-color:#fff">        
				部分<input name="part" type="text" size="2" value="<?//=$part?>" />         
				部分名稱<input name="part_name" type="text" size="20" value="<?//=$part_name?>" />
				<div style="float:right"></div>
		</div>
        
		<div class="isShunt">
		<?		

		if( isset($isShunt) && $isShunt!='' )
		if( is_array(explode(',',$isShunt)) ){
			$page_attr = $question_array->attributes();
			if( isset($page_attr['shunt']) ){				
				$shunt_inpage = $page_attr['shunt'];
				$shunt_inpage_array = explode(',',$shunt_inpage);
			}else{
				$shunt_inpage_array = array();
			}
			echo '填寫身分';
			foreach(explode(',',$isShunt) as $shunt_n){
				echo '<input type="checkbox" name="isShunt" value="'.$shunt_n.'" '.(in_array($shunt_n,$shunt_inpage_array)?'checked="checked"':'').' />'.$shunt_n;
			}
			
		}
		?>
		</div>
		
		<div style="margin:0 auto">
			<div style="float:right">
				<div style="float:left"><?//=$changetime_text?></div>
				<div style="float:left;margin-left:40px"><div><a href="demo?page=1" target="_blank">預覽</a></div><div></div></div>
				<div style="float:left;margin-left:40px"><div><a href="creatTable">建立問卷</a></div></div>
			</div>
		</div>	
        
    </div>
</div>
	
<div id="building" style="border:0px dashed #A0A0A4;border-top:0;border-bottom:0;z-index:1;padding-left:200px;padding-right:200px">

	<div id="contents" style="margin-left:0px">
    
    <?	
    include_once($buildQuestion_editor_ver);    
    
    echo '<div class="main">';
        echo '<div class="addq_box" append="false" align="center" style="padding-right:0px;background-color:#fff;border:1px dashed #ccc">';
            echo '<table style="width:100%" cellpadding="1" cellspacing="1"><tr>';	
            echo '<td><div></div></td>';	
            echo '<td width="16px"><span class="addquestion" anchor="ques" addlayer="0" title="加入題目" /></td>';
            echo '<td width="1px"><div style="font-size:10px;color:red;background-color:#D4BFFF;width:170px;position:absolute;margin-left:20px"></div></td>';	
            echo '</tr></table>';
        echo '</div>';
    echo '</div>';	
    
    global $q_allsub;
    $q_allsub = array();

    if( $question_array  )
    foreach($question_array as $question){

        if($question->getName()=="question"){
            if($question->explain!="")
            echo '<div class="readme"><h3><b>'.iconv('utf-8', 'big5',$question->explain).'</b></h3></div>';

            echo '<div class="main">';
            buildQuestion($question,$question_array,0,"no");
            echo '</div>';
        }
    }

    echo '<div class="main" style="margin-top:100px;border:2px dashed red">';

    if( $question_array )
    foreach($question_array as $question){		
        if($question->getName()=="question_sub"){
            if( !in_array($question->id,$q_allsub) ){	
                buildQuestion($question,$question_array,0,"no");
            }
        }
    }		
    echo '</div>';
		
    ?>
        
	</div>

	<div id="footer" style="margin-bottom:20px"></div>
    
</div>