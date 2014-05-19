<?
##########################################################################################
#
# filename: fieldwork102.php
# function: 102年實習師資生資料上傳與匯入基本資料(新版)
#
# 維護者  : 周家吉
# 維護日期: 2013/11/19
#
##########################################################################################

	session_start();

	if (!($_SESSION['Login'])){
		header("Location: ../../index.php");
	}
	$sch_id=$_SESSION['sch_id100'];//學校代號

	include_once("/home/leon/data/edu/config/use_102/setting.inc.php"); 


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>上傳102年實習師資生資料</title>
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
</style>
<?
  
  //資料庫連結，及存取之資料表
  $sql_name = "tted_edu_102";
  $sql = new mod_db();  
  $q_string2 = "select * from [upload102] where school='".$sch_id."' AND type=2 order by cid";

  $obj_query2 = $sql->query("$q_string2");
  $sql->disconnect();
  

?>
</head>

<body bottommargin="0" topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0" bgcolor="white">
<table cellpadding="3" cellspacing="1" border="0" width="100%">
	<tr>
	  <td class="header2">&nbsp;上傳102學年度實習師資生資料</td>
	</tr>
</table>
<table id="newupload" width="95%" align="center" style="display:inline" border="1">
  <tr bgcolor="#FFFFCC">
    <td colspan="8" align="center">上傳102學年度實習師資生資料</td>
  </tr>
  <tr id="gen_content">
	<td colspan="8" align="left" style="padding-left:10px;border-bottom:1px solid black;border-left:1px solid black;">
		<br>※&nbsp;&nbsp;基本資料欄位格式：<a href="../function/download.php?file=fieldwork102.xls">表格下載</a> /
        									  <a href="../function/download.php?file=fieldwork102_example.xls">範本下載</a>
        <!--<a href="../../download.php?file=101tutor_2007.xlsx">範本表格下載(2007版本)</a><br><br>-->
		<fieldset style="width:60%;font-size:16px">
			<legend>匯入說明：</legend>
			1.請下載範本表格後，請依照此欄位格式填入資料。<br>
			2.匯入檔案類型請使用<b>Excel 2003</b>。<br>
			3.填入資料時請留【欄位格式表頭】再進行匯入，不用更動此表格任一欄位，以免造成匯入錯誤。<br>
			 <font color='red'>
			4.匯入檔案若使用【加密】或【隱藏表頭欄位】或【凍結表頭欄位】皆有可能造成匯入錯誤。
			</font><br>			
			5.若匯入含有特殊文字，會顯示為【無資料】，此時可將特殊文字以星號(*)取代，即可正常匯入資料庫。<br>
			 若仍無法正常匯入，請洽教評中心承辦人員協助排除。</font>(02-7734-3669)<br>
            6.<font color="#0000FF"><strong>如遇表格資料皆已準備正確，但依舊無法上傳，建議另外下載本中心格式，將原本的資料以<u><font color="#FF0000">「複製」以「選擇性貼上」選擇「值」</font></u>的方式貼至本中心的制式表格中。</strong></font><br>
            7.<a href="../function/download.php?file=fieldwork102QA.doc"><font size="3"><strong>名單上傳Q&amp;A</strong></font></a>
	  </fieldset>
		<br><br>
		  <fieldset style="width:60%">
		  <legend>檔案匯入：</legend>
			&nbsp;版&nbsp;本&nbsp;選&nbsp;擇：
			<input name="chooseFun" type="radio" value="1" onClick="file1.style.display='inline'; file2.style.display='none';" checked="true">Excel 2003版本(xls)
			<!-- <input name="chooseFun" type="radio" value="2" onClick="file2.style.display='inline'; file1.style.display='none';" >Excel 2007以上版本(xlsx) -->
			
			
			<form enctype="multipart/form-data" method="POST" name="FileForm1" action="upload_fieldwork102Data.php" STYLE="margin: 0px; padding: 0px;">
				<table id="file1" style="display='inline'">
				<tr>
				<td>
				功&nbsp;能&nbsp;選&nbsp;擇：&nbsp;<select name="memo" size="1">
                <option value="0">請選擇</option>
                <option value="1">增加新名單</option>
                <option value="2">更改名單資料</option>
                <!--<option value="3">刪除名單資料</option>-->
                <option value="4">完整名單資料</option></select>
				</td>
				</tr>
					<tr>
						<td align="left">
							檔&nbsp;案&nbsp;選&nbsp;擇：
							<input name="sfile1" type="file" id="sfile1">
							<input type="button" value="送出檔案" onClick="checkFile1()">
						</td>
					</tr>
					<tr>
						<td align="left">
							連絡人資訊：<input name="contactinfo" size="50"> (50字內)<br><br>
						</td>
					</tr>
				</table>
			</form>			
		  </fieldset>
		  <br>
		<?php
			echo "<br>".$_POST["post_arr"][0];
			echo "<br>".$_POST["post_arr"][1];
			echo "<br>".$_POST["post_arr"][2]."<br>";
		 ?>
	</td>
  </tr>
<?php
	
  $i=1;
  while($obj_all2 = $sql->objects('',$obj_query2)){
		if ($i==1){
?>
<tr>
<td colspan="10" align="left"><input type="button" name="Submit" value="查詢已上傳名單總列表" onClick="location.href='../function/modify_fieldwork102.php'" style="width:200;height:30;color:#999;background-color:#F00";> </td>
</tr>
  <tr id="gen_content">
	<td colspan="10" align="center" style="background: #FFFFCC;padding-left:10px;border-bottom:1px solid black;border-left:1px solid black;">已上傳的名單</td>
  </tr>
<?php
		}
?>
  <tr id="gen_content">
	<td colspan="10" align="left" style="padding-left:10px;border-bottom:1px solid black;border-left:1px solid black;"><?php echo "　檔案".$i."　上傳於".$obj_all2->uploadtime."___執行狀態 :".$obj_all2->status."。";?></td>
  </tr>
<?php
		$i++;
  }
?>


</table>
</body>
</html>

<script language="JavaScript">

//這裡控制要檢查的項目，true表示要檢查，false表示不檢查   

var isCheckFileType = true;  //是否檢查檔案副檔名 

function checkFile() {   

    var f = document.FileForm;   
    var re = /\.(xlsx)$/i;  //允許的檔案副檔名   

    if (isCheckFileType && !re.test(f.sfile.value)) {   
        alert("檔案類型錯誤：只允許上傳xlsx(excel 2007以上)檔案");   
    } else {   
        document.FileForm.submit();   
    }   
} 

function checkFile1() {   

	var f1 = document.FileForm1;	
    var re = /\.(xls)$/i;  //允許的檔案副檔名    

	if (isCheckFileType && !re.test(f1.sfile1.value)) {   
        alert("檔案類型錯誤：只允許上傳xls(excel 2003)檔案");   
    } else {   
        document.FileForm1.submit();   
    } 
} 

</script> 
