<?

//抓取使用者之sch_id
foreach (auth::user()->works as $users)
	{
		$sch_id =  $users->sch_id; 
	}
?>

<!---
<style>
	.header1, h1
		{color: #ffffff; background: #B69866; font-weight: bold; font-family: Tahoma, Verdana; font-size: 13px; margin: 0px; padding: 3px;}
	.header2, h2
		{color: #000000; background: #B69866; font-weight: bold; font-family: Tahoma, Verdana; font-size: 12px;}
</style>
-->

<? if ($sch_id = 9999){ //若為教評中心人士(sch_id=9999)，則出現選擇學校選單?>
<table width="100%" align="center" >
	<tr>
		<td class="header2">
		<table id="depart" width="100%" align="center" border="1" style="display:inline">
			<tr>
				<td class="header1" align="center">&nbsp;<span class="style1">變更進入學校</span></td>
			</tr>
			<tr>
				<td>              
                <form action="<?=$fileAcitver->get_post_url()?>" method="post">
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
		</td>
	</tr>
</table>
<? } ?>
</br>
</br>


<table cellpadding="3" cellspacing="1" border="0" width="100%" align="center">
	<tr>
		<td class="header2">&nbsp;102學年度應屆畢業師資生</td>
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

<?

//若是自行選擇學校，則sch_id取該選擇學校之id；若無則取使用者所屬之學校id
if (Input::has('uid')){
	$sch_id = Input::get('uid');}
	
$data= DB::table('tted_edu_102.dbo.graduation102_pstat as a')
            ->join('tted_edu_102.dbo.graduation102_userinfo as b', 'a.newcid', '=','b.newcid')
			->where('uid','=',$sch_id)
			->get();
			
foreach ($data as $results){
	
	if($results){
		echo "<tr>";
		echo "<td scope=col align='center'>".$results->stdid."</td>\n";
		echo "<td scope=col align='center.'>".$results->udepcode."</td>\n";
		echo "<td scope=col align='center'>".$results->udepname."</td>\n";
		echo "<td scope=col align='center'>".$results->stdname."</td>\n";
		echo "<td scope=col align='center'>".$results->birthyear."</td>\n";
		//若無頁數資料則顯示0，若填答完成則以紅色字表示
		if($results->page==null) echo "<td scope=col align='center'>".'0'."</td>\n"; 
		else if($results->page >='16') echo "<td align='center' scope=col><span><font color=red>填答完成</font></span></td>\n";	
			 else echo "<td scope=col align='center'>".$results->page."</td>\n";
		}
	else{
		echo "該校尚無學生資料";
		return null; //資料表裡無資料
		}
	}
?> 
		      </table>
			</td>
			</tr>
		</table>
		</td>
	</tr>
</table>