<?php

$works = array_map(function($sch_id) {

    $work = ['sch_id' => $sch_id];

    if ($sch_id == '1028') {
        $work['dep_id'] = Input::get('dep_id');
    }

    if ($sch_id == '9999') {
        $work['sch_name'] = Input::get('sch_name');
    }

    return new Project\Cher\Work(['sch_id' => $sch_id]);

}, [Input::get('sch_id')]);

Project\Cher\User::find($user->id)->works()->delete();
Project\Cher\User::find($user->id)->works()->saveMany($works);