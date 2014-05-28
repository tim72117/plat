<?

##########################################################################################
#
# filename: gra102.php
# function: 上傳101學年度國三畢業生基本資料	
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

?>


<script src="<?=asset('js/tigra_tables.js')?>"></script>

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
    	<a href="../../function/download_file.php?file=101gra_form.xls">表格下載</a> 

<? 
$user = auth::user();

//表單資料
$intent_key = $fileAcitver->intent_key;
echo Form::open(array('url' => $user->get_file_provider()->get_active_url($intent_key, 'import'), 'files' => true, 'method' => 'post'));
echo Form::file('file_upload');
echo Form::submit('上傳檔案');
echo Form::hidden('intent_key', $intent_key);
echo Form::hidden('_token1', csrf_token());
echo Form::hidden('_token2', dddos_token());
echo Form::close();

//上傳判斷
if( Session::has('upload_file_id') ){ 
//$aaa='1';
//if ($aaa = '1'){

	$id_doc = Session::get('upload_file_id');	
	//$id_doc = 192;
	$doc = DB::table('files')->where('id',$id_doc)->pluck('file');

	$reader = PHPExcel_IOFactory::createReaderForFile( storage_path(). '/file_upload/'. $doc );
	$reader->setReadDataOnly(true);
	$objPHPExcel  = $reader->load( storage_path(). '/file_upload/'. $doc );
	$workSheet = $objPHPExcel->getActiveSheet();
	//var_dump($workSheet->toArray(null,true,true,true));
	//echo '<pre>', print_r($workSheet->toArray(null,true,true,true), true), '</pre>';
	
		//取得行列值
		$RowHigh = $workSheet->getHighestRow(); //資料筆數
		$ColHigh = alpha2num($workSheet->getHighestColumn()); //欄位數目
		//echo "RowHigh = ".$RowHigh." , ColHigh = ".$ColHigh."</br>";

	$value = array();//上傳用暫存陣列
	$data = ($workSheet->toArray(null,true,true,true));

	//將每筆資料存入暫存陣列並上傳
	for($i=2;$i<=$RowHigh;$i++){ // i = 1為索引值，不需存入
		for ($j=0;$j<=$ColHigh;$j++){
			$value[$j] = $data[$i][num2alpha($j)];
				//echo "【".$value[$j]."】"; 
	   	}
		$value[$j+1] = createnewcid($value[2]); 
		//【資料內容】：0 = $shid; 1 = name ; 2 = stdidnumber ; 3 = sex; 5 = newcid;

//檢查內容


//寫入資料			
$insert = DB::insert('insert into use_103.dbo.gra103_userinfo (shid,name,sex,newcid) values (?,?,?,?)',array($value[0],$value[1],$value[3],$value[5]));//gra103_userinfo
$insert = DB::insert('insert into use_103.dbo.gra103_id (stdidnumber,newcid) values (?,?)',array($value[2],$value[5]));//gra103_id	
$insert = DB::insert('insert into use_103.dbo.gra103_pstat (newcid) values (?)',array($value[5]));//gra103_pstat
	}


}else{
	
	if( $errors )
		echo implode('、',array_filter($errors->all()));
}

?>

	</td>
  </tr>	
  	
<?php

//列出已上傳的名單
$virtualFile = VirtualFile::find($fileAcitver->file_id);
$i=1;
foreach($virtualFile->files as $file){

		if ($i==1){
?>
  <tr id="gen_content">
	<td colspan="8" class="header1" align="center" style="padding-left:10px;border-bottom:1px solid black;border-left:1px solid black;">已上傳的名單</td>
  </tr>

<?php
	}
?>
  <tr id="gen_content">
	<td align="left" style="padding-left:10px;border-bottom:1px solid black;border-left:1px solid black;"><?php 
	
		echo "檔案".$i."　檔名：".$file->title."　上傳於：".$file->ctime.'<br />';
?></td>
    
    
  </tr>
<?php
		$i++;
  }
?>
</table>
