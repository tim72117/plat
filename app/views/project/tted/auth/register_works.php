<?php

$works = array_map(function($sch_id) {
    return new Project\Teacher\Work(['sch_id' => $sch_id]);
}, [Input::get('user.work.sch_id')]);
$user->positions()->attach(Input::get('user.work.position'));

Project\Teacher\User::find($member->user_id)->works()->delete();
Project\Teacher\User::find($member->user_id)->works()->saveMany($works);
