<?php
$user = Auth::user();
$fileProvider = app\library\files\v0\FileProvider::make();

$inGroups = $user->inGroups->lists('id');

$shareFiles = ShareFile::with('isFile')->where(function($query) use($user) {
    $query->where('target', 'user')->where('target_id', $user->id)->where('created_by', '<>', $user->id);
})->orWhere(function($query) use($user, $inGroups) {
    count($inGroups)>0 && $query->where('target', 'group')->whereIn('target_id', $inGroups)->where('created_by', '<>', $user->id);
})->orderBy('created_at', 'desc')->get();

$project_id = DB::table('projects')->where('code', $user->getProject())->first()->id;
$news = DB::table('news')->where('project', $project_id)->whereNull('deleted_at')->orderBy('publish_at', 'desc')->get();
foreach($news as $new) { 
    $publish_at = new Carbon\Carbon($new->publish_at);
    $now = Carbon\Carbon::now();
    $difference = ($publish_at->diff($now)->days);
    echo '<div class="item"><i class="top aligned announcement ' . ($difference>7 ? '' : 'red') . ' icon"></i>';
    
    echo '<div class="content">';
    echo    '<div class="header">';
    echo        $new->title;
    echo    '</div>';
    echo    $new->context;
    echo    '<div class="description">' . $difference . '天前</div>';    
    echo '</div>';
    
    echo '</div>';
}

foreach($shareFiles as $shareFile) {    
    switch($shareFile->isFile->type) {
        default:         
            echo '<div class="item"><i class="file outline icon"></i>';
            echo '<div class="content">教育評鑑與研究中心傳送一個檔案給你：<a href="/'.$fileProvider->download($shareFile->id).'">'.$shareFile->isFile->title.'</a></div>';
            echo '</div>';
            break;    
    }
}
