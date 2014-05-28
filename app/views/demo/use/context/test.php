<?php
$user = Auth::user();
$csrf_token = csrf_token();
$dddos_token = dddos_token();

echo 'My file id:'.$fileAcitver->file_id;

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

/*
| 被請求者 取得匯入的檔案列表
*/
$virtualFile = VirtualFile::find($fileAcitver->file_id);
foreach($virtualFile->files as $file)
	echo $file->title.'<br />';



echo '<br /><br />';


/*
| 請求者 取得匯入的檔案列表
*/
$user->get_file_provider()->get_request();


$has_requester = Requester::where('id_requester','=',$fileAcitver->file_id)->exists();
/*
| 送出請求
*/
if( is_null($virtualFile->requester) && !$has_requester ){	
	
	$groups = Group::with(array('docs_target' => function($query) use ($fileAcitver) {
		$query->leftJoin('auth_requester','docs.id','=','auth_requester.id_doc')->where('auth_requester.id_requester',$fileAcitver->file_id);
	}))->where('id_user',$user->id)->get();

	if( $groups->count()>0 ){
		echo Form::open(array('url' => $user->get_file_provider()->get_active_url($intent_key, 'request_to'), 'files' => true));
		foreach($groups as $group){	
			if( $group->docs_target->count()<1 ){
				echo Form::checkbox('group[]', $group->user->id, true);
				echo $group->user->username;
			}
		}
		echo Form::submit('Request!');
		echo Form::hidden('intent_key', $intent_key);
		echo Form::hidden('_token1', $csrf_token);
		echo Form::hidden('_token2', $dddos_token);
		echo Form::close();
	}
}

/*
| 停止請求
*/
if( $has_requester ){
	
	$requesters = Requester::with('doc')->where('id_requester','=',$fileAcitver->file_id)->get();
	echo Form::open(array('url' => $user->get_file_provider()->get_active_url($intent_key, 'request_end'), 'files' => true));	
	foreach($requesters as $requester){	
		echo Form::checkbox('doc[]', $requester->id_doc, true);
		echo $requester->doc->user->username;
	}
	echo Form::submit('Request end!');
	echo Form::hidden('intent_key', $intent_key);
	echo Form::hidden('_token1', $csrf_token);
	echo Form::hidden('_token2', $dddos_token);
	echo Form::close();
}



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
	
	var_dump($workSheet->toArray(null,true,true,true));
	
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


