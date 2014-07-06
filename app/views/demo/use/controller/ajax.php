<?php

return array(
    'gg' => function() {
        return array('saveStatus'=>true, 'user_id'=>Input::get('user_id'));
    },
    'active' => function() {
        $input = Input::only('project', 'user_id', 'active', 'cacheName');
        $contact = Contact::where('user_id', $input['user_id'])->where('project', $input['project'])->first();
        $contact->active = $input['active'];
        $contact->active_ip = Request::getClientIp();
        $contact->active_at = date("Y-m-d H:i:s");
        $contact->user->active = $input['active'];
        $contact->push();
        Cache::forget($input['cacheName']);
        return array('saveStatus'=>true, 'cache'=>Cache::get($input['cacheName']));
    },    
    'deleteUser' => function() {
        $input = Input::only('user_id');
        
        //User::
        
        return array('saveStatus'=>true, 'user_id'=>$input['user_id']);
    }
);
