<?php
$user = new app\library\files\v0\User();


$intent_key = $fileAcitver->intent_key;
//echo link_to($user->get_file_provider()->get_active_url($intent_key, 'import'), '標題', $attributes = array(), $secure = null);


echo Form::open(array('url' => $user->get_file_provider()->get_active_url($intent_key, 'import'), 'files' => true));
echo Form::file('file_upload');
echo Form::submit('Click Me!');
echo Form::hidden('intent_key', $intent_key);
echo Form::hidden('_token1', csrf_token());
echo Form::hidden('_token2', dddos_token());
echo Form::close();


if( Session::has('upload_file_id') ){
	$file_id = Session::get('upload_file_id');	
	$file = DB::table('doc')->where('id',$file_id)->pluck('file');
	$reader = PHPExcel_IOFactory::load( storage_path(). '/'. $file );
	$workSheet = $reader->getActiveSheet();
	foreach ($workSheet->getRowIterator() as $row) {
		$cellIterator = $row->getCellIterator();
		$cellIterator->setIterateOnlyExistingCells(false);
		foreach ($cellIterator as $cell){
			echo $cell->getValue();
		}
	}
}

