<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-TW" lang="zh-TW">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=10" />
<meta http-equiv="X-UA-Compatible" content="IE=9" />
<meta http-equiv="X-UA-Compatible" content="IE=8" />
<title>師資培育資料庫資料查詢平台</title>

<!--<script type="text/javascript" src="//ssllogo.twca.com.tw/twcaseal_v3.js" charset="utf-8"></script>-->

<link href="<?=asset('demo/use/css/use100.css')?>" rel="stylesheet" type="text/css">
	
<script type = "text/javascript">

</script>
    
</head>
    
<body bgcolor="white" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" marginwidth="0" marginheight="0" class="intd">

<div align="center" style="margin:0 auto;font-size:18px;line-height: 60px;font-weight: bold">師資培育資料庫資料查詢平台</div>

<table cellpadding="3" cellspacing="2" border="0" width="50%" align="center">
	<tr>
	  <td class="header1" align="center" style="line-height: 40px;font-size:16px"><?=$title?></td>
	</tr>
	<tr>
	  <td class="intd2">
			<?=$context?>	
		  	
        	
			<?
			echo '<p align="center">';
			if( $contextFile!='remind' && $contextFile!='register' )
				echo link_to('user/auth/password/remind', '忘記密碼');
			echo '</p>';
			echo '<p align="center">';
				if( $contextFile!='login' )
					echo link_to('user/auth/use', '登入');

				if( $contextFile=='login' ){
					echo link_to('user/auth/register/tted', '帳號申請');
				}
			echo '</p>';
			?>
			<!--\ <a href="isms_sel_acc" target="_blank">帳號註銷</a> <br>
        	<a href="AccountApplyProcess.pdf" target="_blank">帳號申請說明</a> \ <a href="AccountCancelProcess.pdf" target="_blank">帳號註銷說明</a><br>
        	<a href="accountQA.pdf" target="_blank">帳號申請Q&amp;A</a>--> 
			
		  <div style="float:right;margin:10px">
			  <div id="twcaseal" class="SMALL"><img src="<?=asset('images/twca.gif')?>"/></div>
		  </div>
	  </td>	
	</tr>
    <tr>
	    <td>      

 
        </td>
</tr>
	<tr>
	  <td class="intd">
		您於&nbsp;<b><?=Session::get('now')?></b>&nbsp;&nbsp;從&nbsp;<b><?=Session::get('ip'); ?></b>&nbsp;連結至本網站。
      </td>
	</tr>
	<?=@$news?>
    
</table>

		
        
<?=$child_footer?>

</body>
</html>