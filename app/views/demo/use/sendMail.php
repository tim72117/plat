<?php



set_time_limit(0);

$users = DB::table('contact')
		->leftJoin('users','users.id','=','contact.user_id')
        ->leftJoin('mail','mail.user_id', '=', 'users.id')	
		//->leftJoin('password_reminders','users.email','=','password_reminders.email')
		//->whereNull('password_reminders.email')
		->where('contact.project','=','use')
        ->whereNull('mail.user_id')
		->where('users.active','=', 0)
        ->where('users.disabled','=', 0)
		->where('users.created_at', '<', '2014-06-03 22:27:48.000')
        //->whereIn('users.email',array('luechihung@yahoo.com.tw'))	
        //->whereIn('users.email',array('tim72117@gmail.com'))	
        ->select('users.id', 'users.email', 'contact.sname')
		->get();

foreach($users as $index => $user){
	
	echo 'start: '.$index.' - '.$user->sname.' - '.$user->email.' - status: ';
	
	$credentials = array('email' => $user->email);
	
	Config::set('auth.reminder.email', 'emails.auth.reminder_use_reset');
    //Config::set('mail.host', 'smtp.gmail.com');
    //Config::set('mail.port', 465);
    //Config::set('mail.encryption', 'ssl');
    //Config::set('mail.username', 'usedatabase.smtp@gmail.com');
    //Config::set('mail.password', 'edulyw928');
	
	//if( false )
	echo Password::remind($credentials, function($message) use($user){
        $message->subject('重新設定後期中等教育資料庫查詢平台密碼');
        DB::table('mail')->insert(array('user_id' => $user->id));
    });
    
    echo 'finish<br />';
}



