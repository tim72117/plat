









<div style="margin:20px 100px;width:500px">
<?


$user = Auth::user();
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