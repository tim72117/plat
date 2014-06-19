<?php



set_time_limit(0);

$users = DB::table('contact')
		->leftJoin('users','users.id','=','contact.user_id')
		//->leftJoin('password_reminders','users.email','=','password_reminders.email')
		//->whereNull('password_reminders.email')
		->where('contact.project','=','use')
		->where('users.active','=',0)
		->where('users.password','=','')
        ->whereIn('users.id',array(21,
34,
39,
66,
109,
130,
133,
171,
172,
193,
217,
219,
242,
385,
412,
447,
541,
621,
628,
644,
647,
682,
707,
737,
752,
805,
881))		
        ->select('users.email','contact.sname')
		->get();

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



