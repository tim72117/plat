<?

$funname ="01_download_file";
$downfile = Input::get('file');			

//	$serverdir= "/home/leon/data/edu/data/upload_download/"; 	   // 存放路徑
	$serverdir= "C:/AppServ/www/plat/app/views/demo/use/context/"; // 本機測試
	$filename = $serverdir.$downfile; //dir+downfile

// 寫入log
//DB::insert('insert into tted_edu_102.dbo.log_102 (type , function ,school ,name ,account ,serverdir,filename ,ip) values ('0', '$funname','Input::get('sch_id')','Input::get('name'.)','Input::get('account')','$serverdir','$downfile' ,'$ip')', array(1, 'Dayle'));


$ext = pathinfo($filename, PATHINFO_EXTENSION);//mod for 周家吉20130409
if( $filename == "" ) {
echo "<html><body>未指定檔案名稱!</body></html>";
exit;
} elseif ( ! file_exists( $filename ) ) {
echo "<html><body>找不到檔案!</body></html>";
exit;
};
switch ($ext) {
	 case "pdf": $ctype="application/pdf"; break;
     case 'zip': $ctype="application/zip"; break;
	 case 'rar': $ctype="application/rar"; break;
     case 'doc': $ctype="application/msword"; break;
     case 'xls': $ctype="application/vnd.ms-excel"; break;
	 case 'xlsx': $ctype=" application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"; break;
  default:echo "<html><body>您不可下載這個檔案</body></html>";
  exit();
}

header('Content-Description: File Transfer');
header("Content-Type: $ctype");
header('Content-Disposition: attachment; filename='.basename($filename));
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: cache, must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($filename));
ob_clean();
flush();
readfile($filename);
exit;
?>