<?php

$positions = array_keys(array_filter(Input::get('user.positions')));

$user->positions()->attach($positions);

Project\Used\User::find($user->id)->works()->save(new Project\Used\Work([
    'sch_id' => Input::get('user.work.sch_id'),
    'department_class' => Input::get('user.work.type'),
]));
