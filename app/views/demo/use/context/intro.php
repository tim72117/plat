<?


$user = Auth::user();
$docs = VirtualFile::with('requester')->has('requester')->where('user_id',$user->id)->get();


foreach($docs as $doc){
	var_dump($doc->requester->docRequester);
	echo '<br />';
	echo '<br />';
	echo '<br />';
}