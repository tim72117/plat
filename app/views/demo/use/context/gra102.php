<?
##########################################################################################
#
# filename: gra102.php
# function: 上傳101學年度國三畢業生基本資料
#
# 維護者  : 周家吉
# 維護日期: 2013/04/26
#
##########################################################################################
$now=date("Ymd-His");
	
if (Input::has('filetype')){

//接收傳值
$value = Input::get('memo');
$filetype = Input::get('filetype');
$name = Input::get('name');
$school = "test_school"; //之後改回抓使用者學校名

//上傳檔案
if ($_FILES["sfile"]["error"] > 0)echo "Error: ".$_FILES["sfile"]["error"];
else{
	//echo storage_path()."</br>";//列出路徑
echo "檔案名稱: " . $_FILES["sfile"]["name"]."<br/>";
echo "檔案類型: " . $_FILES["sfile"]["type"]."<br/>";
echo "檔案大小: " . ($_FILES["sfile"]["size"] / 1024)." Kb<br />";
echo "暫存名稱: " . $_FILES["sfile"]["tmp_name"]."<br/>";


		$sfilename = stripslashes($_FILES['sfile']['name']);//抓出上傳檔名
		$sfilename = $now."_".$school."_".$value."_".$filetype."_".$sfilename;//將檔名加上資訊
		
		$serverdir = storage_path()."/storage"; //設定存取路徑(存放在app/storage下
		
		$sfile = $_FILES["sfile"]["tmp_name"];
        $sdestination = $serverdir."$sfilename";		
		copy($sfile,$sdestination);
	
}

?>

 <form action="<?=$fileAcitver->get_post_url()?>" method="post">
        <input name="add" type="submit" value="回到上頁">
	<?
	}
		
else {		  
/*include("../../../css/use100.css");  

	session_start();

	if (!($_SESSION['Login'])){
		header("Location: ../../index.php");
	}
	
	$sch_id=$_SESSION['sch_id'];
	$tb_name='[use_102].[dbo].[upload102]';
*/
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<script src="<?=asset('js/tigra_tables.js')?>"></script>
<script language="JavaScript" src="../../tigra_tables.js"></script>
<title> 上傳101學年度國三畢業生基本資料</title>
<link href="../../../css/theme_inc.css" rel="stylesheet" type="text/css">
<?


  //資料庫連結，及存取之資料表

/*  include("../../../../../../../../../../../home/leon/data/use102/config/setting102.inc.php");

  $sql = new mod_db();
  $q_string = "select uploadtime,filename from $tb_name where school='".$_SESSION['sch_id']."' AND type=9 order by id ";
  $obj_query = $sql->query("$q_string");    
  $sql->disconnect();*/
  
$resultss = DB::table('use_102.dbo.upload102')
				  ->select('uploadtime','filename')
				 //->where('type', '=', 9)  測試用，應選9
				  ->get();

?>
</head>

<body bottommargin="0" topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0" bgcolor="white">
<table cellpadding="3" cellspacing="1" border="0" width="100%">
	<tr>
	  <td class="header2">101學年度國三畢業生基本資料</td>
	</tr>
</table>
<table id="newupload" width="80%" align="left" style="display:inline" border="1" >
	<tr >
	  <td class="header1" colspan="8" align="center" >上傳101學年度國三畢業生基本資料</td>
  </tr>
  <tr id="gen_content">

    <td colspan="8" align="left" style="padding-left:10px;border-bottom:1px solid black;border-left:1px solid black;">相關檔案: 
    	<a href="../../function/download_file.php?file=101gra_form.xls">表格下載</a> <!-- /
	    <a href="../../function/download.php?file=101gra_in.xls">格式範例</a>  -->

<!---原本		<form enctype="multipart/form-data" method="POST" action="gra102_puserdata">  --->
        <form enctype="multipart/form-data" action="<?=$fileAcitver->get_post_url()?>" method="post">

                <p align="center">所提供名單作業說明：
                  <select name="memo" size="1">
<option value="0">請選擇</option>
                             <option value="4">完整名單資料</option>
                             <option value="1">增加新名單</option>
                             <option value="2">更改名單資料</option>
                             <option value="3">刪除名單資料</option>
                   </select><br><br>
            備註：
        	<input name="contact" size="50"> 
           		<font COLOR ="red">(50字內)</font><br><br>
					<input type="file" name="sfile">
					<input type="hidden" name="filetype" value="9">
            <br><br>																						
        	<input name="add" type="submit" value="送出檔案"></p></p>
      </form>
	</td>
  </tr>		
<?php
  $i=1;
foreach ($resultss as $results){
 // while($obj_all = $sql->objects('',$obj_query)){
		if ($i==1){

?>
  <tr id="gen_content">
	<td colspan="8" class="header1" align="center" style="padding-left:10px;border-bottom:1px solid black;border-left:1px solid black;">已上傳的名單</td>
  </tr>
<?php
	}
?>
  <tr id="gen_content">
	<td align="left" style="padding-left:10px;border-bottom:1px solid black;border-left:1px solid black;"><?php echo "檔案".$i."　上傳於".$results->uploadtime."___".$results->filename."。";?></td>
  </tr>
<?php
		$i++;
  }}
?>
</table>
</body>
</html>