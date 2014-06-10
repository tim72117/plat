









<div style="margin:20px 100px;width:800px">
<?

$user = Auth::user();

if( $user->schools->count()>1 ){
	
	$sch_id = Input::get('sch_id', Session::get('sch_id'));
	Session::put('sch_id', $sch_id);
	echo '選擇您承辦業務的學校代碼';
	echo Form::open(array('url' => 'page/project'));
	echo Form::select('sch_id', $user->schools->lists('sname','id'), $sch_id); 
	echo Form::submit('Click Me!');
	echo Form::close();
	
}elseif( $user->schools->count()>0 ){	
	Session::put('sch_id', $user->schools[0]->id);
}




$docs = VirtualFile::with('requester.docRequester')->has('requester')->where('user_id',$user->id)->get();


foreach($docs as $doc){
	echo '您有一個檔案上傳的請求 來自於：'.$doc->requester->docRequester->user->username;
	echo '<br />';
	echo $doc->requester->description;
	echo '<br />';
	echo '<br />';
	echo '<br />';
}


?>

	
</div>