<?
##########################################################################################
#
# filename: changepasswd.php
# function: 更改校級密碼。
#
# 維護者  : 蕭聖哲
# 維護日期: 2011/5/5
#
##########################################################################################
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script>
$('form').submit(function(evt) {
	
		if ($('input[name="newpasswd1"]').val()!=$('input[name="newpasswd2"]').val()){
		    alert ("新密碼兩次輸入不一樣!請您重新輸入!");	
			$('input[name="passwd"]').val("");
			$('input[name="newpasswd1"]').val("");
			$('input[name="newpasswd2"]').val("");		
			$('input[name="passwd"]').focus();
		    return false;
		}else if ($('input[name="newpasswd1"]').val().length<8){
		    alert ("新密碼小於八碼!請您重新輸入!");
			$('input[name="passwd"]').val("");
			$('input[name="newpasswd1"]').val("");
			$('input[name="newpasswd2"]').val("");		
			$('input[name="passwd"]').focus();
		    return false;
		}else if ($('input[name="newpasswd2"]').val().length<8){
		    alert ("新密碼小於八碼!請您重新輸入!");
			$('input[name="passwd"]').val("");
			$('input[name="newpasswd1"]').val("");
			$('input[name="newpasswd2"]').val("");		
			$('input[name="passwd"]').focus();
		    return false;
		}
	$.ajax({
	  url: "pchangepasswd",
	  type: "GET",
	  data: {'passwd':$('input[name=passwd]').val(),
			'newpasswd1':$('input[name=newpasswd1]').val(),
			'newpasswd2':$('input[name=newpasswd2]').val()},
	  dataType: "json",
	  success: function(Jdata) {
		if(Jdata.value =='1'){alert("修改成功");}
		else if(Jdata.value =='2'){alert("您輸入的舊密碼有誤!請您重新輸入!");}
		else if(Jdata.value =='3'){alert("密碼強度稍弱!需包含英數字，至少八碼。");}
		$('input[name="passwd"]').val("");
		$('input[name="newpasswd1"]').val("");
		$('input[name="newpasswd2"]').val("");
	  },	  
	  error: function(e) {
		alert("網路發生錯誤，請通報管理者。(02)7734-3658");
	  }
	});
	evt.preventDefault();
});
</script>
<title>修改校級聯絡人密碼</title>
</head>

<body bottommargin="0" topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0" bgcolor="white">
<table cellpadding="3" cellspacing="1" border="0" width="100%">
	<tr>
	  <td class="header2">&nbsp;修改聯絡人密碼</td>
	</tr>
</table>
<form action="auth/password/change" method="post">
<input type="hidden" name="_token1" value="<?=csrf_token()?>" />
<input type="hidden" name="_token2" value="<?=dddos_token()?>" />
<table id="editschool" width="30%" align="center" style="display:inline" border="1">
	<tr bgcolor="#FFFFCC"><td colspan="2" align="center">修改聯絡人密碼</td>
  </tr>
			
	<tr>
        <td>輸入舊密碼</td>
		<td><input name="passwordold" type="password" size="20" maxlength="20" autocomplete="off" ></td>
	</tr>
    <tr>
        <td>輸入新密碼</td>
		<td><input type="password" name="password" size="20" maxlength="20" autocomplete="off" ></td>
	</tr>
    <tr>
        <td>確認新密碼</td>
		<td><input type="password" name="password_confirmation" size="20" maxlength="20" autocomplete="off" ></td>
	</tr>
    <tr><td colspan="2" align="center"><input type="submit" name="Submit" value="送出"></td></tr>    
</table>
</form>
<br><br>

			<?
				if( isset($dddos_error) && $dddos_error )
					echo '登入次數過多,請等待30秒後再進行登入';
				if( isset($csrf_error) && $csrf_error )
					echo '畫面過期，請重新登入';
				echo implode('、',array_filter($errors->all()));
			?>

</body>
</html>