<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>後期中等教育資料庫查詢平台</title>

<link href="css/pub.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=asset('js/tigra_tables.js')?>"></script>
<script type="text/javascript" src="<?=asset('js/jquery-1.10.2.min.js')?>"></script>
<script type="text/javascript" src="<?=asset('js/jeditable.js')?>"></script>
<script type="text/javascript" src="<?=asset('js/jquery-ui-1.10.3.custom.min.js')?>"></script>
<!--[if lt IE 9]>
<script src="js/html5shiv.js"></script>
<![endif]-->

<link rel="stylesheet" href="<?=asset('js/jquery-ui-1.10.3.custom.min.css')?>" />

<script type="text/javascript">
 	$(document).ready(function(){	//選單功能
    	$( "#Layer1" ).accordion({
      		heightStyle: "content"
		});
		$( "#Layer2" ).accordion({
      		heightStyle: "content"
		});
		$('a').click(function(){
			$("#pageLoad").load($(this).attr("href"));
			// Prevent browsers default behavior to follow the link when clicked
			return false;
		});
		$('#pageLoad').on('click','.pagebtn',function(){
			$("#pageLoad").load($(this).attr("src"));
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
	background-color: #dd4b39;
	color: #fff;
}
.folder {
	background-image: url(images/folder.png);
	line-height: 40px;
	margin: 12px 12px 12px 0;
	width: 16px;
	height: 16px;
	float: left;
}
.cnew {
	/*background-image: url(images/cfolder.png);*/
	background-repeat: no-repeat;
	width: 62px;
	height: 32px;
	background-position: center;
	background-color: #dd4b39;
	/*background-image: -webkit-linear-gradient(top,#dd4b39,#d14836);*/
	border-radius: 5px;
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
.upload-file {
	font-size: 12px;
}
.button:hover {
	cursor: pointer;
	-ms-user-select:none;
	-webkit-user-select: none;
}
.border-box {
	-webkit-box-sizing: border-box;	
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}
.ui-widget {
	font-size: 12px;
}
#Layer1 ul {
	margin: 0px;
	padding: 0px;
	list-style-type: none;
}
#Layer2 ul {
	margin: 0px;
	padding: 0px;
	list-style-type: none;
}

a,a:link,a:visited{color:#0066FF;text-decoration: none}
a:hover{color:#FFFFFF;background-color: #FF6600;text-decoration: none;}

.clear {
	clear: both;
	height: 0;
}
</style>


</head>

<body>



<div style="width: 100%;height: 100%;max-height:100%">

	<div style="width:100%;height: 130px;position: absolute;z-index:10;background-color: #fff">
		<div style="background-color: #fff;width:100%;height: 80px"><img src="<?=asset('demo/use/images/title.jpg')?>" width="500" height="80"></div>
		<div style="background-color: #eee;width:100%;height: 20px;border-bottom: 1px solid #ddd" align="right">
	    	&nbsp;<a href="<?=URL::to('user/auth/logout')?>" target="_top">登出</a>;
        </div>
	</div>
	
	<div class="border-box" style="height:100%;width:100%;background-color: #f00;padding-top:130px">
		
<div style="height:100%;overflow-y: hidden;float:left">
	<div style="width: 350px;height:100%;background-color: #fff;border-right: 1px solid #ddd;overflow-y: auto">
		<div id ="Layer1">
			
			<div>
			<ul>				
				<li id="links"><a href="<?=URL::to('page/project')?>">回首頁</a></li>
				<li id="links"><a href="<?=URL::to('user/auth/password/change')?>">更改密碼</a></li>
			</ul>
			</div>

                     
<div id="Layer4">
<h4>【 後期中等教育資料庫查詢平台 】</h4>
<?
$user = Auth::user();
$packageDocs = $user->get_file_provider()->lists();


foreach($packageDocs as $packageDoc){
	foreach($packageDoc['actives'] as $active){		
		
		echo '<div class="inbox" style="clear:both;overflow: hidden;cursor:default">';
		if( $active['active']=='open' ){
			echo '<div class="count button" folder="" style="font-size:12px;text-decoration: underline;float:left;margin-left:10px">';
			//echo '<div class="intent button" intent_key="'.$active['intent_key'].'">'.$active['active'].'</div>';
			echo '<a href="'.URL::to('user/doc/'.$active['intent_key']).'">'.$active['active'].$packageDoc['title'].'</a> - ';
			echo '</div>';
		}
		echo '</div>';
	}
}
?>
</div>
                     
                     
		</div>
	</div>
</div>

		<div style="height: 100%;overflow-y: hidden;margin-left: 200px">
			<div style="height: 100%;overflow: auto;background-color: #fff;font-size:14px;text-align: left">
				<?=$context?>
				<table style="width:100%" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<div id='pageLoad'><? //echo Session::get('sch_id'); ?></div>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>			
		</div>
		
	</div>
	
</div>	

</body>
</html>