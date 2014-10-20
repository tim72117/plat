

<div style="margin:10px 0 0 0;width:800px">
<?

$user = Auth::user();


echo '<div style="border: 1px solid #aaa;padding:10px;width:800px;color:#f00">教育部中小學師資資料庫整合平台進行系統轉移，登入後請盡速確認承辦人個人資料。';

$browser = get_browser(null, true);
if( true || $browser['browser']=='IE' && $browser['version']<8 ) {
    echo '<p style="color:#f00">';
    echo '本系統不支援IE7以下版本，請更新您的瀏覽器版本</p>';
    echo '<p>' . 
         '<a href="http://windows.microsoft.com/zh-tw/internet-explorer/download-ie" target="_blank">下載IE瀏覽器' .
         '<img src="'.asset('images/browser_internet-explorer-20.png').'" height="20" border="0" style="margin-bottom:-4px" /></a>' .
         '、' .
         '<a href="http://www.google.com/intl/zh-TW/chrome/" target="_blank">下載Chrome瀏覽器' .
         '<img src="'.asset('images/browser_chrome-20.png').'" height="20" border="0" style="margin-bottom:-4px" /></a>' .
         '、' .
         '<a href="http://mozilla.com.tw/firefox/new/" target="_blank">下載Firefox瀏覽器' .
         '<img src="'.asset('images/browser_firefox.png').'" height="20" border="0" style="margin-bottom:-4px" /></a>';    
    echo '</p>';
}

echo '</div>';


$docs = Apps::with('requester.docRequester')->has('requester')->where('user_id',$user->id)->get();


foreach($docs as $doc){
	echo '<div style="border: 1px solid #aaa;padding:10px;width:800px;margin-top:5px;color:#f00">';
	echo '<p style="margin:0">您有一個檔案上傳的請求 "'.$doc->isFile->title.'" 來自於：國立臺灣師範大學教育評鑑與研究中心'.$doc->requester->docRequester->user->username;
	if( $doc->requester->running ){
		echo '<p style="margin:5px 0 0 0">'.$doc->requester->description.'</p>';
	}else{
		echo '(已完成)';
	}
	echo '</p>';
	echo '</div>';
}


$inGroup = $user->inGroups->lists('id');


if( count($inGroup)>0 ) {
    $shares = ShareApp::query()->where(['target' => 'group', 'active' => true])->whereIn('target_id', $inGroup)->get();


    foreach($shares as $share){
        Apps::firstOrCreate(['user_id' => $user->id, 'file_id' => $share->doc->file_id]);
        echo '<div style="border: 1px solid #aaa;padding:10px;width:800px;margin-top:5px;color:#f00">';
        echo '國立臺灣師範大學教育評鑑與研究中心分享一個檔案給你：'.$share->doc->isFile->title;
        echo '</div>';
    }
}

?>

	
</div>