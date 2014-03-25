<div class="col12">	
	
	<form action="register" method="post">
	<div style="width:400px;margin: 0 auto">
		<div>註冊帳號</div>
		<?=Form::text('username', Input::old('username',''), array('placeholder' => '帳號','class' => 'register-block'))?>
		<?=Form::password('password', array('placeholder' => '密碼','class' => 'register-block'));?>
		<?=Form::password('password_confirmation', array('placeholder' => '確認密碼','class' => 'register-block'));?>
		<div style="width:300px;height: 40px;font-size: 15px;padding: 10px;box-sizing: border-box;margin-top:5px">
			<input type="checkbox" name="agree" value="1" id="agree-contract" /><label for="agree-contract">我同意 服務條款、隱私政策</label>
		</div>
		<div style="width:300px;font-size: 15px;padding: 10px;box-sizing: border-box;margin-top:5px;color:#d14836">
			<?
				$errorMessage = array(
					$errors->first('username','帳號:message'),
					$errors->first('password','密碼:message'),
					$errors->first('password_confirmation','確認密碼:message'),
					$errors->first('agree','同意 服務條款、隱私政策:message')
				);
				echo implode('、',array_filter($errorMessage));
			?>
		</div>
		<input type="hidden" name="_token1" value="<?=csrf_token()?>" />
		<input type="hidden" name="_token2" value="<?=dddos_token()?>" />
		<button class="register" style="width:300px;height:40px;margin:20px 0 0 0;padding:10px;text-align: center;font-size:15px;color:#fff" title="註冊">註冊</button>
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