<?php

/*	session_start();
  	if (!($_SESSION['Login'])) {
           //如果未登入,則顯示登入畫面
           header("Location: ../../index.php");
  	}
	$user=$_SESSION['sname'];
	$sch_id=$_SESSION['sch_id100'];//學校代號
    include("/home/leon/data/edu/config/use_102/setting.inc.php"); 
	 	
		$sql1 = new mod_db();
*/		
$sch_id = '0004';

$obj1= DB::table('tted_edu_102.dbo.fieldwork102_公私立回收率')
			->where('uid','=',$sch_id)
			->select('uid','uname','totalnum','returnnum','return_percent')
			->get();
			
/*		$obj1=$sql1->objects("SELECT [uid],[uname],[totalnum],[returnnum],[return_percent] 
							FROM [tted_edu_102].[dbo].[fieldwork102_公私立回收率] where uid='$sch_id'");
*/		
/*
$obj2= DB::table('tted_edu_102.dbo.fieldwork102_公私立回收率')
			->where('uid','=',$sch_id)
			->select('count(s.newcid) as cy_srrturn','uname','totalnum','returnnum','return_percent')
			->get();
/*					
		$obj2=$sql1->objects("SELECT count(s.newcid) as cy_srrturn,(SELECT COUNT(newcid) FROM [tted_edu_102].[dbo].[fieldwork102_userinfo] where stdname is not null) as cy_total
								FROM [tted_edu_102].[dbo].[fieldwork102_userinfo] s left join [tted_edu_102].[dbo].[fieldwork102_pstat] p on s.newcid=p.newcid where p.page >='8'");
*/

$obj3= DB::table('tted_edu_102.dbo.graduation102_公私立回收率')
			->where('uid','=',$sch_id)
			->select('uid','uname','totalnum','returnnum','return_percent')
			->get();
/*				
		$obj3=$sql1->objects("SELECT [uid],[uname],[totalnum],[returnnum],[return_percent] 
							FROM [tted_edu_102].[dbo].[graduation102_公私立回收率] where uid='$sch_id'");
*/		
		
/*		$obj4=$sql1->objects("SELECT count(s.newcid) as cy_srrturn,(SELECT COUNT(newcid) FROM [tted_edu_102].[dbo].[graduation102_userinfo] where stdname is not null) as cy_total
								FROM [tted_edu_102].[dbo].[graduation102_userinfo] s left join [tted_edu_102].[dbo].[graduation102_pstat] p on s.newcid=p.newcid where p.page >='16'");
*/
$obj5= DB::table('tted_edu_102.dbo.newedu101_公私立回收率')
			->where('uid','=',$sch_id)
			->select('uid','uname','totalnum','returnnum','return_percent')
			->get();								

/*		$obj5=$sql1->objects("SELECT [uid],[uname],[totalnum],[returnnum],[return_percent] 
							FROM [tted_edu_102].[dbo].[newedu101_公私立回收率] where uid='$sch_id'");
		
/*		
		$obj6=$sql1->objects("SELECT count(s.newcid) as cy_srrturn,(SELECT COUNT(newcid) FROM [tted_edu_102].[dbo].[newedu101_userinfo] where stdname is not null) as cy_total
								FROM [tted_edu_102].[dbo].[newedu101_userinfo] s left join [tted_edu_102].[dbo].[newedu101_pstat] p on s.newcid=p.newcid where p.page >=9");
		$sql1->disconnect();
*/
		
