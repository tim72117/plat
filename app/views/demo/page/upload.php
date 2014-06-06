<?php


$user = Auth::user();
$csrf_token = csrf_token();
$dddos_token = dddos_token();

$fileProvider = app\library\files\v0\FileProvider::make();

/*
| 上傳檔案且匯入檔案到doc
*/
echo Form::open(array('url' => $fileProvider->create(), 'files' => true));
echo Form::file('file_upload');
echo Form::submit('Click Me!');
echo Form::hidden('_token1', $csrf_token);
echo Form::hidden('_token2', $dddos_token);
echo Form::close();


$files = Files::where('owner',$user->id)->where('type',3)->get();
foreach($files as $file){
	echo '<a href="'.URL::to($fileProvider->download($file->id)).'">'.$file->title.'下載</a><br />';
}




if( Session::has('upload_file_id') ){
	$file_id = Session::get('upload_file_id');	
	echo $file_id;
}else{
	
	if( $errors )
		echo implode('、',array_filter($errors->all()));
}