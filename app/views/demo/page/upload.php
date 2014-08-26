<?php


$user = Auth::user();

$fileProvider = app\library\files\v0\FileProvider::make();

/*
| 上傳檔案且匯入檔案到doc
*/
echo Form::open(array('url' => $fileProvider->create(), 'files' => true));
echo Form::file('file_upload');
echo Form::submit('Click Me!');
echo Form::close();


$files = Files::where('created_by', $user->id)->where('type', 3)->get();
foreach($files as $file){
	echo '<a href="'.URL::to($fileProvider->download($file->id)).'">'.$file->title.'下載</a><br />';
}




if( Session::has('upload_file_id') ){
	$file_id = Session::get('upload_file_id');	
	echo $file_id;
	
	VirtualFile::create(array(
		'user_id'  =>  $user->id,
		'file_id'  =>  $file_id,
	));
}else{
	
	if( $errors )
		echo implode('、',array_filter($errors->all()));
}