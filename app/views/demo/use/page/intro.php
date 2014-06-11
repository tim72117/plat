

<div style="margin:20px 100px;width:800px">
<?

$user = Auth::user();

//if( $user->id==1 ){
	echo '<div style="border: 1px solid #aaa;padding:10px;width:800px">後期中等教育資料查詢平台進行系統轉移，登入後請盡速確認承辦人個人資料。</div>';
//}
	
$fileProvider = app\library\files\v0\FileProvider::make();
echo '<div style="border: 1px solid #aaa;padding:10px;width:800px;margin-top:5px">';
echo '<a href="'.URL::to($fileProvider->download(16)).'">102學年度高二學生中獎名單(公告).pdf</a><br />';
echo '<a href="'.URL::to($fileProvider->download(17)).'">102學年度導師問卷普查 中獎名單公告.pdf</a>';
echo '</div>';	

if( $user->schools->count()>1 ){
	
	$sch_id = Input::get('sch_id', Session::get('sch_id'));
	Session::put('sch_id', $sch_id);
	echo '<div style="border: 1px solid #aaa;padding:10px;width:800px;margin-top:5px">';
	echo '選擇您承辦業務的學校代碼';
	echo Form::open(array('url' => 'page/project'));
	echo Form::select('sch_id', $user->schools->lists('sname','id'), $sch_id); 
	echo Form::submit('Click Me!');
	echo Form::close();
	echo '</div>';
	
}elseif( $user->schools->count()>0 ){	
	Session::put('sch_id', $user->schools[0]->id);
}




$docs = VirtualFile::with('requester.docRequester')->has('requester')->where('user_id',$user->id)->get();


foreach($docs as $doc){
	echo '<div style="border: 1px solid #aaa;padding:10px;width:800px;margin-top:5px">';
	echo '<p style="margin:0">您有一個檔案上傳的請求 "'.$doc->isFile->title.'" 來自於：'.$doc->requester->docRequester->user->username;
	if( $doc->requester->running ){
		echo '<p style="margin:5px 0 0 0">'.$doc->requester->description.'</p>';
	}else{
		echo '(已完成)';
	}
	echo '</p>';
	echo '</div>';
}


$shares = Sharer::with('fromUser','fromDoc')->where('shared_user_id', $user->id)->get();

foreach($shares as $share){
	echo '<div style="border: 1px solid #aaa;padding:10px;width:800px;margin-top:5px">';
	echo $share->fromUser->username.'分享一個檔案給你：'.$share->fromDoc->isFile->title;
	if( !$share->accept ){
		echo Form::open(array('url' => $user->get_file_provider()->get_doc_active_url('get_share', $share->from_doc_id), 'style'=>'width:0;display:inline-block;margin:0'));
		echo Form::submit('同意', array('style'=>'margin:0;line-height:15px'));
		echo Form::close();
	}
	echo '</div>';
}

?>

	
</div>