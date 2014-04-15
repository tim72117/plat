<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Question Management</title>

<!--[if lt IE 9]><script src="js/html5shiv.js"></script><![endif]-->
<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="js/jQuery-File-Upload-9.2.1/js/jquery.fileupload.js"></script>
<script type="text/javascript" src="<?=asset('js/Highcharts-3.0.7/js/highcharts.js')?>"></script>

<link rel="stylesheet" href="css/onepcssgrid-1p.css" />
<link rel="stylesheet" href="js/smoothness/jquery-ui-1.10.3.custom.min.css" />
<link rel="stylesheet" href="<?=asset('css/onepcssgrid.css')?>" />
<link rel="stylesheet" href="<?=asset('css/management/share.css')?>" />
<link rel="stylesheet" href="<?=asset('css/management/share.index.css')?>" />

<script type="text/javascript">
$(document).ready(function(){

	var fakeFileUpload = document.createElement('input');
	fakeFileUpload.type = 'file';
	fakeFileUpload.multiple = true;

	$('.upload').click(function(){
		fakeFileUpload.click();
	});	

	var fileUploadQueue = {
		index: 0,	
		dataLength: 0,
		fileDataArray: [],
		uploadObj: null,
		add: function(data,progress){
			this.fileDataArray.push({progress:progress,data:data});
			this.dataLength = this.fileDataArray.length;
		},
		upload: function(){
			console.log(this.index);
			if( this.index<this.dataLength ){
				this.uploadObj = this.fileDataArray[this.index];
				this.uploadObj.data.submit();
				this.index++;
			}else{
				this.index = 0;
				this.dataLength = 0;
				this.fileDataArray = [];
			}
		}
	};
	
	$('#uploadbox').children('button').click(function(){			
		fileUploadQueue.upload();				
	});
	
	$(fakeFileUpload).fileupload({
        url: 'http://localhost:800/upload',
		//url: 'http://localhost/server/php/index.php',
        dataType: 'json',
		replaceFileInput: false,
		add: function (e, data){			
			//console.log(data);
			//console.log(e);
			$('#uploadbox').show();
			$('#uploadbox').append('<div class="upload-file">'+data.files[0].name+'</div>');
			var progress = $('<div style="width:0;height:100%;background-color: #dd4b39"></div>').appendTo($('<div style="width:100%;height:2px"></div>').appendTo('#uploadbox'));
			//progress.append('<div style="width:0;height:100%;background-color: #dd4b39"></div>');
			fileUploadQueue.add(data,progress);
		},		
        done: function (e, data){
            //console.log(data);
			$.each(data.result.files, function (index, file) {
                fileUploadQueue.upload();
				//$('<p/>').text(file.name).appendTo('#files');
            });
        },
        progressall: function (e, data){
            var progress = parseInt(data.loaded / data.total * 100, 10);
            fileUploadQueue.uploadObj.progress.css(
                'width',
                progress + '%'
            );
        }
    });
	
	false && 
	$('.upload').get(0).addEventListener('dragover', function(evt){
		console.log('dragover');
		evt.stopPropagation();
		evt.preventDefault();
	}, false);
	
	
	var reader = new FileReader();
	reader.onloadstart = function(e) {
		console.log('start');
	};
		
	false && 
	$('.upload').get(0).addEventListener('drop', function(evt){
		evt.stopPropagation();
		evt.preventDefault();

		//reader.readAsBinaryString(evt.dataTransfer.files[0]);
	}, false);
	
	$('.cfolder').click(function(){
		$('#filelist').append(
			'<tr class="question edit">'+
				'<td style="border-bottom: 1px solid #ddd;line-height:40px;width:80px"></td>'+
				'<td style="border-bottom: 1px solid #ddd;line-height:40px;width:350px"><div class="file folder"></div><div class="mininput" contenteditable="true"></div></td>'+
				'<td style="border-bottom: 1px solid #ddd;line-height:40px"></td>'+
			'</tr>').find('.mininput').focus();
	});
	
	$('#filelist').on('blur','.mininput',function(e){
		var c = $(e.currentTarget);
		c.removeAttr('contenteditable');
		c.parent().parent().removeClass('edit');
	});
	
	false && 
	$('.upload').get(0).addEventListener('dragenter', function(evt){
		evt.stopPropagation();
		evt.preventDefault();
	}, false);
	
	false && 
	$('.upload').get(0).addEventListener('dragleave', function(evt){
		evt.stopPropagation();
		evt.preventDefault();
	}, false);
	
	
	$('.cnew').click(function(){
		$('.newQuestion').toggle();		
	});
	
	$('.filetype-list').click(function(){
		$('.newQuestion').hide();
		var width = $('body').width();
		var height = $('body').height();
		$('<div class="dialog"></div>').css({
			position: 'absolute',
			backgroundColor: '#fff',
			border: '1px solid #ccc',
			width: '500px',
			height: '500px',
			top: height/2-250,
			left: width/2-250
		})
		.append('<input type="input" class="questionName" />')
		.append('<div class="button submit">確定</div>')
		.appendTo('body');
	});
	
	$('body').on('click','.submit',function(){
		var questionName = $('.questionName').val();
		$('#filelist').append(
			'<tr class="question edit">'+
				'<td style="border-bottom: 1px solid #ddd;line-height:40px;width:80px"></td>'+
				'<td style="border-bottom: 1px solid #ddd;line-height:40px;width:350px"><div class="file question"></div><div class="mininput" contenteditable="true">'+questionName+'</div></td>'+
				'<td style="border-bottom: 1px solid #ddd;line-height:40px"></td>'+
			'</tr>').find('.mininput').focus();
	});
	
	var contextmenu;
	$('.question').mouseup(function(e){		
		if( e.which===3 ){			
			var c = $(e.currentTarget);
			var offset_top = c.offset().top;
			var offset_left = e.pageX;
			( contextmenu instanceof $ ) && contextmenu.remove();
			contextmenu = $('<div class="dialog"></div>').css({
				position: 'absolute',
				backgroundColor: '#fff',
				border: '1px solid #ccc',
				width: '200px',
				height: '300px',
				top: offset_top+5,
				left: offset_left+5
			})
			.appendTo('body');
			contextmenu.one('click',function(e){
				var c = $(e.currentTarget);
				c.hide();
			});
		}else{
			$('#current').children().stop().animate({
				left: '0'
			});
		}		
	});
	$('.question').bind('contextmenu',function(){ return false; });
	
	console.log(  );
	
	$('#current').children().css({
		left: $('#current').width()
	});
	
});
</script>
<style>
html,body {
	height: 100%;
	font-family: 微軟正黑體;
}
body {
	margin: 0;
	padding: 0;
}
.question:hover {
	background-color: rgba(255,255,255,1);/*#dd4b39;*/
	color: #000;
	cursor: pointer;
}
.question.edit {
	background-color: rgba(221,75,57,0.3);/*#dd4b39;*/
	color: #000;
}

