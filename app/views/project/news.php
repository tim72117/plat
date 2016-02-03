<?php

$posts = $project->posts()->limit(3)->get();
$now = Carbon\Carbon::now();

$event = '';

foreach($posts as $post) {

    $publish_at = new Carbon\Carbon($post->publish_at);

    $difference = $publish_at->diffInDays($now, false);

    if (json_decode($post->display_at)->intro && $publish_at->diffInSeconds($now, false) > 0) {

        $event .= '<div class="event">';

        $event .= '    <div class="label">';
        $event .= '        <i class="pencil icon"></i>';
        $event .= '    </div>';

        $event .= '<div class="content">';
        $event .=    '<div class="summary">';
        $event .=       $post->title;
        $event .=       '<div class="date">' . '  ' . $difference . '天前</div>';
        $event .=    '</div>';
        $event .=    '<div class="extra text">' . $post->context . '</div>';
        $event .=    '<div class="meta">';
        // $event .=       '<a class="like"><i class="like icon"></i> 4 Likes </a>';
        $event .=    '</div>';
        $event .= '</div>';

        $event .= '</div>';
    }

}
?>
<div class="ui feed">
    <?=$event?>
</div>