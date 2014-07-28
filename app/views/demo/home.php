<!DOCTYPE html>
<html xml:lang="zh-TW" lang="zh-TW">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=10" />
<meta http-equiv="X-UA-Compatible" content="IE=9" />
<meta http-equiv="X-UA-Compatible" content="IE=8" />
<title>資料查詢平台</title>

<script src="<?=asset('js/twcaseal_v3.js')?>"></script>
<!--[if lt IE 9]>
<script src="<?=asset('js/html5shiv.js')?>"></script>
<![endif]-->

<link rel="stylesheet" href="<?=asset('demo/use/css/use100.css')?>">
	
<script type = "text/javascript">

</script>
    
</head>
    
<body bgcolor="white" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" marginwidth="0" marginheight="0" class="intd">

<div align="center" style="margin:0 auto;font-size:18px;line-height: 60px;font-weight: bold">資料查詢平台</div>

<table cellpadding="3" cellspacing="2" border="0" width="50%" align="center">
	<tr>
	  <td class="header1" align="center" style="line-height: 40px;font-size:16px"><?=$title?></td>
	</tr>
	<tr>
	  <td class="intd2">
			<?=$context?>	
			
		  <div style="float:right;margin:10px">
			  <div id="twcaseal" class="SMALL"><img src="<?=asset('images/twca.gif')?>"/></div>
		  </div>
	  </td>	
	</tr>
    
</table>

		
        
<?=$child_footer?>

</body>
</html>