.cnew {
	/*background-image: url(images/cfolder.png);*/
	background-repeat: no-repeat;
	width: 62px;
	height: 32px;
	background-position: center;
	background-color: #dd4b39;
	/*background-image: -webkit-linear-gradient(top,#dd4b39,#d14836);*/
	border-radius: 3px;
	text-align: center;
	line-height: 32px;
	color: #fff;
	border: 1px solid #ddd;
}
.upload {
	background-image: url(images/upload.png);
	background-repeat: no-repeat;
	background-position: center;
	width: 32px;
	height: 32px;
	background-color: #dd4b39;
	border-radius: 3px;
	text-align: center;
	line-height: 32px;
	border: 1px solid #ddd;
}
.cfolder {
	background-image: url(images/cfolder.png);
	background-repeat: no-repeat;
	background-position: center;
	width: 42px;
	height: 32px;
	border-radius: 3px;
	text-align: center;
	line-height: 32px;
	border: 1px solid #ddd;
}
.upload-file {
	font-size: 12px;
}
.button {
	-ms-user-select:none;
	-webkit-user-select: none;
}
.button:hover {
	cursor: pointer;
}
.mininput {
	display: block;
	margin: 5px auto 5px 28px;
	font-size: 14px;
	line-height: 30px;
	outline-color: #dd4b39;
	border: 0;
}
.border-box {
	-webkit-box-sizing: border-box;	
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}
.filetype-list {
	background-image: url(images/File_edit.png);
	background-repeat: no-repeat;
	background-size: 32px 32px;
	background-position: 10px center;
	width: 80px;
	height: 60px;
	border: 1px solid #aaa;
	background-color: rgba(255,255,255,0.3);
	border-radius: 3px;
	line-height: 60px;
	padding-left: 42px;
	cursor: pointer;
}
.dialog {
	box-shadow: 5px 5px 5px -2px #aaa;
}
.questionName {
	padding: 5px;
	height: 20px;
}
.submit {
	width: 62px;
	height: 32px;
	background-color: #dd4b39;
	border-radius: 3px;
	text-align: center;
	line-height: 32px;
	border: 1px solid #ddd;
	color: #fff;
}
.level1 {
	margin-left: 20px;
	background-color: #66ff66;
}
.level2 {
	margin-left: 40px;
}
.level3 {
	margin-left: 60px;
	background-color: #66ff66;
}
</style>
</head>

