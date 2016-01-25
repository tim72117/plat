<?php
return array(
    'getUsers' => function() {        
        $cacheName = Auth::user()->id.'-school-profiile-users-tiped-all';     

        Input::get('reflash', false) && Cache::forget($cacheName);

        $users = Cache::remember($cacheName, 3, function() {            
            return User_tiped::with(['contact', 'schools', 'departments'])->has('contact')->has('schools')->where('id', '>', '20')->get();
        })->map(function($user){    
            return Struct_tiped::auth($user);   
        });
        
        return ['users' => $users];
    },
    'active' => function() {
        $cacheName = Auth::user()->id.'-school-profiile-users-tiped-all';

        Cache::forget($cacheName);

        $input = Input::only('user_id', 'active');

        $user = User_tiped::find($input['user_id']);

        $user->active = $input['active'];
        $user->contact->active = $input['active'];
        $input['active'] && $user->contact->active_ip = Request::getClientIp();
        $input['active'] && $user->contact->active_at = \Carbon\Carbon::now()->toDateTimeString();

        $user->push();         
        
        return ['user' => Struct_tiped::auth($user)];
    },  
    'disabled' => function() {
        $cacheName = Auth::user()->id.'-school-profiile-users-tiped-all'; 

        Cache::forget($cacheName);

        $input = Input::only('user_id', 'disabled');

        $user = User_tiped::find($input['user_id']);

        $user->disabled = $input['disabled'];
        $input['disabled'] && $user->active = false;
        $input['disabled'] && $user->contact->active = false;

        $user->push();
        
        return ['user' => Struct_tiped::auth($user)];
    },  
    'deleteUser' => function() {
        $input = Input::only('user_id');      
        return array('saveStatus'=>true, 'user_id'=>$input['user_id']);
    }
);
