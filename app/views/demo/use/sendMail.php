<?php



$group = Group::with('users')->where('id','2')->first();

foreach($group->users as $user){
	
	echo $user->email;
	
	$credentials = array('email' => $user->email);
		
	Password::remind($credentials);
}



