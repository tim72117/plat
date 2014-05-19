<?
##########################################################################################
#
# filename: modify_fieldwork102.php
# function: 修改102高一新生狀態
#           1. 檢查是否已登入
#           2. 列出選擇
#
# 維護者  : 周家吉
# 維護日期: 2013/11/22
#
##########################################################################################

  //取時目前時間
  $now = date("Y/n/d g:i");
  $table_name = 'tted_edu_102.dbo.graduation102_userinfo';
  
if (Input::has('pmode')){
	$pmode = Input::get('pmode');
	echo 'pmode = '.$pmode.' (if)';
	  switch ($pmode) {  //判斷選擇執行查詢語句
	         case "0":
		 	   $resultss = DB::table($table_name)->where('pstat', '=', 0)->get();
			   $num_all = DB::table($table_name)->where('pstat', '=', 0)->count();
	         break;
	         case "1":
		  	   $resultss = DB::table($table_name)->where('pstat', '=', 1)->get();
			   $num_all = DB::table($table_name)->where('pstat', '=', 1)->count();
	         break;
	         default:
			   $resultss = DB::table($table_name)->where('pstat', '=', 0)->get();
			   $num_all = DB::table($table_name)->where('pstat', '=', 0)->count();
	         break;
	  }}
else {
	$pmode = 0;
	echo 'pmode = '.$pmode.' (else)';
	$resultss = DB::table($table_name)->where('pstat', '=', 0)->get();
	$num_all = DB::table($table_name)->where('pstat', '=', 0)->count();
	}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>資料修改</title>
    
<script src="<?=asset('js/jquery-1.7.1.min.js')?>"></script>
<script src="<?=asset('js/jeditable.js')?>"></script>
<script src="<?=asset('js/tigra_tables.js')?>"></script>

<!--
<script language="JavaScript" src="../../js/tigra_tables.js"></script>
<script type="text/javascript" src="../../js/jquery-1.7.1.min.js"></script>   
<script type="text/javascript" src="../../js/jeditable.js"></script>
-->
<script type="text/javascript">
$( function() {
		$("#all_table").on("mouseover",'th.pstat', function() {
        	$(this).editable("04_save_modify_graduation102" ,{
				data   : "{'0':'調查對象','1':'非調查對象'}",
    			type   : "select",
				id        : 'elementid',
				name      : 'newvalue',
				tooltip   : '點兩下可修改',
				cancel    : '取消',
				submit    : '確定',
				indicator : '修改中...',
				event     : "dblclick",
				callback : function(value, settings) {
					var pmode = '<? echo $pmode; ?>';
					if(pmode== 0)
					{
						if(value.substr(0,9) == '	修改成:調查對象' ){
							 $(this).parent().find(":checkbox").prop("disabled",true);
						 }else{
							$(this).parent().find(":checkbox").prop("disabled",false);
						}
					}
					else
					{
						console.log(value);
						if(value.substr(0,9) == '	修改成:調查對象' ){
							 $(this).parent().find(":checkbox").prop("disabled",true);
						 }else{
							$(this).parent().find(":checkbox").prop("disabled",false);
						}	
					}
				 }
				});			
	     });
		 });
