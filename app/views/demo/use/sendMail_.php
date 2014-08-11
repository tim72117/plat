<?php

set_time_limit(0);
		
$users = DB::table('user_in_group')	    
		->leftJoin('users','users.id','=','user_in_group.user_id')
		->leftJoin('contact','contact.user_id','=','user_in_group.user_id')		
        ->leftJoin('mail','mail.user_id','=','user_in_group.user_id')	
        //->where('email','tim72117@gmail.com')
        ->where('user_in_group.group_id', '=', 1)
        ->whereNull('mail.user_id')
		->select('users.id', 'users.email', 'contact.sname')
		->get();

Config::set('mail.host', '192.168.0.77');
Config::set('mail.port', 25);
Config::set('mail.encryption', '');

    
foreach($users as $index => $user){
	
	echo 'start: '.$index.' - '.$user->sname.' - '.$user->email.' - status: ';

    if( false )
	Mail::send('demo.use.mails.use_mail_140630', array(), function($message) use ($user){
		$message->from('usedatabase@deps.ntnu.edu.tw', '國立台灣師範大學 教育研究與評鑑中心')
			->to($user->email, $user->sname)
			->subject('平台改版承辦人說明信');
            //->attach( storage_path().'/file1.pdf', array('as' => '後期中等教育整合資料庫學校承辦人說明會') );
        
        DB::table('mail')->insert(array('user_id' => $user->id));		
    });		
				
  
    echo 'finish<br />';
}




