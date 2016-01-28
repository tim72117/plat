<?php

$works = array_map(function($sch_name) {
    return new Project\Apply\Work(['sch_id' => Input::get('sch_id'), 'sch_name' => $sch_name]);
}, [Input::get('sch_name')]);

Project\Apply\User::find($member->user_id)->works()->delete();
Project\Apply\User::find($member->user_id)->works()->saveMany($works);