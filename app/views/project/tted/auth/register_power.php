<?php

$works = Project\Teacher\User::find(Auth::user()->id)->works->unique()->map(function($work) {
    return new Project\Yearbook\Work(['sch_id' => $work->sch_id]);
})->all();

Project\Yearbook\User::find($member->user_id)->works()->delete();
Project\Yearbook\User::find($member->user_id)->works()->saveMany($works);