</script>
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
	  <td class="header2">&nbsp;更改102學年度應屆畢業師資生狀態</td>
	</tr>
	<tr>
	  <td>
	    <table cellpadding=4 cellspacing=0 border=0 width=90% align="left">
				<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>
					<table id="menu_table" width="30%" border="1" cellspacing=1 cellpadding=1>
					<tr>
						<th scope="col" width="50%" <? if ($pmode==0) {echo "bgcolor=#F0F0FD";} else {echo "bgcolor=#ffffff";}?>><span class="head2"><a href="04_modify_graduation102?pmode=0">102學年度應屆畢業師資生名單</a></span></th>
								<th scope="col" width="50%" <? if ($pmode==1) {echo "bgcolor=#F0F0FD";} else {echo "bgcolor=#ffffff";}?>><span class="head2"><a href="04_modify_graduation102?pmode=1">已刪除102學年度應屆畢業師資生名單</a></span></th>
							</tr>
			</table>
					</td>
				</tr>
			</table>
			<p>&nbsp;</p> 
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<table cellpadding=4 cellspacing=0 border=0 align="left" width="95%">
				<tr>
				<td>
			  	  <p><font color="#FF0000"><? //echo $user; ?> </font>您好：貴校目前共有 <font color="blue" size="+3"><? echo $num_all;?></font> 位狀態為&lt;<font color="red"><?php if($pmode==1){ echo "非102學年度應屆畢業師資生";}else{ echo "102學年度應屆畢業師資生";} ?></font>&gt;之102學年度應屆畢業師資生</p>
			    <input type="hidden" name="stu_mode" value="<?php if($pmode==1){ echo "0";}else{ echo "1";}?>">
						<span class="head5"></span><?php if($pmode==1){ echo "請勾選要<<font color=\"red\">復原刪除</font>>之人員";}else{ echo "請勾選要<<font color=\"red\">刪除</font>>之人員";}?>，勾選完畢請按最下方或右方之送出按鈕。
						<input type="hidden" name="degree" value="4"/>
						<br>
		  	      <table width="100%" border="1" id="all_table" cellpadding="2">
		            <tr class="header1">
									<th width="10%" scope="col" align="center">學號</th>
                                    <th width="10%" scope="col" align="center">科系所代碼</th>
                                    <th width="10%" scope="col" align="center">科系中文名稱</th>
                                    <th width="10%" scope="col" align="center">學制別</th>
									<th width="10%" scope="col" align="center">姓名</th>
                                    <th width="10%" scope="col" align="center">出生年</th>
									<th width="20%" scope="col" align="center">是/否調查對象</th>
								    <th width="15%" scope="col">目前狀態</th>
		            </tr>
<?
//	  $all_query = $sql->query("$list_string");
//		$dep = 0;
	
		if ($pmode == 0)
			$std_pstat = '102學年度應屆畢業師資生';
		else
			$std_pstat = '非102學年度應屆畢業師資生';
	
		foreach ($resultss as $results){

		 //休退學其它
		if($results->pstat == 0)
			{$pstat ='調查對象';}
		else if($results->pstat == 1)
			{$pstat ='非調查對象';}
				  
			echo "<tr>";
			echo "<td scope=col align=center>&nbsp;&nbsp;&nbsp;".$results->stdid. "</td>\n";
			echo "<td scope=col align=center>&nbsp;&nbsp;&nbsp;".$results->udepcode. "</td>\n";
			echo "<td scope=col align=center>&nbsp;&nbsp;&nbsp;".$results->udepname. "</td>\n";
			echo "<td scope=col align=center>".$results->stdschoolsys ."</td>\n";
			echo "<td scope=col align=center>&nbsp;&nbsp;&nbsp;".$results->stdname . "</font></td>\n";	
			echo "<td scope=col align=center>&nbsp;&nbsp;&nbsp;".$results->birthyear. "</font></td>\n";		
			
		if($pstat == '調查對象'){
				echo "<th class=pstat id=$results->newcid  scope=col align=center><font color=\"#990000\">".$pstat.' 請點選兩下'. "</th >\n";
			}
		else if($pstat == '非調查對象'){
				echo "<th class=pstat id=$results->newcid  scope=col align=center><font color=\"FF8040\">".$pstat.' 請點選兩下'. "</th >\n";
			}
		
		
			if($pmode == 0){
				echo "<td scope=col align=center><font color=\"blue\">".$std_pstat. "</td>\n";
			}
			else{
				echo "<td scope=col align=center><font color=\"red\">".$std_pstat. "</td>\n";
				}
	
		
	
			echo "</tr>";
	  }
	//  $sql->disconnect();
?>
        </table>
	      <p>
		  </p>
	    <p></p>
	    <p>&nbsp;</p>
		</td></tr></table>
   </td>
  </tr>
  <tr>
	 <td class="intd">&nbsp;</td>
  </tr>
</table>
<script language="JavaScript">
	tigra_tables('all_table', 2, 0, '#FFFFFF', '#F0F0FD', '#8FBEFD', '#8FBEFD');
</script>
</body>
</html>
<?
//	}
?>