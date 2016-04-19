<?php

if (Input::get('user.member.apply') && Input::get('user.member.project_id') == 4){
    $member_das = Plat\Member::firstOrNew(['user_id' => $user->id, 'project_id' => 4]);
    $member_das->actived = false;
    $member_das->save();
}

Project\Used\User::find($user->id)->works()->save(new Project\Used\Work([
    'sch_id' => Input::get('user.work.sch_id'),
    'department_class' => Input::get('user.work.type'),
    'staff' => Input::get('user.work.staff', false),
    'parent' => Input::get('user.work.parent', false),
    'student' => Input::get('user.work.student', false),
    'tutor' => Input::get('user.work.tutor', false),
]));
