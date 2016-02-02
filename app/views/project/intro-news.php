<?php

$news = $project->news()->orderBy('publish_at', 'desc')->get();

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

