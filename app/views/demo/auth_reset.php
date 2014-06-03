<form action="" method="post">
	<table style="width:300px;margin:0 auto">
		<tr>
			<th>電子郵件信箱</th>
			<td><input type="text" name="email"></td>
		</tr>
		<tr>
			<th>密碼</th>
			<td><input type="password" name="password"></td>
		</tr>
		<tr>
			<th>確認密碼</th>
			<td><input type="password" name="password_confirmation"></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;color:#f00"><?=implode('、',array_filter($errors->all()))?></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center"><input type="submit" value="送出"></td>
		</tr>

	</table>	
</form>