<?php



set_time_limit(0);

$users = DB::table('contact')
		->leftJoin('users','users.id','=','contact.user_id')
        ->leftJoin('mail','mail.user_id', '=', 'users.id')	
		->where('contact.project','=','use')
		->where('contact.user_id','>',19)//小於19為內部人員
        ->whereNull('mail.user_id')//每次清空！
		//->where('email','=','aletisho@gmail.com')
        ->select('users.id', 'users.email', 'contact.sname')
		->get();
		//->whereIn('users.email',array('luechihung@yahoo.com.tw'))	
        //->whereIn('users.email',array('tim72117@gmail.com'))	
		//->where('users.active','=', 0)
        //->where('users.disabled','=', 0)
		//->where('users.created_at', '<', '2014-06-03 22:27:48.000')
		//->leftJoin('password_reminders','users.email','=','password_reminders.email')
		//->whereNull('password_reminders.email')
		//

foreach($users as $index => $user){
	
	echo 'start: '.$index.' - '.$user->sname.' - '.$user->email.' - status: ';
	
	$credentials = array('email' => $user->email);
	
	Config::set('auth.reminder.email', 'emails.auth.140812');
    Config::set('mail.host', 'smtp.gmail.com');
    Config::set('mail.port', 465);
    Config::set('mail.encryption', 'ssl');
    Config::set('mail.username', 'usedatabase.smtp@gmail.com');
    Config::set('mail.password', 'edulyw928');
	
	$a=0;
	if( $a==1 ){
	echo Password::remind($credentials, function($message) use($user){
		$message->sender('USEdatabase@deps.ntnu.edu.tw ');
        $message->subject('教育部國民及學前教育署103學年度高一及專一新生調查開跑囉！');
        DB::table('mail')->insert(array('user_id' => $user->id));
   });
   }
   else echo 'hello';
   
    echo 'finish<br />';
}