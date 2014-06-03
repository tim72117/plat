<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-TW" lang="zh-TW">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=10" />
<meta http-equiv="X-UA-Compatible" content="IE=9" />
<meta http-equiv="X-UA-Compatible" content="IE=8" />
<title>後期中等教育資料庫資料查詢平台</title>

<script type="text/javascript" src="//ssllogo.twca.com.tw/twcaseal_v3.js" charset="utf-8"></script>

<link href="<?=asset('demo/use/css/use100.css')?>" rel="stylesheet" type="text/css">
	

<script type = "text/javascript">

</script>



</head>
	
<body bottommargin="0" topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0" bgcolor="white">
<p align="center"><img src="<?=asset('demo/use/images/title.jpg')?>" width="558" height="114"></p>

<body bgcolor="white" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" marginwidth="0" marginheight="0" class="intd">
<table cellpadding="3" cellspacing="1" border="1" width="50%" align="center">
	<tr>
	  <td class="header1" align="center"><?=$title?></td>
	</tr>
	<tr>
	  <td class="intd">
		  <?=$context?>		  

	  </td>	
	</tr>
    <tr>
	    <td>      
		  <div style="float:right;margin:10px">
			  <div id="twcaseal" class="SMALL"><img src="<?=asset('images/twca.gif')?>"/></div>
		  </div>
			
        	<p align="right">
				<?
					echo link_to('user/auth/use', '登入');
					echo ' \ ';
					if( $contextFile=='login' )
						echo link_to('user/auth/register/use', '帳號申請');
				?>
			\ <a href="isms_sel_acc" target="_blank">帳號註銷</a> <br>
        	<a href="AccountApplyProcess.pdf" target="_blank">帳號申請說明</a> \ <a href="AccountCancelProcess.pdf" target="_blank">帳號註銷說明</a><br>
        	<a href="accountQA.pdf" target="_blank">帳號申請Q&amp;A</a> </p>       
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
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-3887306-1");
pageTracker._initData();
pageTracker._trackPageview();
</script>
</body>
</html>