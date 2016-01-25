<?php

$works = array_map(function($sch_id) {
    return new Project\Teacher\Work(['sch_id' => $sch_id, 'type' => Input::get('type_class')]);
}, [Input::get('sch_id')]);

Project\Teacher\User::find($user->id)->works()->delete();
Project\Teacher\User::find($user->id)->works()->saveMany($works);
