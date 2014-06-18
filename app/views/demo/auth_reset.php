<?=Form::open(array('url' => action('UserController@reset', array($token)), 'method' => 'post'))?>
    <input type="hidden" name="_token2" value="<?=dddos_token()?>" />
	<table style="width:300px;margin:0 auto">
		<tr>
			<th>電子郵件信箱</th>
			<td><input type="text" name="email"></td>
		</tr>
		<tr>
			<th>新密碼</th>
			<td><input type="password" name="password"></td>
		</tr>
		<tr>
			<th>確認新密碼</th>
			<td><input type="password" name="password_confirmation"></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;color:#f00"><?=implode('、',array_filter($errors->all()))?></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center"><input type="submit" value="送出"></td>
		</tr>

	</table>	
<?=Form::close()?>