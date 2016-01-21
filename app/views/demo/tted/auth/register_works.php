<?php

$works = Project\Yearbook\User::find(Auth::user()->id)->works->unique()->map(function($work) {
    return new Project\Teacher\Work(['ushid' => $work->ushid, 'type' => 0]);
})->all();

Project\Teacher\User::find(Auth::user()->id)->works()->delete();
Project\Teacher\User::find(Auth::user()->id)->works()->saveMany($works);