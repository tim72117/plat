<?php

echo 'test<br />';

VirtualFile::getRequester()->doc;

/*
				$queries = DB::getQueryLog();
				foreach($queries as $key => $query){
					echo $key.' - ';var_dump($query);echo '<br /><br />';
				}
				
			
 * 
 */	
				



$user = Auth::user();
$csrf_token = csrf_token();
$dddos_token = dddos_token();


echo 'My file id:'.$fileAcitver->file_id.'<br />';
echo $user->project;

/*
| 上傳檔案且匯入檔案到doc
*/
$intent_key = $fileAcitver->intent_key;
echo Form::open(array('url' => $user->get_file_provider()->get_active_url($intent_key, 'import'), 'files' => true));
echo Form::file('file_upload');
echo Form::submit('Click Me!');
echo Form::hidden('intent_key', $intent_key);
echo Form::hidden('_token1', $csrf_token);
echo Form::hidden('_token2', $dddos_token);
echo Form::close();


$virtualFile = VirtualFile::find($fileAcitver->file_id);
if( $virtualFile->user_id==$user->id ){
	/*
	| 被請求者 取得匯入的檔案列表
	*/
	
	echo 'My files:';
	foreach($virtualFile->hasFiles as $file){
		echo $file->title.'<br />';
	}
	
	echo '<br />------------------------------------<br />';
	if( is_null($virtualFile->requester) ){	
		/*
		| 請求者 取得匯入的檔案列表
		*/
		$fileProvider = app\library\files\v0\FileProvider::make();
		$preparers = $virtualFile->preparers;
		echo '<table>';
		foreach($preparers as $preparer){
			foreach($preparer->docPreparer->hasFiles as $file){
				echo '<tr>';					
					echo '<td>'.$preparer->docPreparer->user->username.'</td>';
					echo '<td>'.$file->title.'</td>';
					echo '<td><a href="'.URL::to($fileProvider->download($file->id)).'">下載</a><br /></td>';
					
				echo '</tr>';
			}
		}
		echo '</table>';
		
	}
}


echo '<br /><br />';


/*
| 檔案上傳後執行
*/
if( Session::has('upload_file_id') ){
	$id_doc = Session::get('upload_file_id');	
	$doc = DB::table('files')->where('id',$id_doc)->pluck('file');
	
	$reader = PHPExcel_IOFactory::createReaderForFile( storage_path(). '/file_upload/'. $doc );
	$reader->setReadDataOnly(true);
	$objPHPExcel  = $reader->load( storage_path(). '/file_upload/'. $doc );
	
	$workSheet = $objPHPExcel->getActiveSheet();
	
	echo $workSheet->getHighestColumn();
	//var_dump($workSheet->toArray(null,true,true,true));
	
	foreach ($workSheet->getRowIterator() as $row) {
		$cellIterator = $row->getCellIterator();
		$cellIterator->setIterateOnlyExistingCells(false);
		foreach ($cellIterator as $cell){
			echo $cell->getValue().'-';
		}
	}
}else{
	
	if( $errors )
		echo implode('、',array_filter($errors->all()));
}






