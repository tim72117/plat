<?php

$works = [new Project\Cher\Work(['sch_id' => Input::get('sch_id')])];

Project\Cher\User::find($user->id)->works()->saveMany($works);