<body>
<?php
if( false ){
	$excel = new COM("Excel.Application") or die("Did not connect");
	//echo $excel->Application->version;
	$xlsFile = "C:\AppServ\www\ques\public\member.xls";
	$excel->Visible = 0;
	//$Workbook = $excel->Workbooks->Open($xlsFile);
	//$Worksheet = $Workbook->Worksheets(1);
	//$cell = $Worksheet->Range('A2');
	//$rows = $Worksheet->UsedRange->Count();
	//echo $rows;
	//echo iconv("big5","UTF-8",$rows->value);
	//$excel->Workbooks->Close();
	//$excel->Quit();
}
				
				/*
				$contents = file_get_contents(app_path().'/views/ques/data/tintern102/setting.php');
				$sorce_token = token_get_all($contents);
				
				//$constants = get_defined_constants(true); 
				//var_dump($constants['tokenizer']);
				

				$source = set_var($sorce_token,array('dateStart'=>'2013-09-29 12:00:01'));//2013-09-29 12:00:00
				file_put_contents(app_path().'/views/ques/data/tintern102/setting_new.php', $source);
				
				//var_dump($sorce_token);
				 * 
				 */
				
				
function set_var($sorce_token,$set = null){
	$source = '';
	$line = 0;
	$paren_count = 0;
	$sorce_token_new = $sorce_token;
	foreach($sorce_token as $key => $text){
		if( is_array($text) ){

			$line = $text[2];

			if( $text[0]==362 ){
				if( $paren_count>0 ){
					//echo '<span style="margin-left:'.($paren_count*20).'px">'.$text[1].'</span>';
				}else{
					//echo $text[1];
				}
			}

			if( $text[0]==315 ){
				if( is_array($sorce_token[$key+2]) && $sorce_token[$key+2][0]==360 && $paren_count==1 )
					if( is_array($sorce_token[$key+4]) && ($sorce_token[$key+4][0]==315  || $sorce_token[$key+4][0]==307) ){
											
						if( $sorce_token[$key+4][0]==315 ){
							$set_key = substr($text[1],1,strlen($text[1])-2);
							
							if( array_key_exists($set_key,$set) ){
								//echo $text[1];
								$sorce_token_new[$key+4][1] = '\''.$set[$set_key].'\'';
							}
						}
					}
			}

			$source .= $sorce_token_new[$key][1];
		}else{
			$source .= $sorce_token_new[$key];

			if( $text=='(' ){
				if( is_array($sorce_token[$key-1]) && $sorce_token[$key-1][0]==362 ){
					//echo '('.$paren_count.'<br />';
				}else{								
					//echo '<span style="margin-left:'.($paren_count*20).'px">('.$paren_count.'</span>';
				}
				$paren_count++;
			}

			if( $text==')' ){
				if( $paren_count>0 ){
					//echo '<span style="margin-left:'.($paren_count*20).'px">)</span><br />';
				}else{
					//echo ')<br />';
				}
				$paren_count--;

			}

		}
	}
	return $source;
}


				
  

?>
<style>
li.tabs {
	height: 25px;
	line-height: 25px;
	position: relative;
	display:block;	
	float: left;
	border: 0px solid #444;
	border-left-color: #aaa;
	border-bottom-width:0px;
	border-bottom-color:#777;
	list-style: none;
	padding: 5px;
	margin-left: 3px;
	color:#fff;
	cursor:pointer;
	background-color:#fff 
}
a.button {
	display:block;	
	text-decoration:none;
	font-weight:400;
	color:#63bd2b;
	font-weight:bold
}
.file {
	margin: 0;
	width: 40px;
	height: 40px;
	float: left;
	background-repeat: no-repeat;
	background-position: center;
}
.file.normal {
	background-image: url(images/file.png);	
}
.file.folder {
	background-image: url(images/folder.png);
}
.file.question {
	background-image: url(images/File_edit_16.png);
	background-position: 2px;
}
.question {
	background-color: #fff;
}

.question.open {
	background-color: rgba(200,200,200,0.1);
}

.button {
	cursor: pointer;
}


</style>

<div style="width:100%;height:50px;background-color: #63bd2b;color:#fff;position:fixed;top:0;left:0;z-index:100">
	<div style="position:relative" class="tabs-box">
		<? echo $child_tab; ?>
		<div style="clear:both"></div>
	</div>
	
</div>

<div style="width:55px;height:100%;background-color: #000;border-right: 1px solid #333;position:fixed;z-index:200">
	
	<div style="height:80px"></div>
	<div class="button folder" style="background-size: 48px 48px;background-repeat: no-repeat;background-image: url(images/folder_sticker.png);width:48px;height:48px"></div>
	<div class="button" style="background-size: 48px 48px;background-repeat: no-repeat;background-image: url(images/paper-clip_sticker.png);width:48px;height:48px;margin-top:20px"></div>
	
