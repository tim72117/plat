<?php
$buildQuestion_editor_ver = app_path().'/views/files/ques/buildQuestion_editor__v2.0.laravel.php';

include_once($buildQuestion_editor_ver);

$pages = $census->pages;

$page = $pages->filter(function($page) {
    return $page->page == Input::get('page', 1);
})->first();

$question_array = simplexml_load_string($page->xml);
?>
<head>

<script src="/js/jquery-1.11.2.min.js"></script>
<script src="/js/ckeditor/ckeditor_v4.1/ckeditor.js"></script>

<link rel="stylesheet" href="/editor/structure_new.css" />

<script>
$(document).ready(function() {

    var valInObj = {page: $('#page').val(), obj: []};

    //---------------------------------------------------------------------------------新增一頁
    $('#btn_cpage').click(function(){
        $.post('add_page',function(data){
            location.reload();
            console.log(data);
        }).error(function(e){
            console.log(e);
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
            valInObj.obj.push(valInObj_one);
            //alert('修改文字欄位_標題');
        }
        //-------------------------------------------------------------------------修改文字欄位_標題
        $('div[target=title].text_changed').each(function(){
            var valInObj_one = {
                target: 'title',
                id: $(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.title_box').parent('div.question_box').attr('id'),
                title: $(this).html()
            };
            valInObj.obj.push(valInObj_one);
        });
        //-------------------------------------------------------------------------修改文字欄位_大標題
        $('div[target=explain].text_changed').each(function(){
            var valInObj_one = {
                target: 'explain',
                id: $(this).parent('td').parent('tr').parent('tbody').parent('table').parent('div.explain_box').attr('index'),
                title: $(this).html()
            };
            //valInObj.obj.push(valInObj_one);
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
            valInObj.obj.push(valInObj_one);
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

                if ($(this).is(':has(td[name=otherv])')){
                    othervArray = $.map($(this).find('td[name=otherv]').children('input'),function(n){
                        return {name:$(n).attr('name'),value:$(n).val()};
                    });
                }

                var onvalue = $(this).children('table').find('input').val();
                var sub_title = qtype=='text'?$(this).children('table').children('tbody').children('tr:eq(0)').find('div[target=item_sub]').text():'';
                var ccheckbox = $(this).children('table').find('span.ccheckbox').hasClass('enable');

                if (qtype=='text' || qtype=='textarea'){
                    var size = $(this).children('table').find('input[name=tablesize]').val();
                }else{
                    var size = 0;
                }

                if (qtype!='scale' && qtype!='select')
                itemArray.push({
                    value: onvalue,
                    subid_array: subid_array,
                    skipArray: skipArray,
                    othervArray: othervArray,
                    ccheckbox: ccheckbox,
                    size: size,
                    title: $(this).find('div.editor').html(),
                    sub_title: sub_title,
                    ruletip: ''
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
        console.log(valInObj);
        if( valInObj.obj.length>0 && !checkerror )
        $.post('save_data', valInObj, function(data){
            console.log(data);
            for(var i in valInObj.obj){
                //$('div[qid='+valInObj.obj[i].id+']').attr('text_changed','false');
            }
            $('div.fieldA').removeClass('changed');
            $('div.editor').removeClass('text_changed');
            $('div.qtype_box').removeClass('changed');


            alert('儲存成功');
            valInObj.obj = [];
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
            '<table><tr>'+
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
            '<table class="ui very basic very compact table"><tr>'+
            '<td width="30px"><input name="v_value" type="text" size="1" disabled="disabled" value="" /></td>'+
            '<td><div class="editor item text_changed" target="degree" contenteditable="true" style="border:1px solid #A0A0A4;min-height:22px"></div></td>'+
            '<td width="16px"><span class="adddegree" anchor="var" addlayer="'+addlayer+'" title="加入量表選項"><i class="add circle icon"></i></span></td>'+
            '<td width="16px"><span class="deletevar scale" title="刪除量表選項"><i class="minus circle icon"></i></span></td>'+
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

            '<table class="ui very basic very compact table"><tr>'+
            (( qtype=='radio' || qtype=='select' || qtype=='checkbox' )?'<td width="30px"><input name="v_value" type="text" size="1" disabled="disabled" value="" /></td>':'')+
            (( qtype=='text' || qtype=='textarea' || qtype=='scale' || qtype=='list' )?'<td style="display:none"><input name="v_value" type="hidden" size="1" disabled="disabled" value="" /></td>':'')+

            (( qtype=='text' )?'<td width="1px"><input name="tablesize" type="text" size="2" value="" /></td>':'')+

            '<td><div class="editor item text_changed" target="item" contenteditable="true" style="border:1px solid #A0A0A4;min-height:22px"></div></td>'+

            (( qtype=='radio' || qtype=='select' )?'<td width="16px"><span class="skipq" title="設定跳題" /></td>':'')+

            (( qtype=='text' )?'<td width="300px"><div class="editor item text_changed" target="item_sub" contenteditable="true" style="border:1px solid #A0A0A4"></div></td>':'')+

            '<td width="16px"><span class="addvar" anchor="var" addlayer="'+addlayer+'" title="加入'+edittext+'"><i class="add icon"></i></span></td>'+
            '<td width="16px"><span class="deletevar" title="刪除'+edittext+'"><i class="minus icon"></i></span></td>'+
            (( qtype=='radio' || qtype=='select' || qtype=='checkbox' || qtype=='list' )
                ?'<td width="16px"><span class="addquestion" anchor="var" addlayer="'+(addlayer+1)+'" title="加入題目"><i class="dropdown icon"></i></span></td>'
                :''
            )+

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

        target.remove();

        var code = target_parent.children('div.initv_box').find('select[name=code]').val();
        recodeValue(target_parent.children('div.var_box'),qtype,code);
        recodeIndex(target_parent.children('div.var_box'));

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
                    '<table class="ui very basic very compact table"><tr>'+
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
    $("#contents").on("click", "div.main .addquestion", function() {

        var qanchor =  $(this).attr('anchor');
        var addlayer = $(this).attr('addlayer');
        var newid = 'QID_'+getPassword(8);
        var qlab_length = newid.length;

        if( addlayer==0 ){
            var target = $(this).parent('.addq_box').parent('div.main');
            var newq = $('<div class="question_box ui tertiary segment" id="'+newid+'" uqid="'+newid+'" parrent="" layer="'+addlayer+'" style="display:none">').appendTo( $('<div class="main" style="width: 800px"></div>').insertAfter( target ) );
        }
        if( addlayer!=0 ){
            var target = $(this).parent().is('.addq_box') ? $(this) : $(this).parent('td').parent('tr').parent('tbody').parent('table');
            if( target.parent().is('.addq_box') ) {
                var newq = $('<div class="question_box ui tertiary segment" id="'+newid+'" uqid="'+newid+'" parrent="" layer="'+addlayer+'" style="display:none;margin-left:45px">').insertAfter( target.parent('div.addq_box') );
            }

            if( target.parent().is('.var_box') )
            if( target.next('div').is('.sub') ){
                var newq = $('<div class="question_box ui tertiary segment" id="'+newid+'" uqid="'+newid+'" parrent="" layer="'+addlayer+'" style="display:none;margin-left:45px">').prependTo( target.next('div.sub') );
            }else{
                var sub_box = $('<div class="sub"></div>').insertAfter( target );
                var newq = $('<div class="question_box ui tertiary segment" id="'+newid+'" uqid="'+newid+'" parrent="" layer="'+addlayer+'" style="display:none;margin-left:45px">').prependTo( sub_box );
            }
        }


        var type_box = $(
            '<div class="qtype_box changed">'+
                (addlayer!=0?'<div style="position:absolute;margin-left:-30px"><img src="/editor/images/link.png" alt="" /></div>':'')+
                '<h5 class="ui header">' + '' + '<div class="sub header">' + newid + '</div></h5>'+
                '<table class="ui very basic very compact table">'+
                '<tr>'+
                '<td>'+
                '<select class="ui dropdown" name="qtype">'+
                    '<option value="select">題型 : 單選題(下拉式)</option>'+
                    '<option value="radio">題型 : 單選題(點選)</option>'+
                    '<option value="checkbox">題型 : 複選題</option>'+
                    '<option value="text">題型 : 文字欄位</option>'+
                    '<option value="textarea">題型 : 文字欄位(大型欄位)</option>'+
                    '<option value="scale">題型 : 量表</option>'+
                    '<option value="list">題型 : 題組</option>'+
                    '<option value="explain">題型 : 說明文字</option>'+
                '</select>'+
                '</td>'+
                '<td width="16px"><span class="deletequestion" alt="刪除題目" /></td>'+
                '<td width="1px"><div style="font-size:10px;color:red;background-color:#D4BFFF;width:170px;position:absolute;margin-left:20px"></div></td>'+
                '</tr>'+
                '</table>'+
            '</div>'+

            '<div class="title_box">'+
                '<table class="ui very basic very compact table"><tr>'+
                '<td width="65px" valign="top"><input name="qlab" type="text" size="4" value="" /></td>'+
                '<td><div id="title_'+newid+'" class="editor title ui segment" contenteditable="true" target="title">請修改文字</div></td>'+
                '<td width="1px"><div style="font-size:10px;color:red;background-color:#D4BFFF;width:170px;position:absolute;margin-left:20px"></div></td>'+
                '</tr></table>'+
            '</div>'
        ).appendTo(newq);

        var var_box = $(
            '<div class="fieldA changed" init="">'+
                '<div class="initv_box" style="margin-right:0px;border:0px dashed #A0A0A4">'+
                '<table class="ui very basic very compact table"><tr>'+
                '<td width="16px"><select name="code">'+
                    '<option value="auto">自動編碼</option>'+
                    '<option value="manual">手動編碼</option>'+
                '</select></td>'+
                '<td><div class="editor title" style=";border:0px dashed #A0A0A4;background-color:#D7E6FC"></div></td>'+

                '<td width="16px"><span class="addvar" anchor="var" addlayer="'+addlayer+'" title="加入選項"><i class="add icon"></i></span></td>'+
                '<td width="16px"><span class="addvar_list" anchor="var" addlayer="1" title="匯入選項" /></td>'+
                '<td width="16px"><span class="deletevar_list" anchor="var" addlayer="1" title="刪除全部選項" /></td>'+
                '<td width="1px"></td>'+
                '</tr></table>'+
                '</div>'+
            '</div>'+
            '<p class="contribute"></p>'
        ).appendTo(newq);

        var addq_box = $(
            '<div class="addq_box '+(addlayer==0?'root':'sub')+'" append="false">'+
                '<div class="ui horizontal divider addquestion" anchor="' + qanchor + '" addlayer="' + addlayer + '"><i class="add icon"></i> 加入題目 </div>'+
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
        if (qtype!='checkbox' && code=='auto') {
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

<md-content layout="column" layout-align="start center" style="height:100%">


<md-toolbar>
    <div class="md-toolbar-tools">
        <span flex></span>
        <form name="form1" action="" method="get" style="color:#000">
        <?=Form::select('page', $pages->pluck('page', 'page'), $page->page, array('id' => 'page', 'onChange'=>'this.form.submit()'))?>  /  <?=$pages->count()?>
        </form>
        <md-button ng-disabled="<?=($census->edit ? 'false' : 'true')?>" id="btn_cpage">
            <md-icon md-svg-icon="insert-drive-file"></md-icon> 新增一頁
        </md-button>
        <md-button ng-disabled="<?=($census->edit ? 'false' : 'true')?>" id="btn_save">
            <md-icon md-svg-icon="save"></md-icon> 儲存
        </md-button>
        <md-button href="demo?page=1" target="_blank">
            <md-icon md-svg-icon="icon-eye"></md-icon> 預覽
        </md-button>
        <span flex></span>
    </div>
</md-toolbar>



<div id="building" style="overflow-y: auto">

    <div ng-cloak id="contents" layout="column" layout-align="start center">

    <div class="main" style="width: 800px">
        <div class="addq_box" append="false">
            <div class="ui horizontal divider addquestion" anchor="ques" addlayer="0"><i class="add icon"></i>加入題目</div>
        </div>
    </div>
    <?php

    global $q_allsub;
    $q_allsub = array();

    foreach($question_array as $question){

        if($question->getName()=="question"){
            if($question->explain!="")
            echo '<div class="readme"><h3><b>'.iconv('utf-8', 'big5',$question->explain).'</b></h3></div>';

            echo '<div class="main" style="width: 800px">';
            buildQuestion($question,$question_array,0,"no");
            echo '</div>';
        }
    }

    echo '<div class="main">';

    echo '<div class="ui horizontal divider" anchor="ques" addlayer="0">錯誤項目 </div>';

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

</div>

</md-content>