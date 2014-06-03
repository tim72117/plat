<?php


$contacts = DB::table('pub_contact_use')->where('admin',false)->where('pstat',1)->orderBy('email')->skip(5)->get();

foreach($contacts as $contact){
	
	$user_same_email = User::where('email',$contact->email)->where('username',$contact->name)->first();

	if( count($user_same_email)==0 ){
		
		$user = new User;
		$user->username    = $contact->name;
		$user->password    = '';
		$user->email       = $contact->email;
		$user->project     = 'use';		
		//$user->save();
		
		$contact_new = new Contact(array(
			'sch_id'           => $contact->sch_id,
			'department'       => $contact->dep,
			'department_class' => $contact->page_index,
			'title'            => $contact->title,
			'tel'              => $contact->tel,
			'fax'              => $contact->fax,
			'schpeo'           => $contact->schpeo,
			'senior1'          => $contact->senior1,
			'senior2'          => $contact->senior2,
			'tutor'            => $contact->tutor,
			'parent'           => $contact->parent,
			'sname'           => $contact->sname,
			'created_by'       => '1',
			'created_ip'       => '127.0.0.1',
		));

		$contact_new->setTable('contact_use');

		//$contact_new = $user->contact()->save($contact_new);
		
		echo $contact_new->getKey().' - '.$contact->name.'<br />';
		
		//DB::table('contact_sch_use')->insert(array('sch_id'=>$contact->sch_id, 'user_id'=>$user->id));
		
		//DB::table('user_in_group')->insert(array('group_id'=>2, 'user_id'=>$user->id));
		
		
	}else{
		
		$user = $user_same_email;
		
		//DB::table('contact_sch_use')->insert(array('sch_id'=>$contact->sch_id, 'user_id'=>$user->id));
		
	}
	
		


	
	
	
	
	
}