foreach ($obj1 as $obj1){
	//fieldwork學校回收率
		$fieldwork_totalnum = $obj1->totalnum;//總量
		$fieldwork_returnnum = $obj1->returnnum;//回填量
		$fieldwork_return_percent = round(($fieldwork_returnnum/$fieldwork_totalnum)*100,2); //回收率
}
/*
foreach ($obj2 as $obj2){
	//fieldwork學校回收率
	//fieldwork全國回收率	
		$fieldwork_cy_total = $obj2->cy_total;
		$fieldwork_cy_sreturn = $obj2->cy_srrturn;
		$fieldwork_cy_return_percent = round(($fieldwork_cy_sreturn/$fieldwork_cy_total)*100,2);//全國回收率
}
*/
foreach ($obj3 as $obj3){
	//graduation102學校回收率
		$graduation102_totalnum = $obj3->totalnum;//總量
		$graduation102_returnnum = $obj3->returnnum;//回填量
		$graduation102_return_percent = round(($graduation102_returnnum/$graduation102_totalnum)*100,2); //回收率
}
/*
foreach ($obj4 as $obj4){
	//graduation102全國回收率	
		$graduation102_cy_total = $obj4->cy_total;
		$graduation102_cy_sreturn = $obj4->cy_srrturn;
		$graduation102_cy_return_percent = round(($graduation102_cy_sreturn/$graduation102_cy_total)*100,2);//全國回收率
}
*/
foreach ($obj5 as $obj5){
	//newedu101學校回收率
		$newedu101_totalnum = $obj5->totalnum;//總量
		$newedu101_returnnum = $obj5->returnnum;//回填量
		$newedu101_return_percent = round(($newedu101_returnnum/$newedu101_totalnum)*100,2); //回收率
}
/*
foreach ($obj5 as $obj6){
	//newedu101全國回收率	
		$newedu101_cy_total = $obj6->cy_total;
		$newedu101_cy_sreturn = $obj6->cy_srrturn;
		$newedu101_cy_return_percent = round(($newedu101_cy_sreturn/$newedu101_cy_total)*100,2);//全國回收率
}
*/		
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>查詢102學年度問卷調查回收狀況</title>
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
	.head5 {
color: red;font-family : Verdana, Helvetica, sans-serif;
font-size : 16pt;
font-weight : bold; 
}
.style3 {color: #FF0000; font-weight: bold; }
.style4 {color: #0000FF}
.style11 {font-size: 18px; color: #FF0000; font-weight: bold; }
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
		  	//	echo  $_SESSION['sname']; 
		  	?>
		  	</font>，您好。
		  </p>
	</tr>
	<tr>
	  <td class="header2">&nbsp;查詢102學年問卷調查回收狀況</td>
	</tr>
	<tr><td><p>&nbsp;</p></td></tr>
	<tr>
		<td>
			<table id="return" width="75%" align="center" border="1" style="display:inline">
				<tr>
  				  <td class="header1" align="center">&nbsp;<span>『<? //echo $_SESSION['sname']; ?>』實習生調查目前問卷回收狀況</span></td>
				</tr>
				<tr>
					<td>
						<table width="100%" border="0" align="center" bordercolor="#9ECEFE">
							<tr>
								<td width="20%" align="center">&nbsp;</td>
								<td width="20%" align="center"><span class="style9">學校母體數</span></td>
								<td width="20%" align="center"><span class="style9">完成填答人數</span></td>
								<td width="20%" align="center"><span class="style9">回收率</span></td>
                               	<td width="20%" align="center"><span class="style9">全國回收率</span></td>
							</tr>

							<tr>
								<td width="20%" align="center"><span class="style9">總計</span></td>
								<td width="20%" align="center"><span class="style9"><? echo $fieldwork_totalnum; ?></span></td>
								<td width="20%" align="center"><span class="style9"><? echo $fieldwork_returnnum; ?></span></td>
								<td width="20%" align="center"><span class="style9"><font color="red"><? if($fieldwork_return_percent==0) echo "----"; else echo "$fieldwork_return_percent%"; ?></font></span></td>
                                <td width="20%" align="center"><span class="style9"><font color="red"><? //if($fieldwork_cy_return_percent==0) echo "----"; else echo "$fieldwork_cy_return_percent%"; ?></font></span></td>
							</tr>
					  </table>
					</td>
				</tr>
			<tr>
  				  <td class="header1" align="center">&nbsp;<span>『<? //echo $_SESSION['sname']; ?>』畢業生調查目前問卷回收狀況</span></td>
				</tr>
				<tr>
					<td>
						<table width="100%" border="0" align="center" bordercolor="#9ECEFE">
							<tr>
								<td width="20%" align="center">&nbsp;</td>
								<td width="20%" align="center"><span class="style9">學校母體數</span></td>
								<td width="20%" align="center"><span class="style9">完成填答人數</span></td>
								<td width="20%" align="center"><span class="style9">回收率</span></td>
                               	<td width="20%" align="center"><span class="style9">全國回收率</span></td>
							</tr>
							<tr>
								<td width="20%" align="center"><span class="style9">總計</span></td>
								<td width="20%" align="center"><span class="style9"><? echo $graduation102_totalnum; ?></span></td>
								<td width="20%" align="center"><span class="style9"><? echo $graduation102_returnnum; ?></span></td>
								<td width="20%" align="center"><span class="style9"><font color="red"><? if($graduation102_return_percent==0) echo "----"; else echo "$graduation102_return_percent%"; ?></font></span></td>
                                <td width="20%" align="center"><span class="style9"><font color="red"><? //if($graduation102_cy_return_percent==0) echo "----"; else echo "$graduation102_cy_return_percent%"; ?></font></span></td>
							</tr>
					  </table>
					</td>
			</tr>
			<tr>
  				  <td class="header1" align="center">&nbsp;<span>『<? //echo $_SESSION['sname']; ?>』新進師資生調查目前問卷回收狀況</span></td>
				</tr>
				<tr>
					<td>
						<table width="100%" border="0" align="center" bordercolor="#9ECEFE">
							<tr>
								<td width="20%" align="center">&nbsp;</td>
								<td width="20%" align="center"><span class="style9">學校母體數</span></td>
								<td width="20%" align="center"><span class="style9">完成填答人數</span></td>
								<td width="20%" align="center"><span class="style9">回收率</span></td>
                               	<td width="20%" align="center"><span class="style9">全國回收率</span></td>
							</tr>
							<tr>
								<td width="20%" align="center"><span class="style9">總計</span></td>
								<td width="20%" align="center"><span class="style9"><? echo $newedu101_totalnum; ?></span></td>
								<td width="20%" align="center"><span class="style9"><? echo $newedu101_returnnum; ?></span></td>
								<td width="20%" align="center"><span class="style9"><font color="red"><? if($newedu101_return_percent==0) echo "----"; else echo "$newedu101_return_percent%"; ?></font></span></td>
                                <td width="20%" align="center"><span class="style9"><font color="red"><? //if($newedu101_cy_return_percent==0) echo "----"; else echo "$newedu101_cy_return_percent%"; ?></font></span></td>
							</tr>
					  </table>
					</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr><td><p>&nbsp;</p></td></tr>
</table>
</body>
</html>