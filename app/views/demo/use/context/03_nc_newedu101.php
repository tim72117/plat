<title>未填答資料</title>
<style>
	.header1, h1
		{color: #ffffff; background: #B69866; font-weight: bold; font-family: Tahoma, Verdana; font-size: 13px; margin: 0px; padding: 3px;}
	.header2, h2
		{color: #000000; background: #B69866; font-weight: bold; font-family: Tahoma, Verdana; font-size: 12px;}
</style>

<td class="header2"></td>
<tr>
  <td>
    <table id="depart" width="100%" align="center" border="1" style="display:inline">
	  <tr>
		<td class="header1" align="center">&nbsp;<span class="style1">變更進入學校</span></td>
	  </tr>
	  <tr>
		<td>               
<form action="03_nc_newedu101" method="post">
<p align="center">請選擇學校，並按送出</p>
<p align="center">
<?PHP								
$users = DB::table('ques_admin.dbo.pub_uschool')
				  ->select('uid','uname')->lists('uname','uid');
?>
<?=
Form::select('uid', $users,  Input::get('uid', ''))
?>
<input type="submit" name="Submit2" value="送出"></form>
  </td>
</tr>
</table>
<p></p>

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

$schid = Input::get('uid');
if (Input::has('uid')){
$resultss = DB::table('tted_edu_102.dbo.newedu101_pstat as a')
            ->join('tted_edu_102.dbo.newedu101_userinfo as b', 'a.newcid', '=','b.newcid')
			->where('uid','=',$schid)
			->get();
			
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
	
	  }}
?> 
            </table>
		  </td>
        </tr>
      </table>
	</td>
  </tr>
</table>