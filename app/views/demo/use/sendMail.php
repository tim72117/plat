<?php

set_time_limit(0);

Config::set('mail.host', '192.168.0.77');
Config::set('mail.port', 25);
Config::set('mail.encryption', '');

$users = DB::table('user_in_group')	    
		->leftJoin('users','users.id','=','user_in_group.user_id')
		->leftJoin('contact','contact.user_id','=','user_in_group.user_id')					
        //->where('email','tim72117@gmail.com')
        ->where('group_id','=', 1)
		->select('users.email','contact.sname')
        ->skip(780)
        ->take(100)
		->get();
    

if( false )
foreach($users as $index => $user){
	
	echo 'start: '.$index.' - '.$user->sname.' - '.$user->email.' - status: ';
	
	$credentials = array('email' => $user->email);
	
	Config::set('auth.reminder.email', 'emails.auth.reminder_use_reset');
	
	if( false )
	echo Password::remind($credentials, function($message){
        $message->subject('重新設定後期中等教育資料庫查詢平台密碼');
    });
    
    echo 'finish<br />';
}



