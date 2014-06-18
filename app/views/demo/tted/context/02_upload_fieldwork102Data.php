<?
##########################################################################################
#
# filename: upload_fieldwork102Data.php
# function: 上傳102學年度實習師資生資料
#
# 維護者  : 周家吉
# 維護日期: 2013/11/21
#
##########################################################################################

	// 顯示所有的錯誤、警告(E_ALL)，執行時期的提醒(E_NOTICE)
	session_start();
	
	if (!($_SESSION['Login'])){
		header("Location: ../../index.php");
	}	
	
	//資料庫連結，及存取之資料表
		include_once('../../public/logincheck.php') ; //newcid by use
		include_once("/home/leon/data/edu/config/use_102/setting.inc.php"); 
		require("/home/leon/data/edu/config/ftp.inc.php");
	
		$funname ='edu_102/upload/upfieldwork102Data_2003.php';
		$serverdir ='/home/leon/data/edu/data/fieldwork102/';
	
		$tb_name='[tted_edu_102].[dbo].[fieldwork102_userinfo] ';
		date_default_timezone_set('Asia/Taipei'); // 調整時區，不然時間會少八小時
		//取得使用者登入ip
		$ip = getenv("REMOTE_ADDR");		
		$validation = 0;		
		$now = date("Ymd-His");
		
		//表單資料
		$memo = $_POST['memo'];
		$contact  = $_POST['contactinfo'];
		//取得使用者登入ip
		$ip = getenv("REMOTE_ADDR");		
		$sch_id=$_SESSION['sch_id100'];//學校代號
		$name=$_SESSION['name'];//承辦人姓名
		$account=$_SESSION['account'];//登入帳號	
				
		$InsertStr="";// 檢查匯入資料(初值)
		$error_pstr ="";// 錯誤匯入資料(初值)
		$error_str="";// 錯誤匯入資料(初值)
		
		$status_db_str="";// db匯入資料(初值)
		
		$insert_count=0;// 匯入筆數(初值)
		$delete_count=0;// 錯誤筆數(初值)
		
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>上傳101學年度實習師資生資料</title>
</head>
<body bottommargin="0" topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0" bgcolor="white" onLoad="document.form1.submit()"> 
<?		
	if ($_FILES["sfile1"]["error"] == 0){		
			//處理要寫入的參數
			$sfilename = stripslashes($_FILES['sfile1']['name']);
			$sfilename = $sch_id."_".$now."_".$sfilename;
			
			$sdestination = "$serverdir".$sfilename;
			
			//將上傳的檔案寫入伺服器內
			move_uploaded_file($_FILES["sfile1"]["tmp_name"],"$sdestination");
			
			$sql = new mod_db();
			$query_str = "Insert into [tted_edu_102].[dbo].[upload102] (school,name,account,memo,contact,filename,ip,type) 
			Values ('".$sch_id."','".$name."','".$account."','".$memo."','".$contact."','".$sfilename."','".$ip."','2')";
			$upload_query=$sql->query("$query_str");			
			$sql->disconnect();
				
			if ($_FILES["file"]["error"] ==0){			
				 $validation = 1; //上傳成功
			}else{
				$validation = 2;//上傳失敗
			}
		}

	
	if($validation == 1){
			
		require_once("../../public/Excel/reader.php");
		$Import_Sheet = new Spreadsheet_Excel_Reader(); //第一個儲存格$Import_Sheet->sheets[0]['cells'][1][1] //[row][col]
		$Import_Sheet->setOutputEncoding('BIG5');
		$Import_Sheet->read($sdestination);
		
		$Import_RowCount = $Import_Sheet->sheets[0]['numRows'];
		$Import_ColCount = $Import_Sheet->sheets[0]['numCols'];// 取得總行數$Import_RowCount,總列數$Import_ColCount
				
		// 去除空白列，並計算匯入檔案之總列數，$Import_NewColCount
		/*
		$Import_NewColCount = 0;
		for ($count = 1; $count <= $Import_ColCount; $count++) { 
			if(rtrim(ltrim($Import_Sheet->sheets[0]['cells'][1][$count])) != ""){ 
				$Import_NewColCount +=1;
			}else{
				break;
					}	
		}
		*/
		$Import_NewColCount=20;
		// 去除空白行，並計算匯入檔案之總行數，$Import_NewRowCount
		$Check_Null = 0;
		$Import_NewRowCount = 0;
		for ($rcount = 1; $rcount <= $Import_RowCount; $rcount++) { 
			for ($lcount = 1; $lcount <= $Import_NewColCount; $lcount++) { 
				if(rtrim(ltrim($Import_Sheet->sheets[0]['cells'][$rcount][$lcount])) == ""){
					$Check_Null += 1; 
					}
				}
				if($Check_Null != $Import_NewColCount){
					$Import_NewRowCount +=1;
				}
				$Check_Null = 0;
			}

		// 動態宣告陣列大小
		$check_error = array('0');
		$check_error = array_pad($check_error, $Import_NewColCount, '0');
		$error_row =0;
		
	//進行 excel 檔案讀行 
	for ($i = 4; $i <= $Import_NewRowCount; $i++) { 
		
		$check_error = array();
		$datalength = array();
		/*
		for($step =2;$step <=20;$step++){
			$temp = strlen(str_replace("?","",str_replace("'","",rtrim(ltrim($Import_Sheet->sheets[0]['cells'][$i][$step])))));
			$strlen = $step.' _ '.$temp.'<br>';
			echo $strlen;
		}		
		exit;
		*/	
		
		//$temp = str_replace("?","",str_replace("'","",rtrim(ltrim($Import_Sheet->sheets[0]['cells'][$i][$step]))));
		
		
		//外加 17
			$temp =$Import_Sheet->sheets[0]['cells'][$i][17];
			$templength = strlen($temp);
			
			//判斷欄位數是否存在資料，有資料datalength為0，否則為1，並將欄位資料寫入datalist[tmp]中
			if($templength==0){
				$datalength=0;				
			}else{
				$datalength=1;
				$datalist[17]=$temp;
			}
			
			if ($datalength!=0){
				if (preg_match("/^[0-9]+$/i", $datalist[17])) {	
					;	
				}else{
					$check_error[$step]=1;
				}
			}else{
				$check_error[$step]=2;
			}
		
		for($step =2;$step <=20;$step++){
		
			//$temp = str_replace("?","",str_replace("'","",rtrim(ltrim($Import_Sheet->sheets[0]['cells'][$i][$step]))));
							
			$temp =str_replace("?","",str_replace("'","",trim($Import_Sheet->sheets[0]['cells'][$i][$step])));
			$templength = strlen($temp);
			
			//判斷欄位數是否存在資料，有資料datalength為0，否則為1，並將欄位資料寫入datalist[tmp]中
			if($templength==0){
				$datalength=0;				
			}else{
				$datalength=1;
				$datalist[$step]=$temp;
			}
			
			// 檢查欄位開始-------------------------------------------------------------------
			// ** $check_error = 1 有問題, $check_error = 2 無資料, $check_error = 0 正確無誤 **			
			
			//學校代碼 2
			if($step ==2){
				if ($datalength!=0){
					$datalist[$step]=$sch_id;
				}else{
					$check_error[$step]=2;
				}
			}
			
			//學號 3 科系所代碼 4 科系中文名稱 5 中文姓名 7 戶籍郵遞區號 10 戶籍地址 11			
			if($step ==3 || $step ==4 || $step ==5 ||$step ==7 ||$step ==10 ||$step ==11 ){
				if ($datalength!=0){
					;
				}else{
					$check_error[$step]=2;
				}
			}
			
			//學制別6欄
			if($step ==6){
				if ($datalength!=0){
					if (eregi("^([1-6]{1})$",$datalist[$step])) {
					}else{
						$check_error[$step]=1;
					}
				}else{
					$check_error[$step]=2;
				}
			}
						
					
			//身分證字號 8欄
			if($step ==8){
				if($datalist[17] == '0' || $datalist[17] =='1' || $datalist[17]=='2' || $datalist[17]=='5' || $datalist[17]=='6' || $datalist[17]=='12'|| $datalist[17]=='13'){
					$check_stdidnumber=checkid($datalist[$step]);
				}else if($datalist[17]=='3' || $datalist[17]=='4' || $datalist[17]=='7' || $datalist[17]=='8' || $datalist[17]=='9' || $datalist[17]=='10' || $datalist[17]=='11'){
					$check_stdidnumber= true;
				}else $check_stdidnumber==false;				
					if ($datalength!=0){
						if ($check_stdidnumber==true) {
							;	
						}else{
							$check_error[$step]=1;
						}
					}else{
							$check_error[$step]=2;
					}
			}
					
			
			//電子郵件信箱9欄
			if($step ==9){
				if ($datalength!=0){
					;
				}else{
					$check_error[$step]=2;
				}
			}
			
			//連絡電話 12
			if($step ==12){			
				if ($datalength!=0){
					if (preg_match("/^[0-9]+$/", $datalist[$step])==1) {	
						;	
					}else{
						$check_error[$step]=1;
					}
				}else{
					$check_error[$step]=2;
				}
			}
			
			//具幼稚園師資職前教育課程修課資格13欄
			//具國民小學師資職前教育修課資格14欄
			//具中等學校師資職前教育修課資格15欄
			//具特殊教育師資職前教育課程修課資格16欄
			if($step ==13  ||$step ==14  ||$step ==15 ||$step ==16){
				if ($datalength!=0){
					if (eregi("^([0-4]{1})$",$datalist[$step])) {
						;	
					}else{
						$check_error[$step]=1;
					}
				}else{
					$check_error[$step]=2;
				}
			}
			//出生年18欄	
			//echo $step.'<br>';
			if($step ==18){
				if ($datalength!=0){
					if (eregi("^([0-9]{4})$",$datalist[$step])) {
						;	
					}else{
						$check_error[$step]=1;
					}
				}else{
					$check_error[$step]=2;
				}
			}
			//原住民別19
			//性別20
			if($step ==19 || $step ==20){
				if ($datalength!=0){
					if (eregi("^[1-2]{1}$",$datalist[$step])) {
						;	
					}else{
						$check_error[$step]=1;
					}
				}else{
					$check_error[$step]=2;
				}
			}
		} 
		//var_dump ($datalist);
		//echo '<br>';  
		//var_dump ($check_error);exit;
		
		// for step 結束 檢查欄位結束-------------------------------------------------------------------
		
		$count_error=0;
		//$newcid = createnewcid($datalist[8]); 
		
		if($datalist[17] == 0 || $datalist[17] ==1 || $datalist[17]==2 || $datalist[17]==5 || $datalist[17]==6){
				$newcid = createnewcid($datalist[8]); 
			}else if($datalist[17]==3|| $datalist[17]==4 || $datalist[17]==7 || $datalist[17]==8){
				$newcid = $datalist[8]; 
			}
		//echo $newcid;exit;
		
		$value='';
		for($step =2;$step <=20;$step++){
			$count_error=$count_error+$check_error[$step];
			
			//產出SQL Command
			$value.= "'".$datalist[$step]."'";
			if($step!=20){
				$value.= ",";
			}
			else if($step==20){
				$value.= ",'".$newcid."'";
			}	
		}
		///echo $value;
		//echo  'count_error : '.$count_error .'</br>';exit;

		if ($count_error>0){
					
				// 顯示欄位錯誤訊息開始-------------------------------------------------------------------
				
				if ($i == 4){
				$error_str = "<table width=1500' align='center'  cellpadding='0' cellspacing='0' border='1'>";
				$error_str .= "<tr bgcolor=#DCE2EE>";

				$error_str .= "	<td align=\'center\'><b>學校代碼</b></td>
								<td align=\'center\'><b>學號</b></td> 
								<td align=\'center\'><b>科系所代碼</b></td> 
								<td align=\'center\'><b>科系中文名稱</b></td> 
								<td align=\'center\'><b>學制別</b></td> 
								<td align=\'center\'><b>中文姓名</b></td> 
								<td align=\'center\'><b>身分證字號</b></td> 
								<td align=\'center\'><b>電子郵件信箱</b></td> 
								<td align=\'center\'><b>戶籍郵遞區號</b></td> 
								<td align=\'center\'><b>戶籍地址</b></td> 
								<td align=\'center\'><b>連絡電話</b></td> 
								<td align=\'center\'><b>具幼稚園師資職前教育課程修課資格</b></td> 
								<td align=\'center\'><b>具國民小學師資職前教育修課資格</b></td> 
								<td align=\'center\'><b>具中等學校師資職前教育修課資格</b></td> 
								<td align=\'center\'><b>具特殊教育師資職前教育課程修課資格</b></td>
								<td align=\'center\'><b>外加名額</b></td> 
								<td align=\'center\'><b>出生年</b></td> 
								<td align=\'center\'><b>原住民別</b></td> 
								<td align=\'center\'><b>性別2</b></td> ";
				$error_str .= "</tr>";		 
				}
				
				if ($i%4==1)
					$error_str .= "<tr bgcolor=#FFFFFF >";
				else
					$error_str .= "<tr bgcolor=#F5F8FD >";

				for($step =2;$step <=20;$step++){
					if ($check_error[$step]==1){
						$error_str .= "<td><font color=red>".$datalist[$step]."</font></td>";
						}
					else if ($check_error[$step]==2){
						$error_str .= "<td><font color=red>無資料</font></td>";
						}
					else{
						$error_str .= "<td>".$datalist[$step]."</td>";
						}
				}
				if ($i == $Import_NewRowCount){
					$error_str .= "</table>";
				}

				$error_row +=1; //錯誤計數

	// 顯示欄位錯誤訊息結束-------------------------------------------------------------------				
		}
		else{
			if ($i == 4){
				$error_str = "<table width=1500' align='center'  cellpadding='0' cellspacing='0' border='1'>";
				$error_str .= "<tr bgcolor=#DCE2EE>";
				$error_str .= "	<td align=\'center\'><b>學校代碼</b></td>
								<td align=\'center\'><b>學號</b></td> 
								<td align=\'center\'><b>科系所代碼</b></td> 
								<td align=\'center\'><b>科系中文名稱</b></td> 
								<td align=\'center\'><b>學制別</b></td> 
								<td align=\'center\'><b>中文姓名</b></td> 
								<td align=\'center\'><b>身分證字號</b></td> 
								<td align=\'center\'><b>電子郵件信箱</b></td> 
								<td align=\'center\'><b>戶籍郵遞區號</b></td> 
								<td align=\'center\'><b>戶籍地址</b></td> 
								<td align=\'center\'><b>連絡電話</b></td> 
								<td align=\'center\'><b>具幼稚園師資職前教育課程修課資格</b></td> 
								<td align=\'center\'><b>具國民小學師資職前教育修課資格</b></td> 
								<td align=\'center\'><b>具中等學校師資職前教育修課資格</b></td> 
								<td align=\'center\'><b>具特殊教育師資職前教育課程修課資格</b></td>
								<td align=\'center\'><b>外加名額</b></td> 
								<td align=\'center\'><b>出生年</b></td> 
								<td align=\'center\'><b>原住民別</b></td> 
								<td align=\'center\'><b>性別2</b></td> ";
				$error_str .= "</tr>";	 
			$error_str .= "<tr>	<td colspan =19 align=\'center\'><b>資料無誤</b></td> </tr>";

			}
						
			if ($i == $Import_NewRowCount){
				$error_str .= "</table>";
			}
		
			$sql_exist = new mod_db();
			$sql_str ="SELECT newcid FROM $tb_name WHERE newcid = '$newcid'";
			//echo $sql_str;exit;
			$obj_ck_exist=$sql_exist->objects("$sql_str");
			$sql_exist->disconnect();	
			//memo = 1 新增  memo = 2 修改  memo = 3 刪除 memo = 4 完整名單 obj_ck_exist 已存在相同名單進行upadte						

			if($obj_ck_exist->newcid ==''){
			//echo '增加'.'</br>' ;
				$InsertStr.="Insert into $tb_name ([uid],[stdid],[udepcode],[udepname],[stdschoolsys],[stdname],[stdidnumber],[stdemail],[stdregzipcode],[stdregaddr],[tel],[childprogram],[priprogram],[secprogram],[speprogram],[other],[birthyear],[aboriginal],[gender],[newcid]) 
							values (".$value.")\n ;";	
				$InsertStr.="Insert into [tted_edu_102].[dbo].[fieldwork102_id] (stdidnumber,newcid) Values ('".$datalist[8]."','".$newcid."')\n ;";		
				$InsertStr.="Insert into [tted_edu_102].[dbo].[fieldwork102_pstat](newcid) Values ('".$newcid."')\n ;";				
				$insert_count +=1;		
			//	echo $InsertStr;exit;
			}elseif($obj_ck_exist->newcid == $newcid){
			//echo '更新'.'</br>' ;
				$InsertStr.= "UPDATE $tb_name SET 	[uid]='".$datalist[2]."',
													[stdid]='".$datalist[3]."',
													[udepcode]='".$datalist[4]."',
													[udepname]='".$datalist[5]."',
													[stdschoolsys]='".$datalist[6]."',
													[stdname]='".$datalist[7]."',
													[stdidnumber]='".$datalist[8]."',
													[stdemail]='".$datalist[9]."',
													[stdregzipcode]='".$datalist[10]."',
													[stdregaddr]='".$datalist[11]."',
													[tel]='".$datalist[12]."',
													[childprogram]='".$datalist[13]."',
													[priprogram]='".$datalist[14]."',
													[secprogram]='".$datalist[15]."',
													[speprogram]='".$datalist[16]."',
													[other]='".$datalist[17]."',
													[birthyear]='".$datalist[18]."',
													[aboriginal]='".$datalist[19]."',
													[gender]='".$datalist[20]."'
													 where newcid='".$newcid."'\n ;";											
				$insert_count +=1;				
			}elseif($memo == "3"){
			//echo '刪除'.'</br>' ;
				$InsertStr.="delete from $tb_name where newcid='".$newcid."'\n";
				$InsertStr.="delete from [tted_edu_102].[dbo].[fieldwork102_id] where newcid='".$newcid."'\n";
				$InsertStr.="delete from [tted_edu_102].[dbo].[fieldwork102_pstat] where newcid='".$newcid."'\n";
				$delete_count +=1;
			}	
		}			
	}
	
	//echo $InsertStr;exit;
	//完成 excel 檔案讀行 	
	if ($error_row>0){
			$status_db_str.="您有錯誤資料共 ".$error_row." 筆資料";
			$error_pstr ="<div><font color=red>※".$status_db_str."</font></div>";
?>				
			<script language="javascript">
				alert('您匯入的資料有誤，請參考頁面上錯誤訊息修正');
			</script>
<?php			
		}
		elseif ($error_row==0 && strlen($InsertStr)==0){
?>
			<script language="javascript">
				alert('匯入錯誤:檔案寫入錯誤');
			</script>
<?php		
		}
				
	if (strlen($InsertStr)!=0){
		if($memo == "3"){
?>
			<script language="javascript">
				var delete_str = "目前錯誤資料共 "+ <?php echo $error_row ?> + "筆，欲【刪除】資料共" + <?php echo $delete_count ?> + "筆資料";
				alert(delete_str);
			</script>
<?php			
		}else{
?>
			<script language="javascript">
				var str = "目前錯誤資料共 "+ <?php echo $error_row ?> + "筆，欲【新增/更新】資料共" + <?php echo $insert_count ?> + "筆資料";
				alert(str);
					// if(window.confirm(str) == false)
					// {
					// location.replace('UploadData_Hedu.php');
					// }
			</script>
<?php						
			}
			$sql = new mod_db();	
			$InsertStr = str_replace("?","'",$InsertStr);	
			$import_query=$sql->query($InsertStr); //執行excel 資料寫入DB
			$sql->disconnect();
	
			if (!$import_query){
				if($memo == "3"){
?>
				<script language="javascript">
					alert('資料刪除失敗');
				</script>
<?php					
				}else{
?>
				<script language="javascript">
					alert('資料新增失敗');
				</script>
<?php					
				}
			}else{
				if($memo == "3"){
					$status_db_str .="您已成功刪除 ".$delete_count." 筆資料";
					$success_pstr ="<div><font color=red>※".$status_db_str."</font></div>";
?>				
					<script language="javascript">
						alert('資料刪除成功');
					</script>
<?php					
				}else{
					$status_db_str .="您已成功新增 ".$insert_count." 筆資料";
					$success_pstr ="<div><font color=red>※".$status_db_str."</font></div>";
?>				
					<script language="javascript">
						alert('資料新增成功');
					</script>
<?php					
				}
			}			
		}
		//寫入檔案中進行備份 Server
			$query_name = $sch_id."_".$now."_query_".".sql";
			$query_file_name = "$serverdir".$query_name;			
			$f = fopen($query_file_name,"a+");
			fwrite($f,$InsertStr);
			fclose($f);	
		//寫入檔案中進行log
			$filename = "upfieldwork102_userinfoData_2003.log";
			$f=fopen("/home/leon/data/edu/log/$filename","a+");
			$fstring= "user=".$name." 執行 sql=".$InsertStr." ip=".$ip." date=".$now."\n";
			fwrite($f,$fstring);
			fclose($f);	
			
		//寫入檔案中進行log Server	
			$sql_log = new mod_db();
			$q_string_sname = "INSERT INTO [tted_edu_102].[dbo].[log_102] ([function] ,[school] ,[name] ,[account] ,[type],[nasdir] ,[serverdir],[filename] ,[ip])
								VALUES ('$funname','$sch_id' ,'$name','$account','0','$nsadir','$serverdir','$query_name' ,'$ip')";	 
			
			$sql_log->query("$q_string_sname");	
		//更新upload status 
			$q_string_sname = "UPDATE [tted_edu_102].[dbo].[upload102] SET status ='$status_db_str' WHERE filename = '$sfilename' ";	 
			$sql_log->query("$q_string_sname");	
			$sql_log->disconnect();  	
			

			
			}else if ($validation == 2){
?>
				<script language="javascript">
					alert('請選擇檔案');
				</script>
<?php			
			}else{
?>
				<script language="javascript">
					alert('資料上傳失敗,請重新上傳');
				</script>
<?php						
			}
			


  	// 自動post成功或錯誤訊息，onload="document.form1.submit()"
?>	
	<form id="form1" name="form1" method="post" action="fieldwork102.php">
		<input type="hidden" name="post_arr[0]" value="<?php echo $error_pstr;?>"/>
		<input type="hidden" name="post_arr[1]" value="<?php echo $error_str;?>"/>	
		<input type="hidden" name="post_arr[2]" value="<?php echo "總共:".$success_pstr;?>"/>		
	</form>	
</body>
</html>

