<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Question Management</title>

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
	    	&nbsp;<a href="auth/logout" target="_top">登出</a>&nbsp;|&nbsp;<a href="https://use-database.cher.ntnu.edu.tw/use/" target="_top">回台灣後期中等教育資料庫整合計畫首頁</a>
        </div>
	</div>
	
	<div class="border-box" style="height:100%;width:100%;background-color: #f00;padding-top:130px">
		
		<div style="height:100%;overflow-y: hidden;float:left">
			<div style="width: 350px;height:100%;background-color: #fff;border-right: 1px solid #ddd;overflow-y: auto">
				<div id ="Layer1">
					<h5>聯絡人查詢及修改</h5>
						<div>
						<ul>
							<? /*<p><li id="links"><a href="<?php // echo URL::to('changepasswd'); ?>" pageid="1" src="">更改密碼</a></li></p>*/ ?>
							<p><li id="links"><a href="changepasswd" pageid="1" src="">更改密碼</a></li></p>
						</ul>
						</div>
					 <h5>下載資料</h5>
						<div>
						<ul>
							<p><li id="links"><a href="post_data_download" pageid="1" src="">102年度所有調查文件及文宣下載</a></li></p>
						</ul>
						</div>
					 <h5>上傳資料</h5>
						<div>
						<ul>
							<p><li id="links"><a href="up102seniorOne" pageid="1" src="">102學年入學新生名單</a></li></p>                  
						</ul>
						</div>
					<h5>101年_資料瀏覽與下載</h5>
						<div>
						<ul>
							<p><li id="links"><a href="101grade_lottery.pdf" pageid="1" src="">101學年度新生調查中獎名單</a></li></p>
							<p><li id="links"><a href="101par_lottery.pdf" pageid="1" src="">101學年度高二及專二家長調查中獎名單</a></li></p>
							<p><li id="links"><a href="101tutor_lottery.pdf" pageid="1" src="">101學年度高二及專二導師調查中獎名單</a></li></p>
							<p><li id="links"><a href="101soph_lottery.pdf" pageid="1" src="">101學年度高二及專二學生調查中獎名單</a></li></p>
							<p><li id="links"><a href="101_award documents.pdf" pageid="1" src="">101高二(專二)導師、學生調查敘獎公文</a></li></p>
						</ul>
						</div> 
					<h5>名單狀態修改</h5>
						<div>
						<ul>
							<p><li id="links"><a href="modify_102seniorOne" pageid="1" src="">102學年度入學新生名單狀態更改</a></li></p>                  
						</ul>
						</div>	
					<h5>查詢進行中調查名單與填寫狀況</h5>
                	<div id="Layer2">
                        <h4>102高一基本資料普查</h4>
                        <div>
                            <ul>
                                <p><li id="links"><a href="return_102seniorOne" pageid="1" src="">本校回收率</a></li></p>
                                <p><li id="links"><a href="nc_102seniorOne" pageid="1" src="">填答狀況</a></li></p>
                            </ul>
                        </div>
                        <h4>102高二基本資料普查</h4>
                        <div>
                            <ul>
                                <p><li id="links"><a href="return_102seniorOne" pageid="1" src="">本校回收率</a></li></p>
                                <p><li id="links"><a href="nc_102seniorOne" pageid="1" src="">填答狀況</a></li></p>
                            </ul>
                        </div>
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
							<div id='pageLoad'><? echo Session::get('sch_id'); ?></div>
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