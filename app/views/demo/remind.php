<?=Form::open(array('url' => action('UserController@remind', array($project)), 'method' => 'post'))?>
    <input type="hidden" name="_token2" value="<?=dddos_token()?>" />
	<table style="width:500px;margin:0 auto">
		<tr>
			<th>電子郵件信箱</th>
			<td><input type="text" name="email" size="55" class="register-block"></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;color:#f00"><?=implode('、',array_filter($errors->all()))?></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center"><input type="submit" value="送出"></td>
		</tr>	
	</table>
<?=Form::close()?>