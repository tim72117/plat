<?
##########################################################################################
#
# filename: gra102_puserdata.php
# function: 執行上傳、修改、刪除101學年度畢業國中學生基本資料
#
# 維護者  : 周家吉
# 維護日期: 2013/04/26
#
##########################################################################################
	session_start();

	if (!($_SESSION['Login'])){
		header("Location: ../../index.php");
	}
		
	$value=$_POST['memo'];//操作類別
	$filetype=$_POST['filetype'];
	
	$sch_id=$_SESSION['sch_id'];
	$name=$_SESSION['name'];
	$account=$_SESSION['account'];    
	$page_index=$_SESSION['page_index'];
	    
	$rpage = $_SERVER['HTTP_REFERER']; //前頁
	
	$path="junior/";
	
	$validation = 0;
	$ip = getenv("REMOTE_ADDR");
	$now=date("Ymd-His");
		
	$today = date("Y/n/d H:i:s");
	$school = $_SESSION['sch_id'];	

  	$tb_name='[use_102].[dbo].[upload102]';
	
	$funname='use102/upload/gra/gra102_puserdata.php';
	$nasdir='/se/use/use_102/'.$path;
	$serverdir ='../../../../../../../home/leon/data/use102/data/'.$path;
	$query_name=$now.".sql";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>上傳101學年度畢業國中生基本資料</title>
<link href="../../css/theme_inc.css" rel="stylesheet" type="text/css">

</head>

<body bottommargin="0" topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0" bgcolor="white">
<table cellpadding="3" cellspacing="1" border="0" width="100%">
	<tr>
	  <td class="header2">&nbsp;上傳名單資料</td>
	</tr>
</table>
<p>&nbsp;</p>

<?
 //資料庫連結，及存取之資料表
 include("../../../../../../../home/leon/data/use102/config/setting102.inc.php");
 include_once("../../../../../../../home/leon/data/use102/config/ftp102.inc.php");

  $sql = new mod_db();

		//處理要寫入的參數
		$sfilename = stripslashes($_FILES['sfile']['name']);
		$sfilename = $now."_".$school."_".$value."_".$filetype."_".$sfilename;
		
        $sdestination = $serverdir."$sfilename";		
	
		//將上傳資料放置系統所在本身伺服器內
     	copy($sfile,$sdestination);		
		
		//開啟FTP連結
		$conn = ftp_connect($ftp_host);
     	$login = ftp_login($conn, $ftp_user, $ftp_password);

		if($login == true){
		
			//將上傳的檔案寫入伺服器內
				$mode = ftp_pasv($conn, TRUE);
			
				$query_str1 = "Insert into $tb_name (school,name,account,memo,contact,filename,ip,type,uploadtime) 
				Values ('".$school."','".$name."','".$account."','".$memo."','".$contact."','".$sfilename."','".$ip."','".$filetype."','".$today."')";
			
			//寫入DB 記錄 同時產生sql備份
				$file_name =  $serverdir.$now.".sql";		

				$f = fopen($file_name,"a+");
				fwrite($f,$query_str1);
				fclose($f);
			
			//上傳之檔案與sql進行備份至FTP
				
				if($login == true){
				
					ftp_put($conn,$nasdir."$sfilename","$sdestination",FTP_BINARY);
					ftp_put($conn,$nasdir."$now".".sql","$file_name",FTP_BINARY);
					ftp_quit($conn);
				}
				$query1=$sql->query("$query_str1");
				
				
				
			if ($query1){
					$validation = 1;		
							
					$sql_log = new mod_db();
				
					$insert_log = "INSERT INTO [use_102].[dbo].[log_102] ([function] ,[school] ,[name] ,[account] ,[type],[nasdir] ,[serverdir],[filename] ,[ip])
VALUES ('$funname','$sch_id' ,'$name','$account','0','$nasdir','$serverdir','$query_name' ,'$ip')";	 
								
					$sql_log->query("$insert_log");
					$sql_log->disconnect();
			}else{
					$validation = 2;
			}

			//判斷是否更新成功以決定echo的訊息
			
			if ($validation == 1){
		?>

	<script language="javascript">
					alert('資料上傳成功');
					location.replace('<?=$rpage?>');
				</script>
	<?php			
			}else if ($validation == 2){
	?>
				<script language="javascript">
					alert('請選擇檔案');
					
					location.replace('<?=$rpage?>');
				</script>
	<?php			
			}else{
	?>
				<script language="javascript">
					alert('資料上傳失敗,請重新上傳');
					location.replace('<?=$rpage?>');
				</script>
	<?php						
			}
		}
 $sql->disconnect();
?>

</body>
</html>