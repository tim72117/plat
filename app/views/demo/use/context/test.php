<?php
$user = Auth::user();
$csrf_token = csrf_token();
$dddos_token = dddos_token();

$intent_key = $fileAcitver->intent_key;
echo Form::open(array('url' => $user->get_file_provider()->get_active_url($intent_key, 'import'), 'files' => true));
echo Form::file('file_upload');
echo Form::submit('Click Me!');
echo Form::hidden('intent_key', $intent_key);
echo Form::hidden('_token1', $csrf_token);
echo Form::hidden('_token2', $dddos_token);
echo Form::close();


$virtualFile = VirtualFile::find($fileAcitver->file_id);
foreach($virtualFile->files as $file)
	echo $file->title.'<br />';



echo '<br /><br />';



$user->get_file_provider()->get_request();



if( is_null($virtualFile->requester) ){
	
	echo $fileAcitver->file_id;
	echo Form::open(array('url' => $user->get_file_provider()->get_active_url($intent_key, 'request_to'), 'files' => true));
	$groups = Group::where('id_user',$user->id)->get();
	foreach($groups as $group){	
		echo Form::checkbox('group[]', $group->user->id, true);
		echo $group->user->username;
	}
	echo Form::submit('Request!');
	echo Form::hidden('intent_key', $intent_key);
	echo Form::hidden('_token1', $csrf_token);
	echo Form::hidden('_token2', $dddos_token);
	echo Form::close();
}


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


