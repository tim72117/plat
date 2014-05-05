<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>變更進入學校</title>
<script language="JavaScript" src="tigra_tables.js"></script>
<style>

	.header1, h1
		{color: #ffffff; background: #B69866; font-weight: bold; font-family: Tahoma, Verdana; font-size: 13px; margin: 0px; padding: 3px;}
	.header2, h2
		{color: #000000; background: #B69866; font-weight: bold; font-family: Tahoma, Verdana; font-size: 12px;}

</style>
</head>

<body bottommargin="0" topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0" bgcolor="white">
<table cellpadding="3" cellspacing="1" border="0" width="100%" align="center">
	<tr>
	  <td class="header2">&nbsp;登入資訊</td>
	</tr>
	<tr>
		<td class="intd">
		  <p>您目前登入的帳號為&nbsp;
		  	<font color="red">
		  		<?
		  			/*$account = $_SESSION['account']; 
		  			echo $account; */
		  		?>
		  	</font>，您好。
		  </p>
	</tr>
	<tr>
	  <td class="header2">&nbsp;變更進入學校</td>
	</tr>
	<tr><td><p>&nbsp;</p></td></tr>
	<tr><td><p>&nbsp;</p></td></tr>
	<tr>
		<td>
			<table id="depart" width="75%" align="center" border="1" style="display:inline">
				<tr>
					<td class="header1" align="center">&nbsp;<span class="style1">變更進入學校</span></td>
				</tr>
				<tr>
					<td>
							<form name="form2" method="post" action="changschid.php">
								<p align="center" class="style3 style5">請選擇學校，並按送出</p>
								<p align="center">
								<select name="uid" id="uid">
								<option value='0'>------------------------------</option>
<?php
								
$users = DB::table('ques_admin.dbo.pub_uschool')->get();

foreach ($users as $user)
{
$a=$user->uid;
$b=$user->uname;
    // echo "<option value='$user->uid'>$user->uname</option>";
	echo "<option value='$a'>$b</option>";
	//var_dump($user->uname);
	
}
								?>
							  </select>
							  <input type="submit" name="Submit2" value="送出">
							  </p>
							</form>
					</td>
				</tr>
			</table>
		</td>
	</tr>

</table>


</body>
</html>
