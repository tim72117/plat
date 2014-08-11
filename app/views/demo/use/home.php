<!DOCTYPE html>
<html xmlns:ng="http://angularjs.org" xml:lang="zh-TW" lang="zh-TW">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=10" />
<meta http-equiv="X-UA-Compatible" content="IE=9" />
<meta http-equiv="X-UA-Compatible" content="IE=8" />
<title>後期中等教育資料庫資料查詢平台</title>

<script src="<?=asset('js/twcaseal_v3.js')?>"></script>
<script src="<?=asset('js/angular.min.js')?>"></script>
<!--[if lt IE 9]><script src="<?=asset('js/html5shiv.js')?>"></script><![endif]-->

<link rel="stylesheet" href="<?=asset('demo/use/css/use100.css')?>" />

</head>

<body bgcolor="white" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" marginwidth="0" marginheight="0" class="intd">
    
<p align="center"><img src="<?=asset('demo/use/images/title.jpg')?>" width="558" height="114"></p>

<table cellpadding="3" cellspacing="2" border="0" width="" align="center">
	<tr>
        <td class="header1" align="center" style="height: 30px;line-height: 30px;background-color: #79BA7A">
            <span style="padding:0 5px 0 5px;cursor: pointer;float:right;background-color: #599A5A;width:60px"><?=link_to('user/auth/use', '登入', array('style'=>'color:#fff'))?></span>
      </td>
	</tr>
	<tr>
        <td align="center" style="height: 60px;line-height: 60px;font-size:24px"><?=$title?></td>
	</tr>
	<tr>
	  <td>
			<?=$context?>	
		  	
        	
			<?
			echo '<p align="center">';
			if( $contextFile!='remind' && $contextFile!='register' )
				echo link_to('auth/password/remind/use', '忘記密碼');
			echo '</p>';
			echo '<p align="center">';

			if( $contextFile=='login' ){
				echo link_to('user/auth/register/use', '帳號申請');
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

	<?=@$news?>
    
</table>

		
        
<?=$child_footer?>
</body>
</html>