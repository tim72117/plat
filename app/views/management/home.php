<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-TW" lang="zh-TW">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=10" />
<meta http-equiv="X-UA-Compatible" content="IE=9" />
<meta http-equiv="X-UA-Compatible" content="IE=8" />
<title>問卷平台</title>

<script type="text/javascript" src="<?=asset('js/jquery-1.10.2.min.js')?>"></script>
<!--[if lt IE 9]><script src="js/html5shiv.js"></script><![endif]-->

<link rel="stylesheet" href="<?=asset('css/onepcssgrid.css')?>" />
<link rel="stylesheet" href="<?=asset('css/management/share.css')?>" />
<link rel="stylesheet" href="<?=asset('css/management/share.index.css')?>" />
	

<style>
body {
	margin: 0;
	font-family: '微軟正黑體','Open Sans Condensed';
}

.register {
	background: #63bd2b;
	border: 1px solid #63bd2b;
	outline: 0;
}
.register:hover {
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#77d13f), color-stop(50%,#63bd2b), color-stop(100%,#63bd2b)); 
	cursor: pointer;
}

.head-pic {
	background-repeat: no-repeat;
	background-position: center;
	cursor: pointer;
	float: left;
}

		
		
</style>
<script>

</script>
</head>

	<body>
	

	
		<div class="onepcssgrid-1000" style="margin-top:0">
			<div class="onerow" style="border-top: 0px solid #bebebe;border-bottom: 0px solid #fff; background-color:#fff">
				<div class="colfull">
					<? echo $child_tab; ?>
				</div>
			</div>
			<div style="clear:both"></div>
		</div>
		

		
		<div class="onepcssgrid-full">			
			<div class="onerow">
				<div class="colfull" style="height:350px;background-color:#63bd2b;;margin-top:10px;padding:0;color:#fff;background-image: url(<?=asset('images/2444K.png')?>)"></div>				
			</div>
			<div style="clear:both"></div>
		</div>
			
		<div class="onepcssgrid-1000" style="margin-top:40px">
			<div class="onerow">
				<?=$child_main?>
			</div>
		</div>
		

		<div class="onepcssgrid-1000" style="margin-top:80px">
			<div class="onerow">
				<?=$child_footer?>
			</div>
		</div>
		


	</body>

</html>