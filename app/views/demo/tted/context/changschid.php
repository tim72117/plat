<?php
header("Content-Type:text/html; charset=utf-8");
##########################################################################################
#
# filename: changschid.php
# function: 變更進入學校
#
# 維護者  : 黃光隆
# 維護日期: 2012/01/09
#
##########################################################################################
	

$connectionInfo = array( "Database"=>"ques_admin", "UID"=>"ques_rw", "PWD"=>"edulyw@928");
$conn = sqlsrv_connect( '192.168.11.2', $connectionInfo);

if( $conn ) {
     echo "Connection established.<br />";
}else{
     echo "Connection could not be established.<br />";
     die( print_r( sqlsrv_errors(), true));
}

//////////////////////////////////////////

/*
$q_string = "SELECT u.newcid, s.[uname]
      ,s.[uid]
      ,[udepname]
      ,[udepcode]
      ,[stdname]
	  ,[stdemail]
      ,u.[tel]
      ,[stdregzipcode]
      ,[stdregaddr]
      ,[page]
	  ,u.newcid
  FROM	[tted_edu_102].[dbo].[newedu101_userinfo] u left join [tted_edu_102].[dbo].[newedu101_pstat] p
		ON (u.[newcid] = p.[newcid])
		left join [tted_public].[dbo].[pub_uschool] s
		ON (s.[uid]=u.[uid])
  where s.year='102' and u.pstat='0'
  order by s.[uid],u.[udepname];";
  
$stmt = sqlsrv_query( $conn, $q_string);


while ($obj_all = $objects = sqlsrv_fetch_object($stmt)){
		
	echo "<tr>";
	echo "<td scope=col>".$obj_all->newcid."</td>\n";
	echo "<td scope=col>".$obj_all->uname."</td>\n";
	echo "<td scope=col>".$obj_all->uid."</td>\n";
	echo "<td scope=col>".$obj_all->udepname."</td>\n";
	echo "<td scope=col>".$obj_all->udepcode."</td>\n";
	echo "<td scope=col>".$obj_all->stdname."</td>\n";
	echo "<td scope=col>".$obj_all->tel."</td>\n";
	echo "<td scope=col>".$obj_all->stdemail."</td>\n";
	echo "<td scope=col>".$obj_all->stdregzipcode."</td>\n";
	echo "<td scope=col>".$obj_all->stdregaddr."</td>\n";
	echo "<td scope=col>".$obj_all->page."</td>\n";
}*/
/////////////////////////////////////////

$results = DB::select('select uid,uname from ques_admin.dbo.pub_uschool where year = 102', array());
echo $results[5]->uname;
$users = DB::table('ques_admin.dbo.pub_uschool')->select('uid', 'uname')->get();
//echo $user->get();
//echo $user->uname;

//echo $results[0]->uname;
//echo "<option value='$results[0]->uname'></option>";
//echo $results[all]->uid;

//$results = DB::select('select uname from ques_admin.dbo.pub_uschool where year = 102', array());
//echo $results[0]->uname;

/*$user = DB::table('tted_edu_102.pub_uschool')->where('uid', '0001')->first();
echo $user;*/
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>變更進入學校</title>
<script language="JavaScript" src="tigra_tables.js"></script>
<style>
	a, A:link, a:visited, a:active
		{color: #0000aa; text-decoration: none; font-family: Tahoma, Verdana; font-size: 11px}
	A:hover
		{color: #ff0000; text-decoration: none; font-family: Tahoma, Verdana; font-size: 11px}
	p, tr, td, ul, li
		{color: #000000; font-family: Tahoma, Verdana; font-size: 11px}
	.header1, h1
		{color: #ffffff; background: #B69866; font-weight: bold; font-family: Tahoma, Verdana; font-size: 13px; margin: 0px; padding: 3px;}
	.header2, h2
		{color: #000000; background: #B69866; font-weight: bold; font-family: Tahoma, Verdana; font-size: 12px;}
	.intd
		{color: #000000; font-family: Tahoma, Verdana; font-size: 11px; padding-left: 15px;}
.style1 {font-size: 16px}
.style2 {font-size: 24px}
.style3 {
	color: #000000;
	font-size: 14px;
}
.style5 {font-size: 11px}
.style9 {font-size: 16px; font-family: "新細明體"; }
.style12 {font-size: 16px; font-family: "新細明體"; color: #0033FF; }
.style15 {color: #FFFFFF}
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
								
$connectionInfo = array( "Database"=>"ques_admin", "UID"=>"ques_rw", "PWD"=>"edulyw@928");
$conn = sqlsrv_connect( '192.168.11.2', $connectionInfo);

$results = DB::select('select uid,uname from ques_admin.dbo.pub_uschool where year = 102', array());


//echo $results[0]->uname;
//echo "<option value='$results[0]->uname'></option>";
//echo $results[1]->uid;
//echo "____";
//echo $results[1]->uname;



$users = DB::table('ques_admin.dbo.pub_uschool')->get();

foreach ($users as $user)
{
$a=$user->uid;
$b=$user->uname;
   // echo "<option value='$user->uid'>$user->uname</option>";
	echo "<option value='$a'>$b</option>";
	var_dump($user->uname);
	
}


//echo "<option value='$results[1]->uid'>$results[1]->uname</option>";
//echo "<option value='aa'>bb</option>";
								//while ($obj_all = $sql->objects('',$obj_query)){
											
								//			echo "<option value='$obj_all->uid'>$obj_all->uname($obj_all->uid)</option>";
						//	}		
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
