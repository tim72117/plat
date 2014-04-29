<form action="login" method="post">
	
  <p align="center">
	帳號：<?=Form::text('username', Input::old('username',''), array('placeholder' => '帳號','class' => 'register-block'))?><br />
	密碼：<?=Form::password('password', array('placeholder' => '密碼','class' => 'register-block'))?><br />
	<input type="hidden" name="_token1" value="<?=csrf_token()?>" />
	<input type="hidden" name="_token2" value="<?=dddos_token()?>" />
	<input type="submit" name="Submit" value="送出">
  </p>

</form>

	<?
		if( isset($dddos_error) && $dddos_error )
			echo '登入次數過多,請等待30秒後再進行登入';
		if( isset($csrf_error) && $csrf_error )
			echo '畫面過期，請重新登入';
		echo implode('、',array_filter($errors->all()));
	?>

<p align="right"><a href="password/remind" target="_self">忘記密碼</a></p>