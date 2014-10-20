

<div style="margin:10px 0 0 0;width:800px">
<?

$user = Auth::user();


echo '<div style="border: 1px solid #aaa;padding:10px;width:800px;color:#f00">後期中等教育資料查詢平台進行系統轉移，登入後請盡速確認承辦人個人資料。';

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

//$news = DB::table('news')->where('project', 1)->get();

//var_dump($news);
	
$fileProvider = app\library\files\v0\FileProvider::make();
echo '<div style="border: 1px solid #aaa;padding:10px;width:800px;margin-top:5px">';
echo '<a href="'.URL::to($fileProvider->download(16)).'">102學年度高二學生中獎名單(公告).pdf</a><br />';
echo '<a href="'.URL::to($fileProvider->download(17)).'">102學年度導師問卷普查 中獎名單公告.pdf</a><br />';
echo '<a href="'.URL::to($fileProvider->download(2)).'">102學年度國三畢業生基本資料範例表格下載</a><br />';
echo '<a href="'.URL::to($fileProvider->download(559)).'">103年後期中等教育學校承辦人說明會0729(說明會後修訂版)</a><br />';
echo '<a href="'.URL::to($fileProvider->download(612)).'">103年高一學生調查說帖</a><br />';
echo '<a href="'.URL::to($fileProvider->download(638)).'">103高一學生線上問卷填答事前資訊準備表</a><br />';
echo '<a href="'.URL::to($fileProvider->download(706)).'">103高一專一學生問卷_公告版</a>';

echo '</div>';	

$inGroup = $user->inGroups->lists('id');
$shares = ShareApp::where(['target' => 'group', 'active' => true])->where(function($query) use($inGroup){
    empty($inGroup) ? $query->whereNull('id') : $query->whereIn('target_id', $inGroup);
})->get()->each(function($share) use($user){
    Apps::firstOrCreate(['user_id' => $user->id, 'file_id' => $share->isApp->file_id]);
    echo '<div style="border: 1px solid #aaa;padding:10px;width:800px;margin-top:5px;color:#f00">';
    echo '國立臺灣師範大學教育評鑑與研究中心分享一個檔案給你：'.$share->isApp->isFile->title;
    echo '</div>';
});
?>

	
</div>