<?php



set_time_limit(0);

$users = DB::table('contact')
		->leftJoin('users','users.id','=','contact.user_id')
		->leftJoin('password_reminders','users.email','=','password_reminders.email')
		->whereNull('password_reminders.email')
		->where('contact.project','=','tted')
        ->select('users.email','contact.sname')
		->get();

foreach($users as $index => $user){
	
	echo $index.' - '.$user->email.'<br />';
	echo $user->sname.'<br />';
	
	$credentials = array('email' => $user->email);
	
	Config::set('auth.reminder.email', 'emails.auth.reminder_use_reset');
	
	if( false )
	echo Password::remind($credentials, function($message){
        $message->subject('重新設定後期中等教育資料庫查詢平台密碼');
    });
}



