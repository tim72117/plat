<?php

$works = Project\Yearbook\User::find(Auth::user()->id)->works->unique()->map(function($work) {
    return new Project\Teacher\Work(['sch_id' => $work->sch_id, 'type' => 0]);
})->all();

Project\Teacher\User::find($member->user_id)->works()->delete();
Project\Teacher\User::find($member->user_id)->works()->saveMany($works);