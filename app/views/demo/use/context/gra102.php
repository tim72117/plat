<?

##########################################################################################
#
# filename: gra102.php
# function: 上傳103學年度國三畢業生基本資料	
#
##########################################################################################

$now=date("Ymd-His");
	
function num2alpha($n)  //數字轉英文(0=>A、1=>B、26=>AA...)
{   for($r = ""; $n >= 0; $n = intval($n / 26) - 1)
        $r = chr($n%26 + 0x41) . $r; 
    return $r; }
	
function alpha2num($a){  //英文轉數字(A=>0、B=>1、AA=>26...)
    $l = strlen($a);
    $n = 0;
    for($i = 0; $i < $l; $i++)
        $n = $n*26 + ord($a[$i]) - 0x40;
    return $n-1;}
	
function checkid($id){
	$id = strtoupper($id);
	//建立字母分數陣列
	$headPoint = array(
		'A'=>1,'I'=>39,'O'=>48,'B'=>10,'C'=>19,'D'=>28,
		'E'=>37,'F'=>46,'G'=>55,'H'=>64,'J'=>73,'K'=>82,
		'L'=>2,'M'=>11,'N'=>20,'P'=>29,'Q'=>38,'R'=>47,
		'S'=>56,'T'=>65,'U'=>74,'V'=>83,'W'=>21,'X'=>3,
		'Y'=>12,'Z'=>30
	);
	//建立加權基數陣列
	$multiply = array(8,7,6,5,4,3,2,1);
	//檢查身份字格式是否正確
	if (preg_match("/^[a-zA-Z][1-2][0-9]+$/",$id) AND strlen($id) == 10){
		//切開字串
		$len = strlen($id);
		for($i=0; $i<$len; $i++){
			$stringArray[$i] = substr($id,$i,1);
		}
		//取得字母分數
		$total = $headPoint[array_shift($stringArray)];
		//取得比對碼
		$point = array_pop($stringArray);
		//取得數字分數
		$len = count($stringArray);
		for($j=0; $j<$len; $j++){
			$total += $stringArray[$j]*$multiply[$j];
		}
		//計算餘數碼並比對
		$last = (($total%10) == 0 )?0:(10-($total%10));
		if ($last != $point) {
			return false;
		} else {
			return true;
		}
	}else{
	   return false;
	}
}

function checkname($name){
	if (preg_match("/^[\x{4e00}-\x{9fa5}]{2,5}$/u",$name)) {
		return true;	
	}else{
		return false;
	}
}

function checkstdid($sch_id){
	if (preg_match("/[0-9]{6}/",$sch_id)) {
		return true;	
	}else{
		return false;
	}
}
?>


<script src="<?=asset('js/tigra_tables.js')?>"></script>
<link href="<?=asset('demo/use/css/use100_content.css')?>" rel="stylesheet" type="text/css">

<table cellpadding="3" cellspacing="1" border="0" width="100%">
	<tr>
	  <td class="header2">103學年度國三畢業生基本資料</td>
	</tr>
</table>
<!--    <td class="header1" align="left" style="display:block" border="0" width="50%">-->
<table id="header2" align="left" style="display:block" border="0" >
	<tr bgcolor="#CAFFCA">
	  <td class="header1" colspan="8" align="center" >上傳103學年度國三畢業生基本資料</td>
  </tr>
  <tr id="gen_content">
    <td class="header3" colspan="8" align="left" style="padding-left:10px;border-bottom:0px solid none;border-left:0px solid black;">相關檔案: 
    	<a href="../../function/download_file.php?file=101gra_form.xls">表格下載</a> 

<? 

$user = auth::user();
$csrf_token = csrf_token();
$dddos_token = dddos_token();

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

