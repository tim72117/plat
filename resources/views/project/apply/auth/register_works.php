<?php

$works = array_map(function($sch_name) {
    return new Project\Apply\Work(['sch_id' => Input::get('user.work.sch_id'), 'sch_name' => $sch_name]);
}, [Input::get('user.work.sch_name')]);

Project\Apply\User::find($member->user_id)->works()->delete();
Project\Apply\User::find($member->user_id)->works()->saveMany($works);