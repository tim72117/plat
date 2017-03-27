<?php

$posts = $project->posts()->get();
$now = Carbon\Carbon::now();

foreach($posts as $post) {

    $publish_at = new Carbon\Carbon($post->publish_at);

    $difference = $publish_at->diffInDays($now, false);

    if ($publish_at->diffInSeconds($now, false) > 0) {
        echo '<div class="item"><i class="top aligned announcement ' . ($difference>7 ? '' : 'red') . ' icon"></i>';

        echo '<div class="content">';
        echo    '<div class="header">';
        echo        $post->title;
        echo    '</div>';
        echo    $post->context;
        echo    '<div class="description">' . $difference . '天前</div>';
        echo '</div>';

        echo '</div>';
    }

}

