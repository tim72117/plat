<?

##########################################################################################
#
# filename: gra102.php
# function: 上傳103學年度國三畢業生基本資料	
#
##########################################################################################

$fileProvider = app\library\files\v0\FileProvider::make();
	
function num2alpha($n)  //數字轉英文(0=>A、1=>B、26=>AA...)
{   for($r = ""; $n >= 0; $n = intval($n / 26) - 1)
        $r = chr($n%26 + 0x41) . $r; 
    return $r; }
	
function alpha2num($a){  //英文轉數字(A=>0、B=>1、AA=>26...)
    $l = strlen($a);
    $n = 0;
    for($i = 0; $i < $l; $i++)
        $n = $n*26 + ord($a[$i]) - 0x40;
    return $n-1;
}	

function checkname($name){
	if (preg_match("/^[a-zA-Z0-9]$/u",$name)) {
	//if (preg_match("/^[\x{4e00}-\x{9fa5}][‧]{2,5}$/u",$name)) {
		return false;	
	}else{
		return true;
	}
}

function checkstdid($sch_id){
	if (preg_match("/[0-9]{6}/",$sch_id)) {
		return true;	
	}else{
		return false;
	}
}

$user = auth::user();
$error_text = '';
$null_text = '';
$null_row_flag = 0; 

//上傳判斷
if( Session::has('upload_file_id') ){ 

	$id_doc = Session::get('upload_file_id');	
	$doc = DB::table('files')->where('id',$id_doc)->pluck('file');

	$reader = PHPExcel_IOFactory::createReaderForFile( storage_path(). '/file_upload/'. $doc );
	$reader->setReadDataOnly(true);
	$objPHPExcel  = $reader->load( storage_path(). '/file_upload/'. $doc );
	$workSheet = $objPHPExcel->getActiveSheet();

	
		//取得行列值
		$RowHigh = $workSheet->getHighestRow(); //資料筆數
		//if (alpha2num($workSheet->getHighestColumn())!=3) $ColHigh = 3;
		//else $ColHigh = alpha2num($workSheet->getHighestColumn());
		$ColHigh = 3;

	$value = array();//上傳用暫存陣列
	$error_msg = array();//儲存錯誤資訊用陣列
	$null_row = array();// 紀錄空白列資訊
	$s=0;				//

	$data = ($workSheet->toArray(null,true,true,true));

	//檢查每筆資料並存入上傳陣列
	for($i=2;$i<=$RowHigh;$i++)
	{
		// i = 1為索引值，不需存入
		$error_flag = 0;
		for ($k=0;$k<=$ColHigh;$k++) if(!empty($error_msg[$k])) $error_msg[$k]="";//清空錯誤代碼
		for ($j=0;$j<=$ColHigh;$j++){
			
			if (!empty($data[$i][num2alpha($j)])){
			 	$value[$j] = $data[$i][num2alpha($j)];
			}else $value[$j] = '';//當欄位無值時亦將該上傳陣列存入無值

			//檢查資料欄位內容：$value[0] = 學校代碼；$value[1] = 姓名；$value[2] = 身分證字號；$value[2] = 性別代碼
	   		switch($j){
				case '0' : //學校代碼
					if (!empty($value[0])){
						if (checkstdid($value[0])==false) {
							$error_flag = 1;
							$error_msg[$j] = "學校代碼錯誤 ； ";		
						}
						else{
							if(strlen($value[0])!= 6){
							$error_flag = 1;
							$error_msg[$j] = "學校代碼為六碼 ； ";
							}
						}
					}
					else{$error_flag = 1;	
						 $error_msg[$j] = "未填入學校代碼 ； ";}
				break;
				case '1' : //姓名
					if (!empty($value[1])){
						if (checkname($value[1])==false) {
							$error_flag = 1;
							$error_msg[$j] = "姓名非中文 ； ";
							}
						}
					else{$error_flag = 1;
						 $error_msg[$j] = "未填入姓名 ； ";}
				break;
				case '2' : //身分證代碼
					if (!empty($value[2])){
						if (check_id_number($value[2])==false) {
							$error_flag = 1;
							$error_msg[$j] = "身分證字號錯誤 ； ";
							}
						}
					else{$error_flag = 1;
						 $error_msg[$j] = "未填入身分證字號 ； ";}
				break;
				case '3' : // 性別代碼
					if (!empty($value[3])){
						if (($value[3]!=1)&&($value[3]!=2)) {
							$error_flag = 1;
							$error_msg[$j] = "性別代碼錯誤 ； ";		
						}elseif (substr( $value[2],1,1)!=$value[3]){
							$error_flag = 1;
							$error_msg[$j]= "性別代碼與身分證字號不相符 ； ";
							}
						}
					else{$error_flag = 1;
						 $error_msg[$j] = "未填入性別代碼 ； ";
						 }	
				break;
				default:
				}	
		
		}
	
		//檢查身分證字號，無誤則串出Newcid至$value[4]
		if ((!empty($value[2])) && (check_id_number($value[2]))) $value[4] =createnewcid($value[2]); 
		

		//檢查錯誤資訊
		if ($error_flag == 1){
		
			//判斷一筆資料是否皆為空白	
			if ((empty($value[0]))&&(empty($value[1]))&&(empty($value[2]))&&(empty($value[3])))
			{	
				$null_row_flag = 1;
				$null_row[$s] = $i-1; // 將皆為空白的資料序號存入陣列
				$s++;
				
			}
			else
			{
				$error_text .= "<tr>";
	
				for ($j=0;$j<=$ColHigh;$j++)
					if (empty($error_msg[$j])){
						$error_text .= "<td scope=col>".$value[$j]."</td>";
					}else{
						$error_text .= '<td scope=col  bgcolor="#FFFFCC">'.'<p>'.'<font color="red">'.$value[$j].'</p>'.'</font>'."</td>";
					}
	
				$error_text .= "<td scope=col>";
				for ($k=0;$k<=$ColHigh;$k++) {
					if(!empty($error_msg[$k]))
						$error_text .= $error_msg[$k];
				}
				$error_text .= "</td>";
				
				$error_text .= "</tr>";
			}
		}else{
			
			//更新或寫入資料
			$DB = DB::table('use_103.dbo.gra103_userinfo')
						->where('stdidnumber', $value[2])
						->get();
			if ($DB) {		
				//gra103_userinfo
				DB::table('use_103.dbo.gra103_userinfo')
						->where('stdidnumber', $value[2])
						->update(array('shid' => $value[0],'name' => $value[1],'sex' => $value[3],'newcid' => $value[4],'stdidnumber' => $value[2],'id_user' => $user->id));
			}else{
				//gra103_userinfo	
				DB::table('use_103.dbo.gra103_userinfo')
						->insert(array('shid' => $value[0],'name' => $value[1],'sex' => $value[3],'newcid' => $value[4],'stdidnumber' => $value[2],'id_user' => $user->id));	
						
			}
			
		}

}


}else{//if( Session::has('upload_file_id') ){ 

	if($errors){
		echo implode('、',array_filter($errors->all()));}

}

