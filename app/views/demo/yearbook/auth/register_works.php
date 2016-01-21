<?php

$works = Project\Teacher\User::find(Auth::user()->id)->works->unique()->map(function($work) {
    return new Project\Yearbook\Work(['ushid' => $work->ushid]);
})->all();

Project\Yearbook\User::find(Auth::user()->id)->works()->delete();
Project\Yearbook\User::find(Auth::user()->id)->works()->saveMany($works);