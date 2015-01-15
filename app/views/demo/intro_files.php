<?
$user = Auth::user();
$fileProvider = app\library\files\v0\FileProvider::make();

$inGroups = $user->inGroups->lists('id');

$shareFiles = ShareFile::with('isFile')->where(function($query) use($user){
    $query->where('target', 'user')->where('target_id', $user->id)->where('created_by', '<>', $user->id);
})->orWhere(function($query) use($user, $inGroups){
    count($inGroups)>0 && $query->where('target', 'group')->whereIn('target_id', $inGroups)->where('created_by', '<>', $user->id);
})->orderBy('created_at', 'desc')->get();

foreach($shareFiles as $shareFile) {    
    switch($shareFile->isFile->type) {
        case 1:
            //$intent_key = $fileProvider->doc_intent_key('open', $shareFile->id, 'app\\library\\files\\v0\\QuesFile');
            //$link['open'] = 'file/'.$intent_key.'/open';
        break;
        case 5:
            //$intent_key = $fileProvider->doc_intent_key('open', $shareFile->id, 'app\\library\\files\\v0\\RowsFile');
            //$link['open'] = 'file/'.$intent_key.'/open';
        break;
        default:         
            echo '<a href="/'.$fileProvider->download($shareFile->file_id).'">'.$shareFile->isFile->title.'</a><br />';
        break;    
    }
}

$shares = ShareApp::where(['target' => 'group', 'active' => true])->where(function($query) use($inGroups){
    empty($inGroups) ? $query->whereNull('id') : $query->whereIn('target_id', $inGroups);
})->get()->each(function($share) use($user){
    Apps::firstOrCreate(['user_id' => $user->id, 'file_id' => $share->isApp->file_id]);
    echo '<div style="border: 0px solid #aaa;padding:10px 10px 10px 0;width:800px;margin-top:5px;color:#f00">';
    echo '國立臺灣師範大學教育評鑑與研究中心分享一個檔案給你：'.$share->isApp->isFile->title;
    echo '</div>';
});