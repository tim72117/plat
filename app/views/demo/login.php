<form action="login" method="post">
	<table style="width:300px;margin:0 auto">
		<tr>
			<th>電子郵件信箱</th>
			<td><?=Form::text('email', Input::old('email',''), array('placeholder' => '電子郵件信箱','class' => 'register-block'))?></td>
		</tr>
		<tr>
			<th>密碼</th>
			<td><?=Form::password('password', array('placeholder' => '密碼','class' => 'register-block'))?></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;color:#f00">
			<?
				if( isset($dddos_error) && $dddos_error )
					echo '登入次數過多,請等待30秒後再進行登入';
				if( isset($csrf_error) && $csrf_error )
					echo '畫面過期，請重新登入';
				echo implode('、',array_filter($errors->all()));
			?>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center"><input type="submit" value="送出"></td>
		</tr>	
	</table>
	<input type="hidden" name="_token1" value="<?=csrf_token()?>" />
	<input type="hidden" name="_token2" value="<?=dddos_token()?>" />
	<input type="hidden" name="project" value="<?=$project?>" />
</form>