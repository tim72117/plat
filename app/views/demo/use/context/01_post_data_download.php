<?
##########################################################################################
#
# filename: post_data_download.php
# function: 調查文件及文宣下載
#
# 維護者  : 周家吉
# 維護日期: 2013/6/28
#
##########################################################################################
/*  session_start();

  if (!($_SESSION['Login'])){
		header("Location: ../../index.php");
  }	  
	$sch_id=$_SESSION['sch_id100'];//學校代號
	include_once("/home/leon/data/edu/config/use_102/setting.inc.php"); 
  
  		$sql = new mod_db();
		$sel_str = "SELECT  [cid]
						  ,[name]
						  ,[account]
						  ,[title]
						  ,[filename]
						  ,[type]
						  ,[ip]
						  ,CONVERT(CHAR(10), [uploadtime], 120) [uploadtime]
			  FROM [tted_edu_102].[dbo].[pub_file_post_102]
			  ORDER BY type,cid desc";													
		$query_file = $sql->query($sel_str);
		$sql->disconnect();
*/?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script language="JavaScript" src="../../js/tigra_tables.js"></script>
<title>「調查文件及文宣下載」</title>
<style>
	.header1, h1
		{color: #ffffff; background: #B69866; font-weight: bold; font-family: Tahoma, Verdana; font-size: 13px; margin: 0px; padding: 3px;}
	.header2, h2
		{color: #000000; background: #B69866; font-weight: bold; font-family: Tahoma, Verdana; font-size: 12px;}
</style>
</head>

<body  bottommargin="0" topmargin="0" leftmargin="10" rightmargin="10" marginheight="10" marginwidth="10" bgcolor="white">

<table id="all_table" width="99%" cellpadding=3 cellspacing=0 border=1 align="left">
<tr>
	  <td class="header1">「102年度所有調查文件及文宣下載」</td>
	</tr>
<?php	
/*$type ='';
  while($obj_all = $sql->objects('',$query_file)){
	  if($type !=$obj_all->type)
	  {
		  $type = $obj_all->type;
		  if($type=='1'){$typeStr='101學年度新進師資生';}
		  if($type=='2'){$typeStr='101學年度應屆畢業師資生';}
		  if($type=='3'){$typeStr='102年實習師資生';}
		  if($type=='4'){$typeStr='102學年度應屆畢業師資生';}
		  if($type=='5'){$typeStr='其它';}
	      echo "<tr><td class=\"header2\" align=\"center\"><font color=\"blue\" size=\"5\">".$typeStr."</font></td><tr>"; }
		  */
		
	 
	  ?>
	<tr id="gen_content">
		<td  align="left" style="padding-left:10px;border-bottom:1px solid black;border-left:1px solid black;">
			<?php //echo '標題 :'."<font color=\"#FF0000\">".$obj_all->title."</font>"."　檔案 :".$obj_all->filename."　上傳於".$obj_all->uploadtime."。";?>
            <?php //echo '標題 :'."<font color=\"#FF0000\">".$obj_all->title."</font>"."　公告時間 : ".$obj_all->uploadtime."。";?>
            <input type="button" name="Submit" value="按此下載" onClick="location.href='download_file.php?file=<?php //echo $obj_all->filename;?>'">
        </td>
	</tr>
<?
 // }
?>
</table>

<script language="JavaScript">
	tigra_tables('all_table', 1, 0, '#ffffff', '#ffffcc', '#ffcc66', '#cccccc');
</script>
</font>
</body>
</html>

