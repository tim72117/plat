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
    'disabled' => function() {
        $input = Input::only('project', 'user_id', 'disabled', 'cacheName');
        $contact = Contact::where('user_id', $input['user_id'])->where('project', $input['project'])->first();
        $contact->active = $input['disabled'] ? false : $contact->active;
        $contact->active_ip = Request::getClientIp();
        $contact->active_at = date("Y-m-d H:i:s");
        $contact->user->active = $input['disabled'] ? false : $contact->user->active;
        $contact->user->disabled = $input['disabled'];
        $contact->push();
        Cache::forget($input['cacheName']);
        return array('saveStatus'=>true, 'cache'=>Cache::get($input['cacheName']));
    },  
    'group' => function() {
        $input = Input::only('group_id', 'user_id', 'active', 'cacheName');
        $user_in_group = DB::table('user_in_group')->where('user_id', $input['user_id'])->where('group_id', $input['group_id']);
        if( $user_in_group->exists() ){
            if( !$input['active'] ){
                $user_in_group->delete();
            }
        }else{
            if( $input['active'] ){
                $user_in_group->insert(array('group_id'=>$input['group_id'], 'user_id'=>$input['user_id']));
            }
        }
        Cache::forget($input['cacheName']);
        return array('saveStatus'=>true, 'cache'=>Cache::get($input['cacheName']));
    },
    'reflash' => function() {
        $input = Input::only('cacheName');
        Cache::forget($input['cacheName']);
        return array('saveStatus'=>true, 'cache'=>Cache::get($input['cacheName']));
    },
    'deleteUser' => function() {
        $input = Input::only('user_id');
        
        //User::
        
        return array('saveStatus'=>true, 'user_id'=>$input['user_id']);
    }
);
