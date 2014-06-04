<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>後期中等教育資料庫查詢平台</title>

<script type="text/javascript" src="<?=asset('js/tigra_tables.js')?>"></script>
<script type="text/javascript" src="<?=asset('js/jquery-1.10.2.min.js')?>"></script>
<script type="text/javascript" src="<?=asset('js/jeditable.js')?>"></script>
<script type="text/javascript" src="<?=asset('js/jquery-ui-1.10.3.custom.min.js')?>"></script>
<!--[if lt IE 9]><script src="js/html5shiv.js"></script><![endif]-->

<link href="css/pub.css" rel="stylesheet" type="text/css">
<link href="<?=asset('js/jquery-ui-1.10.3.custom.min.css')?>" rel="stylesheet" type="text/css" />
<link href="<?=asset('demo/use/css/use100_content.css')?>" rel="stylesheet" type="text/css" />

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



</head>

<body>

<div style="width: 100%;height: 100%;max-height:100%">

	<div style="width:100%;height: 110px;position: absolute;z-index:10;background-color: #fff">
		<div style="background-color: #ffffff;width:100%;height:80px"><img src="<?=asset('demo/use/images/title.jpg')?>" width="500" height="80"></div>
		<div style="background-color: #458A00;width:100%;height:30px;line-height: 30px;border-bottom: 1px solid #ddd;color:#fff" align="right">
			<a href="<?=URL::to('page/project')?>" style="margin-right:10px" class="login-bar">回首頁</a>
			<a href="<?=URL::to('user/auth/password/change')?>" style="margin-right:10px" class="login-bar">更改密碼</a>
	    	<a href="<?=URL::to('user/auth/logout')?>" style="margin-right:10px" class="login-bar">登出</a>
        </div>
	</div>
	
	<div class="border-box" style="height:100%;width:100%;background-color: #fff;padding-top:110px">
		
		<div style="height:100%;overflow-y: hidden;float:left">
			<div style="width: 350px;height:100%;background-color: #fff;border-right: 1px solid #ddd;overflow-y: auto;margin-top:0">


				<div id="Layer4">
				<h2>【 後期中等教育資料庫查詢平台 】</h2>
				<?
				$user = Auth::user();
				$packageDocs = $user->get_file_provider()->lists();

				foreach($packageDocs as $packageDoc){
					foreach($packageDoc['actives'] as $active){		

						if( $active['active']=='open' ){
							echo '<div class="inbox" style="clear:both;overflow: hidden;cursor:default;margin-top:10px">';
							echo '<div class="count button" folder="" style="font-size:16px;text-decoration: none;float:left;margin-left:10px">';
							//echo '<div class="intent button" intent_key="'.$active['intent_key'].'">'.$active['active'].'</div>';
							echo '<a href="'.URL::to('user/doc/'.$active['intent_key']).'">'.$packageDoc['title'].'</a>';
							echo '</div>';
							echo '</div>';
						}

					}
				}

				?>
				</div>
				
			</div>
		</div>

		<div style="height: 100%;overflow-y: hidden;margin-left: 200px">
			<div style="height: 100%;overflow: auto;background-color: #fff;font-size:14px;text-align: left">
				
				<table style="width:100%" cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<div id='pageLoad'><? //echo Session::get('sch_id'); ?></div>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td width="1000px"><?=$context?></td>
							<td style="vertical-align:top"><?=$request?></td>
						</tr>					
					</tbody>
				</table>
			</div>			
		</div>
		
	</div>
	
</div>	

</body>
</html>