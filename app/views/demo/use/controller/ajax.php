<?php

return array(
    'gg' => function(){
        return array('saveStatus'=>true, 'user_id'=>Input::get('user_id'));
    },
    'active' => function(){
        $input = Input::only('user_id', 'active');
        $user = User::find($input['user_id']);
        $user->active = $input['active'];
        $user->save();
        Cache::forget('sch_profile.group9999');
        return array('saveStatus'=>true);
    },       
);
