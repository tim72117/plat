<!doctype html>

<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>資料管理平台</title>

	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<meta name="author" content="" />

	<meta property="og:title" content="" />
	<meta property="og:description" content="" />
	<meta property="og:image" content="" />
	<meta property="og:url" content="" />
	<meta property="og:site_name" content="" />

	<link rel="shortcut icon" href="favicon.ico" />

	<meta name="viewport" content="width=device-width"/>

	<!--[if lt IE 9]><script src="js/html5shiv.js"></script><![endif]-->

	<!--<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300" type="text/css">-->
	<link rel="stylesheet" href="../css/onepcssgrid.css" />
	<link rel="stylesheet" href="../css/management.share.css" />
	<link rel="stylesheet" href="../css/management.share.index.css" />
	
	<!--<link rel="stylesheet" href="coin-slider/coin-slider-styles.css" type="text/css" />
	<link rel="stylesheet" type="text/css" href="Parallax Content Slider/css/style.css" />
	<link rel="stylesheet" type="text/css" href="Parallax Content Slider/css/demo.css" />-->
	<!--<link rel="stylesheet" href="jmpress/css/style.css" />
	<link rel="stylesheet" type="text/css" href="jmpress/css/demo.css" />-->
	
	<script type="text/javascript" src="../js/jquery-1.9.1.min.js"></script>

	<!--
	<script type="text/javascript" src="jmpress/js/jmpress.min.js"></script>
	<script type="text/javascript" src="jmpress/js/jquery.jmslideshow.js"></script>
	<script type="text/javascript" src="jmpress/js/modernizr.custom.48780.js"></script>
	-->
	
	

	<style>
		body {
			margin: 0;
			background-color:#fff;
			height:100%;
			font-family: '微軟正黑體','Open Sans Condensed';
		}

		.col1, .col2, .col3, .col4, .col5, .col6, .col7, .col8, .col9, .col10, .col11, .col12 {
			/*background: #adcb47;*/
			color: #000;
			padding: 0;
		}

		@media all and (max-width: 768px) {
			.onerow {
				margin: 0 0 100px;
			}
			.onerow.top {
				margin: 0;
			}
			.onerow.banner,.separator {
				display:none !important;
			}
		}
		
		.inner-bolder-box {
			border-top: 1px solid #bebebe;			
			height:300px;
			color:#999;			
		}		
		
		.inner-bolder-box-boder {
			border: 1px solid #bebebe;			
			height:200px;
			color:#999;			
		}
		
		.inner-bolder-box-boder:hover {
			box-shadow: 1px 1px 20px 0 rgba(0,0,0,0.2);
			-webkit-box-shadow: 1px 1px 20px 0 rgba(0,0,0,0.2);		
		}
		
		.section {
			border: 1px solid #bebebe;
		}
		.section.b-bordder {
			border: 1px solid #000;
		}
		
				
@media all and (max-width: 768px) {
	li.tab {
		width: 48% !important;
		margin-left: 1% !important;
		border: 1px solid #bebebe !important;
	}	
	.to8 {
		float: left;
		margin: 0 3% 0 0;
		width: 48%;
	}
	.to8:nth-of-type(even) {
		margin:0 !important;
	}
}
		
button.search {
	background-color: #fff;
	border: 3px solid #fff;
	border-radius: 0px;	
	color: #63bd2b;
	outline: 0;
	box-sizing: border-box;
	font-family: '微軟正黑體';
	font-size: 15px;
	font-weight: bold;
}
button.search:hover {
	/*background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#fff), color-stop(50%,#1d75bb), color-stop(100%,#1d75bb)); */
	background-color: #63bd2b;
	-webkit-box-shadow: 0 0 2px 2px #63bd2b inset;		
	cursor: pointer;
	color: #fff;
}
button.search:active {
	box-shadow: none;
}
select.search_area {
	outline: 0;
}

li.button {
	background-color: #63bd2b;
	border: 1px solid transparent;
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

a.button {
	display: block;
	box-sizing: border-box;
	text-decoration: none;
}

.button.green {
	background: #63bd2b;
}
.button.green:hover {
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#77d13f), color-stop(50%,#63bd2b), color-stop(100%,#63bd2b));
}

		
		
	</style>
	<script>
	$(function() {
		$('div.onerow > div').each(function(){
			var width = $(this).width();
			var helfwidth = width/2;
			//$(this).append('<div style="position:absolute;color:#000;font-size:8px;background-color:rgba(255,255,255,0.5);margin-top:-5px;margin-left:'+helfwidth+'px">width:'+width+'</div>');
		});
	

		
		/*
		var aa = function(){
			_width = $('#testi img.active').width();
			_index = $('#testi img.active').index();
			_all = $('#testi img').length;
			_index_next = (_index+1)>=_all?0:_index+1;
			
			$('#testi img').eq(_index).removeClass('active');
			$('#testi img').eq(_index_next).addClass('active');
			
			
			console.log(_all+' '+_index+' '+_index_next);
			$('#testi img').eq(_index).stop().animate({
				left: '100%'//_width
			}).end().eq(_index_next).css('left', -_width).stop().animate({
				left: '0%'
			},function(){ setTimeout(function(){ aa() },5000); });
		};
		setTimeout(function(){ aa() },5000);
		$('#testi img:eq(0)').siblings('img').css({
			left: $(this).width()
			//width:$('#testi').width()+'px'
		});
		*/

		
		console.log($('#testi').width()+'px');
		
	});//rgba(93,197,27,0.8)
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
				<div class="colfull" style="height:80px;background-color:#63bd2b;;margin-top:10px;padding:0;color:#fff">
					<div class="onepcssgrid-1000">
						<div class="onerow">
							<div class="col3" style="height: 40px"></div>
							<div class="col9 last">
								<div style="margin:10px">
									<!--<select class="search_area" style="width:200px;height:40px;padding:10px"><option>選擇地區</option></select>
									<button class="search" style="width:80px;height:40px;text-align: center" title="搜尋">搜尋</button>-->
								</div>
							</div>
							<div style="clear:both"></div>
						</div>
						<div style="clear:both"></div>
					</div>
				</div>				
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