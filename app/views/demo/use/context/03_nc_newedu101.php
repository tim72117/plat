<?
##########################################################################################
#
# filename: nc_graduation102.php
# function: 列出102學年度新進師資生師資生填答
# 維護者  : 周家吉
# 維護日期: 20140213
#
##########################################################################################

 /*session_start();
	if (!($_SESSION['Login'])){
		header("Location: ../../index.php");
	}
	$sch_id=$_SESSION['sch_id100'];//學校代號

	include_once("/home/leon/data/edu/config/use_102/setting.inc.php"); 
	
	  $sql = new mod_db();
	  $list_string = "SELECT distinct s.stdid,s.udepcode, s.udepname,s.stdname, birthyear as birth, p.page
						FROM [tted_edu_102].[dbo].[newedu101_pstat] p 
						RIGHT OUTER JOIN [tted_edu_102].[dbo].[newedu101_userinfo] s 
						ON p.newcid = s.newcid 
						WHERE s.uid='$sch_id' and s.pstat='0' and stdname is not null  order by s.stdid" ;

	  $all_query = $sql->query("$list_string");
	  $sql->disconnect();*/
	  	  
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>未填答資料</title>
<script language="JavaScript" src="../../tigra_tables.js"></script>
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
	  <td class="header2">&nbsp;101學年度新進師資生</td>
	</tr>
    <tr>
		<td>
		<table cellpadding=4 cellspacing=0 border=0 align="left" width="95%">
		  <tr>
			<td>
		       <table width="70%" border="1" id="all_table"  cellpadding="5">
		       <tr class="header1">
               		<th width="10%" scope="col">學號</th>
					<th width="10%" scope="col">科系所代碼</th>
					<th width="10%" scope="col">班級名稱</th>
					<th width="10%" scope="col">學生姓名</th>
                    <th width="10%" scope="col">出生日</th>
                    <th width="10%" scope="col">填答頁數</th>
				</tr>
<?
$resultss = DB::table('tted_edu_102.dbo.newedu101_pstat')
            ->join('tted_edu_102.dbo.newedu101_userinfo', 'tted_edu_102.dbo.newedu101_pstat.newcid', '=','tted_edu_102.dbo.newedu101_userinfo.newcid')->get();
			//->select('tted_edu_102.dbo.fieldwork102_pstat.newcid', 'tted_edu_102.dbo.fieldwork102_userinfo.stdname');
/*	  while ($obj_all = $sql->objects('',$all_query)){
*/		  
foreach ($resultss as $results){
		
      if($results){

			echo "<tr>";
			echo "<td scope=col align='center'>".$results->stdid."</td>\n";
			echo "<td scope=col align='center.'>".$results->udepcode."</td>\n";
			echo "<td scope=col align='center'>".$results->udepname."</td>\n";
			echo "<td scope=col align='center'>".$results->stdname."</td>\n";
			echo "<td scope=col align='center'>".$results->birthyear."</td>\n";
			if($results->page==null) echo "<td scope=col align='center'>".'0'."</td>\n"; 
			else if($results->page >='9') echo "<td align='center' scope=col><span><font color=red>填答完成</font></span></td>\n";
			 else echo "<td scope=col align='center'>".$results->page."</td>\n";
	  			}
				
        else {
		echo "此資料表裡尚無資料";
		return null; //資料表裡無資料
		}
	
	  }
?>          </table>
		</td></tr></table>
	</td>
  </tr>
</table>
<script language="JavaScript">
	tigra_tables('all_table', 2, 0, '#FFFFFF', '#F0F0FD', '#8FBEFD', '#8FBEFD');
</script>
</body>
</html>