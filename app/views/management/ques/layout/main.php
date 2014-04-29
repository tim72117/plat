<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Question Management</title>

<!--[if lt IE 9]><script src="js/html5shiv.js"></script><![endif]-->
<script type="text/javascript" src="<?=asset('js/jquery-1.10.2.min.js')?>"></script>
<script type="text/javascript" src="<?=asset('js/jquery-ui-1.10.3.custom.min.js')?>"></script>
<script type="text/javascript" src="<?=asset('js/Highcharts-3.0.7/js/highcharts.js')?>"></script>

<link rel="stylesheet" href="<?=asset('js/smoothness/jquery-ui-1.10.3.custom.min.css')?>" />
<link rel="stylesheet" href="<?=asset('css/onepcssgrid.css')?>" />
<link rel="stylesheet" href="<?=asset('css/management/share.css')?>" />
<link rel="stylesheet" href="<?=asset('css/management/share.index.css')?>" />

<script type="text/javascript">
$(document).ready(function(){

	$('body').on('click', 'input.solve', function(){
		event.preventDefault();
		var input = $(this);
		$.getJSON('report_solve', {id:$(this).val(), checked:$(this).is(':checked')}, function(data){			
			if( data!=='' ){				
				input.prop('checked', data);
			}
		}).error(function(e){
			console.log(e);
		});
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
tr:hover {
	background-color: #eee;
}
</style>
</head>

<body>

<div class="onepcssgrid-1000" style="margin-top:0">
	<div class="onerow" style="border-top: 0px solid #bebebe;border-bottom: 0px solid #fff; background-color:#fff">
		<div class="colfull">
			<?=$child_tab?>
		</div>
	</div>
	<div style="clear:both"></div>
</div>

<table>
	<thead>
		<th width="200">時間</th>
		<th width="300">聯絡方法</th>
		<th>問題回報</th>
		<th width="50">已解決</th>
		<th width="400">瀏覽器</th>		
	</thead>
	<tbody><?=$reports?></tbody>
</table>

	<div class="message"></div>

</body>
</html>