</div>

<div style="padding-left:55px;padding-top:50px;background-color: #eee;min-height:100%;box-sizing: border-box">
	
	<div style="">
		
		<div style="margin:20px auto 0 auto" cellpadding="0" cellspacing="0">
			<div style="margin:0 auto;width:800px">
				<div class="" style="border-bottom: 1px solid #eee;margin:3px;height:40px;background-color: #fff">
					<div style="height:40px;line-height:40px;width:80px;float:left"></div>
					<div style="height:40px;line-height:40px;width:350px;float:left;text-align:center">標題</div>
					<div style="height:40px;line-height:40px;float:left"></div>
				</div>
			</div>
			<div>
				<?=View::make('management.platform.docs')?>
			</div>
		</div>
		
	</div>

</div>


	
<!--
<div style="width: 100%;height: 100%;max-height:100%">

	<div style="width:100%;height: 130px;position: absolute;z-index:10;background-color: #fff">
		<div style="background-color: #eee;width:100%;height: 80px"></div>
		<div style="background-color: #fff;width:100%;height: 50px;border-bottom: 1px solid #ddd">
			<div style="width:200px;margin-left: 200px">
				<div class="button cfolder" style="float:left;margin:10px auto auto 80px"></div>
			</div>			
		</div>
	</div>
	
	<div class="border-box" style="height:100%;width:100%;background-color: #fff;padding-top:130px">
		
		<div style="height:100%;overflow-y: hidden;float:left;width:200px">
			<div style="height:100%;background-color: #fff;border-right: 1px solid #ddd;overflow-y: auto">
				<div style="margin:20px 0 0 20px">
					<div class="button cnew" style="float:left">建立</div>
					<div class="button upload" style="float:left;margin-left:5px"></div>
				</div>
				<div class="dialog newQuestion" style="display: none;position: absolute;width:200px;height:250px;z-index: 10;top:184px;left:20px;background-color: #fff;border: 1px solid #ccc">
					<div class="button filetype-list">問卷檔案</div>
				</div>
			</div>
		</div>
			

		<div style="height: 100%;overflow-y: hidden;float:left;width:500px">
			<div style="height: 100%;overflow: auto;background-color: #fff;font-size:14px;text-align: left;;border-right: 1px solid #ddd">
				<table id="filelist" style="width:100%" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<th style="border-bottom: 1px solid #ddd;line-height:40px;width:80px">1</th>
							<th style="border-bottom: 1px solid #ddd;line-height:40px;width:350px">標題</th>
							<th style="border-bottom: 1px solid #ddd;line-height:40px"></th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>			
		</div>
		
		
		<div style="height: 100%;overflow: hidden" id="current">
			<div style="height:100%;overflow:auto;background-color:#fff;font-size:14px;text-align:left;position:relative">
				<?


				
				function cfile(){
					
				}
				/*
				$query = DB::table('question')->where('CID','71101')->select('QID','question_label','column_name','part','spss_name','qtree_level','qtree_parent','qtype','competence')->orderBy('qtree_level');
				//echo $query->toSql();
				$question = $query->get();
				
				$doc = new DOMDocument();
				$doc->validateOnParse = true;
				
				$ul = $doc->createElement( 'ul' );
				$doc->appendChild( $ul );
				foreach($question as $q){
					$li = $doc->createElement( 'li' );
					$li->setAttribute( 'id', $q->spss_name );
					$li->setAttribute( 'class', 'level'.$q->qtree_level );
					$label = $doc->createElement( 'label', $q->question_label );					
					$li->appendChild( $label );					
					
					if( $q->qtree_parent!='' ){
						//echo $q->qtree_parent;
						$xpath = new DOMXPath($doc);
						
						
						if( $xpath->query("//*[@id='$q->qtree_parent']")->length===1 )
							$xpath->query("//*[@id='$q->qtree_parent']")->item(0)->appendChild( $li );//->appendChild( $doc->createElement( 'label', '' ) );
						//exit;
						//$doc->getElementById( $q->qtree_parent )->tagName;
					}else{
						$ul->appendChild( $li );
					}
						
				}
				

				
				echo $doc->saveHTML();
				 * 
				 */
				?>
			</div>
		</div>
		

		
	</div>
	
</div>	
-->	
	<div id="uploadbox" style="width:200px;height:200px;padding:2px;position: absolute;background-color: #fff;left:1px;bottom:1px;border: 1px solid #aaa;z-index:10;display:none"><button>上傳</button></div>

	
</body>
</html>