<?php


Config::set('demo.project', 'use');

/*
$group = Group::with(array('users' => function($query){
		return $query->where('users.id', '=', 1);//->where('users.project', 'use');
	}
))->where('id', 3)->first();
$users = $group->users;
*/


$users = DB::table('users')
		->leftJoin('contact_use','users.id','=','contact_use.id')
		->where('contact_use.sname','like','%教育%')
		//->where('users.id','=','1')
		->get();

foreach($users as $user){
	
	echo $user->email.'<br />';
	echo $user->sname.'<br />';
	
	$credentials = array('email' => $user->email);
	
	Config::set('auth.reminder.email', 'emails.auth.reminder_use_reset');
	
	if( false )
	Password::remind($credentials, function($message){
        $message->subject('重新設定後期中等教育資料庫查詢平台密碼');
    });
}