//上傳判斷
$r=1;
if( Session::has('upload_file_id') ){ 

	$id_doc = Session::get('upload_file_id');	
	$doc = DB::table('files')->where('id',$id_doc)->pluck('file');

	$reader = PHPExcel_IOFactory::createReaderForFile( storage_path(). '/file_upload/'. $doc );
	$reader->setReadDataOnly(true);
	$objPHPExcel  = $reader->load( storage_path(). '/file_upload/'. $doc );
	$workSheet = $objPHPExcel->getActiveSheet();

	
		//取得行列值
		$RowHigh = $workSheet->getHighestRow(); //資料筆數
		$ColHigh = alpha2num($workSheet->getHighestColumn()); //欄位數目

	$value = array();//上傳用暫存陣列
	$error_msg = array();
	$data = ($workSheet->toArray(null,true,true,true));

	//檢查每筆資料並存入上傳陣列
	for($i=2;$i<=$RowHigh;$i++)
	{
		// i = 1為索引值，不需存入
		$error_flag = 0;
		for ($k=0;$k<=$ColHigh;$k++) if(!empty($error_msg[$k])) $error_msg[$k]="";//清空錯誤代碼
		for ($j=0;$j<=$ColHigh;$j++){
			$value[$j] = $data[$i][num2alpha($j)];

			//檢查內容
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
						if (check_my_idnumber($value[2])==false) {
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
		
		if ((!empty($value[2])) && (checkid($value[2]))) $value[4] =createnewcid($value[2]); 
		

//檢查內容

if ($error_flag == 1){
	if ($r==1){ 
	?>
<table cellpadding="3" cellspacing="0" border="1" width="100%">
	<tr bgcolor="#CAFFCA">
		<td colspan="8" align="center">以下資料有誤，請協助修改後重新上傳</td>
	</tr> 
</table>
	<table id="all_table" width="100%" cellpadding=3 cellspacing=0 border=1 align="left">
		<tr bgcolor="#E4E4E4">
		  <th width="10%" class="title" scope="col">學校代碼</th>
		  <th width="10%" class="title" scope="col">學生姓名</th>
		  <th width="15%" class="title" scope="col">身分證字號</th>
		  <th width="5%" class="title" scope="col">性別</th>
		  <th width="60%" class="title" scope="col">錯誤資訊</th>
		</tr>
<?
	  $r++;}
			
				echo "<tr>";
				
				for ($j=0;$j<=$ColHigh;$j++)
					if (empty($error_msg[$j])) echo "<td scope=col>".$value[$j]."</td>\n";
					else echo '<td scope=col  bgcolor="#FFFFCC">'.'<p>'.'<font color="red">'.$value[$j].'</p>'.'</font>'."</td>\n";

				echo "<td scope=col>";
				for ($k=0;$k<=$ColHigh;$k++) {
					if(!empty($error_msg[$k])) echo $error_msg[$k];
				}
				echo "</td>\n";
	}
			else{
			//更新或寫入資料
			$DB = DB::table('use_103.dbo.gra103_userinfo')
							  ->where('stdidnumber', $value[2])
							  ->get();
				if ($DB) {		
			//gra103_userinfo
			$update = DB::table('use_103.dbo.gra103_userinfo')
						->where('stdidnumber', $value[2])
						->update(array('shid' => $value[0],'name' => $value[1],'sex' => $value[3],'newcid' => $value[4],'stdidnumber' => $value[2],'id_user' => $user->id));
			}

	else{
		//gra103_userinfo	
		
/////////////////// test /////////////////////		
$insert = DB::table('use_103.dbo.gra103_userinfo')->insert(
    array('shid' => $value[0],'name' => $value[1] , 'sex' => $value[3],'newcid' => $value[4],'stdidnumber' => $value[2],'id_user' => $user->id)
);
		
//		$insert = DB::insert('insert into use_103.dbo.gra103_userinfo (shid,name,sex,newcid,stdidnumber,id_user) values (?,?,?,?,?,?)',array($value[0],$value[1],$value[3],$value[4],$value[2],$user->id));
		}
							}
			}


}else{
	
	if($errors){
		echo implode('、',array_filter($errors->all()));}

}

//列出已上傳的名單
$virtualFile = VirtualFile::find($fileAcitver->file_id);

$i=1;
	foreach($virtualFile->hasFiles as $file){
		if ($i==1){
?>
	<tr bgcolor="#CAFFCA">
	<td colspan="8" class="header1" align="center" style="padding-left:10px;border-bottom:0px solid black;border-left:0px solid black;">已上傳的名單</td>
  </tr>

<?php
	}
?>
  <tr id="gen_content">
	<td colspan="8" class="header2" align="center" style="padding-left:10px;border-bottom:0px solid black;border-left:0px solid black;"><?php 
		echo "檔案".$i."　檔名：".$file->title."　上傳於：". date('Y-m-d h:i:s A',strtotime($file->ctime)).'<br />';
?>
	</td>
	 </tr>
<?php
		$i++;
  }
?>
</table>
