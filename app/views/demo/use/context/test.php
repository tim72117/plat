<?php

echo 'test<br />';



//var_dump($group->users);
//var_dump(Group::find(2)->users->count());

//$requester = Requester::with('doc.requester')->where('id_requester','=',$fileAcitver->file_id)->get();
//var_dump($requester);
//var_dump($requester[0]->doc->id_user);



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
foreach($virtualFile->hasFiles as $file)
	echo $file->title.'<br />';



echo '<br /><br />';

/*
| 請求者
*/
if( is_null($virtualFile->requester) ){	
	/*
	| 請求者 取得匯入的檔案列表
	*/
	$user->get_file_provider()->get_request();
	
	/*
	| 送出請求
	*/
	$preparers = Requester::with('docPreparer.user')->where('requester_doc_id','=',$fileAcitver->file_id)->get();
	$preparers_user_id = array_pluck(array_pluck($preparers->toArray(),'doc_preparer'),'user_id');
	$group = Group::with('users')->where('id','2')->get();
	echo $preparers->count();

	/*
	$groups = Group::with(array('docsTarget' => function($query) use ($fileAcitver) {
		$query->leftJoin('auth_requester','docs.id','=','auth_requester.id_doc')->where('auth_requester.id_requester',$fileAcitver->file_id);
	}))->where('id_user',$user->id)->get();
	 */
	if( $group[0]->users->count() > 0 ){
		echo Form::open(array('url' => $user->get_file_provider()->get_active_url($intent_key, 'request_to'), 'files' => true));
		foreach($group[0]->users as $user_in_group){
			if( !in_array($user_in_group->id, $preparers_user_id) ){				
				echo Form::checkbox('group[]', $user_in_group->id, true);
				echo $user_in_group->username;
			}
		}
		echo Form::submit('Request!');
		echo Form::hidden('intent_key', $intent_key);
		echo Form::hidden('_token1', $csrf_token);
		echo Form::hidden('_token2', $dddos_token);
		echo Form::close();
	}
	


	if( $preparers->count() > 0 ){
		/*
		| 停止請求
		*/
		echo Form::open(array('url' => $user->get_file_provider()->get_active_url($intent_key, 'request_end'), 'files' => true));	
		foreach($preparers as $preparer){	
			echo Form::checkbox('doc[]', $preparer->preparer_doc_id, true);
			echo $preparer->docPreparer->user->username;			
		}
		echo Form::submit('Request end!');
		echo Form::hidden('intent_key', $intent_key);
		echo Form::hidden('_token1', $csrf_token);
		echo Form::hidden('_token2', $dddos_token);
		echo Form::close();
	}
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






