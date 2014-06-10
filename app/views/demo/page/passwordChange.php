
<form action="<?=URL::to('user/auth/password/change')?>" method="post">
<input type="hidden" name="_token1" value="<?=csrf_token()?>" />
<input type="hidden" name="_token2" value="<?=dddos_token()?>" />
<table id="editschool" width="350px" align="left" style="background-color: #fff;border: 1px solid #aaa;text-align: center;margin-left: 20px" border="0">
	<tr bgcolor="#fff">
		<td colspan="2" align="center" style="padding:20px">修改聯絡人密碼</td>
	</tr>			
	<tr>
        <td width="120">輸入舊密碼</td>
		<td><input type="password" name="passwordold" size="20" maxlength="20" autocomplete="off" ></td>
	</tr>
    <tr>
        <td>輸入新密碼</td>
		<td><input type="password" name="password" size="20" maxlength="20" autocomplete="off" ></td>
	</tr>
    <tr>
        <td>確認新密碼</td>
		<td><input type="password" name="password_confirmation" size="20" maxlength="20" autocomplete="off" ></td>
	</tr>
    <tr><td colspan="2" align="center" height="50"><input type="submit" name="Submit" value="送出"></td></tr>    
</table>
</form>

<?
	if( isset($dddos_error) && $dddos_error )
		echo '嘗試次數過多,請等待30秒後再進行更改';
	if( isset($csrf_error) && $csrf_error )
		echo '畫面過期，請重新登入';
	echo implode('、',array_filter($errors->all()));
?>

