<div class="col12">	
	
	<form action="loginAuth" method="post">
	<div style="width:400px;margin: 0 auto">
		<div>登入</div>
		<?=Form::text('username', Input::old('username',''), array('placeholder' => '帳號','class' => 'register-block'));?>
		<?//=Form::password('password', array('placeholder' => '密碼test','class' => 'register-block'));?>
		<?=Form::password('password', array('placeholder' => '密碼','class' => 'register-block'));?>
		<div style="width:300px;font-size: 15px;padding: 10px;box-sizing: border-box;margin-top:5px;color:#d14836">
			<?
				if( isset($dddos_error) && $dddos_error )
					echo '登入次數過多,請等待30秒後再進行登入';
				if( isset($csrf_error) && $csrf_error )
					echo '畫面過期，請重新登入';
				echo implode('、',array_filter($errors->all()));
			?>
		</div>
		<input type="hidden" name="_token1" value="<?=csrf_token()?>" />
		<input type="hidden" name="_token2" value="<?=dddos_token()?>" />
		<button class="register" style="width:300px;height:40px;margin:20px 0 0 0;padding:10px;text-align: center;font-size:15px;color:#fff" title="登入">登入</button>
	</div>
	</form>	
	
	
</div>
<div style="clear:both"></div>

<style>
.register-block {
	width: 300px;
	height: 40px;
	font-size: 15px;
	padding: 10px;
	box-sizing: border-box;
	margin-top: 5px;
}		
</style>