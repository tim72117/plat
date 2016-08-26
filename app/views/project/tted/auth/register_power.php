<?php

$works = Plat\Member::where('user_id', $member->user_id)->where('project_id', 2)->first()->organizations->load('now')->map(function($organization) {
    return new Project\Yearbook\Work(['sch_id' => $organization->now->id]);
})->all();

Project\Yearbook\User::find($member->user_id)->works()->delete();
Project\Yearbook\User::find($member->user_id)->works()->saveMany($works);
