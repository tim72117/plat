<?php

$news = $project->news()->orderBy('publish_at', 'desc')->limit(3)->get();

$event = '';

foreach($news as $new) {

    $publish_at = new Carbon\Carbon($new->publish_at);

    $now = Carbon\Carbon::now();

    $difference = ($publish_at->diff($now)->days);

    if( json_decode($new->display_at)->intro ) {

        $event .= '<div class="event">';

        $event .= '    <div class="label">';
        $event .= '        <i class="pencil icon"></i>';
        $event .= '    </div>';

        $event .= '<div class="content">';
        $event .=    '<div class="summary">';
        $event .=       $new->title;
        $event .=       '<div class="date">' . '  ' . $difference . '天前</div>';
        $event .=    '</div>';
        $event .=    '<div class="extra text">' . $new->context . '</div>';
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