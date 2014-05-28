<?

//$user = auth::user();
##########################################################################################
#
# filename: gra102.php
# function: 上傳101學年度國三畢業生基本資料	
#
##########################################################################################

$now=date("Ymd-His");
	//echo $user->id;

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
    	<a href="../../function/download_file.php?file=101gra_form.xls">表格下載</a> <!-- /

<? 
$intent_key = $fileAcitver->intent_key;
echo Form::open(array('url' => $user->get_file_provider()->get_active_url($intent_key, 'import'), 'files' => true, 'method' => 'post'));
//'method' => 'post'
echo Form::file('file_upload');
echo Form::submit('上傳檔案');
echo Form::hidden('intent_key', $intent_key);
echo Form::hidden('_token1', csrf_token());
echo Form::hidden('_token2', dddos_token());
echo Form::hidden('filetype', '9');
echo Form::close();

if( Session::has('upload_file_id') ){ 
//$aaa='1';
//if ($aaa = '1'){

//測式////////////////////////////////////////////////////

$value_array =array();
$j=0;
$k=0;

	$id_doc = Session::get('upload_file_id');	
//	$id_doc = 192;
	$doc = DB::table('files')->where('id',$id_doc)->pluck('file');
	
	$reader = PHPExcel_IOFactory::createReaderForFile( storage_path(). '/file_upload/'. $doc );
	$reader->setReadDataOnly(true);
	$objPHPExcel  = $reader->load( storage_path(). '/file_upload/'. $doc );
	
	$workSheet = $objPHPExcel->getActiveSheet();
	
	//var_dump($workSheet->toArray(null,true,true,true));
	echo "</br>"."</br>";
	$abc = ($workSheet->toArray(null,true,true,true));
	
		$RowHigh = $workSheet->getHighestRow(); // row數 = 3
		$ColHigh = $workSheet->getHighestColumn(); // col數 = D
				
		function alpha2num($ColHigh)  //將英文索引轉為數字(A=>0、B=>1、AA=>26...)
		{
			$l = strlen($ColHigh);
			$n = 0;
			for($i = 0; $i < $l; $i++)
				$n = $n*26 + ord($a[$i]) - 0x40;
			return $n-1;
		
		}		
		
		echo "RowHigh = ".$RowHigh." , ColHigh = ".$ColHigh."</br>";

	echo "</br>"."</br>";
	//echo $abc[3]["A"]."---------------";
	echo "</br>"."</br>";
	echo '<pre>', print_r($workSheet->toArray(null,true,true,true), true), '</pre>';
	echo "</br>"."</br>";
	
	
		
	
	
	//var_export($workSheet->toArray(null,true,true,true));
	//echo $value_array[1]["A"]."</br>"."</br>";
	foreach ($workSheet->getRowIterator() as $row) {
		

		$cellIterator = $row->getCellIterator();
		//echo "test1"."</br>"."</br>";
		$cellIterator->setIterateOnlyExistingCells(false);
		//echo "test2"."</br>"."</br>";	
		
		foreach ($cellIterator as $cell){
		//echo "test3"."</br>"."</br>";

		echo $cell->getValue();
			//$value_array[$j][$k] = $cell->getValue();
			//echo "j".$j."k".$k."【".$value_array[$j][$k]."】"."</br>";
			$k++;
		}
				
				/*	if(empty($value_array[$j][1])){
					$RowHigh = $j-1;
					$ColHigh = $k-1;
						break;}
								
					$j++;
					$k=0;*/
		//echo "test4"."</br>"."</br>";
			
		}

/*for ($l=0;$l<$j;$l++){
	for ($m=0;$m<$k;$m++){
	echo $value_array[$l][$m]."\t";}
	echo "</br>";}*/


//$insert = DB::insert('insert into use_102.dbo.upload102 (school,name,account,memo,filename,type,ip) values (?,?,?,?,?,?,?)', 												array($school, 'name','xuabjin',1,$name,9,$ip));

/////////////////

/*$id_doc = Session::get('upload_file_id');	
	$doc = DB::table('doc')->where('id',$id_doc)->pluck('file');
	
	$reader = PHPExcel_IOFactory::createReaderForFile( storage_path(). '/file_upload/'. $doc );
	$reader->setReadDataOnly(true);
	$objPHPExcel  = $reader->load( storage_path(). '/file_upload/'. $doc );
	
	$workSheet = $objPHPExcel->getActiveSheet();
	
	var_dump($workSheet->toArray(null,true,true,true));
	
	foreach ($workSheet->getRowIterator() as $row) {
		$cellIterator = $row->getCellIterator();
		$cellIterator->setIterateOnlyExistingCells(false);
		foreach ($cellIterator as $cell){
			echo $cell->getValue().'-';
		}
	}*/
}else{
	
	if( $errors )
		echo implode('、',array_filter($errors->all()));
}






/////////////////

//}

?>

	</td>
  </tr>	
  	
<?php
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
	//echo "檔案".$i."　上傳於".$results->uploadtime."___".$results->filename."。";
	
		echo "檔案".$i."　檔名：".$file->title."　上傳於：".$file->ctime.'<br />';
?></td>
    
    
  </tr>
<?php
		$i++;
  //}
  }
?>
</table>
