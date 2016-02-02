<?php

$works = array_map(function($sch_id) {
    return new Project\Yearbook\Work(['sch_id' => $sch_id]);
}, [Input::get('user.work.sch_id')]);

Project\Yearbook\User::find($user->id)->works()->delete();
Project\Yearbook\User::find($user->id)->works()->saveMany($works);
