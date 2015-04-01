<?php
$project_id = DB::table('projects')->where('code', $project)->first()->id;

$news = DB::table('news')->where('project', $project_id)->orderBy('publish_at', 'desc')->limit(3)->get();

echo '<div class="ui list">';

foreach($news as $new) { 
    
    $publish_at = new Carbon\Carbon($new->publish_at);
    
    $now = Carbon\Carbon::now();
    
    $difference = ($publish_at->diff($now)->days);

    if( json_decode($new->display_at)->intro ) {
        echo '<div class="item">';

        echo '<div class="content">';
        echo    '<div class="header">';
        echo        $new->title;
        echo    '</div>';
        echo    '<div class="description">' . $new->context . '  ' . $difference . '天前</div>';    
        echo '</div>';

        echo '</div>';
    }
    
}

echo '</div>'; 