//判斷是否出現空白資料列
if ($null_row_flag == 1) 
{
	if ($s<=5)
	{	
		$null_text .= '<tr><td colspan="8" align="left">※ 第';
		for ($r=0;$r<$s-1;$r++){
			$null_text .= $null_row[$r]."、"; } //第1~($s-1)筆
			$null_text .= $null_row[$r]."筆資料為空白列，請注意。".'</td></tr>';
	}
	else
	{	$null_text .='<tr><td colspan="8" align="left">※ 第';
		for ($s=0;$s<5;$s++){
			$null_text .= $null_row[$s]."、"; } //第1~5筆
			$null_text .= $null_row[$s]."筆及其他數筆資料為空白資料列，請注意。".'</td></tr>';
	}
}
	
?>



<div style="margin:10px 0 0 10px;width:800px">	
<table width="100%" cellpadding="3" cellspacing="3" border="0">
	<tr bgcolor="#CAFFCA">
		<td height="32" colspan="8" align="center" class="header1" >上傳102學年度國三畢業生基本資料</td>
  </tr>
	<tr>
		<td colspan="8" align="left" style="padding-left:10px">相關檔案: 
			<a href="<?=URL::to($fileProvider->download(2))?>">範例表格下載</a><br />
			<?
			//表單資料
			echo "</br>"."</br>";
			$intent_key = $fileAcitver->intent_key;
			echo Form::open(array('url' => $user->get_file_provider()->get_active_url($intent_key, 'import'), 'files' => true));
			echo Form::file('file_upload');
			echo "</br>"."</br>";
			echo Form::submit('上傳檔案');
			echo Form::hidden('intent_key', $intent_key);
			echo Form::hidden('_token1', csrf_token());
			echo Form::hidden('_token2', dddos_token());
			echo Form::close();

			echo "</br>";
			?>		
		</td>
	</tr>
</table>


<? if ($error_text){ ?>

<table width="99%" cellpadding="3" cellspacing="0" border="1">
	<tr bgcolor="#CAFFCA"><td colspan="8" align="center">以下資料有誤，請協助修改後重新上傳</td></tr> 
	<tr bgcolor="#EEEEEE">		
		<th width="10%" class="title" scope="col" align="center">學校代碼</th>
		<th width="10%" class="title" scope="col" align="center">學生姓名</th>
		<th width="15%" class="title" scope="col" align="center">身分證字號</th>
		<th width="10%" class="title" scope="col" align="center">性別</th>
		<th width="55%" class="title" scope="col" align="center">錯誤資訊</th>
	</tr>
<?
	echo $error_text;
    echo $null_text;
?>
</table>
</br>
<?				  }
?>


<table width="100%" cellpadding="3" cellspacing="3" border="0">
	<tr bgcolor="#EEEEEE">	
		<td colspan="8" align="center">已上傳的名單</td>
        
	</tr>
<?
	//列出已上傳的名單
	$virtualFile = VirtualFile::find($fileAcitver->file_id);

	?>

	<?
	$i=1;
	foreach($virtualFile->hasFiles as $file){
		echo '<tr>';
		echo '   <td colspan="8" class="header1" align="left" style="padding-left:10px;border-bottom:0px solid black;border-left:0px solid black;">';
		echo "     檔案".$i."　檔名：".$file->title."　上傳於：". date('Y-m-d h:i:s A',strtotime($file->updated_at)).'<br />';
		echo '   </td>';
		echo '</tr>';
		$i++;
	}
	?>
</table>	
</div>
