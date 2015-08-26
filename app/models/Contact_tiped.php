<?php

class User_tiped extends User {

    public function schools() { 
		return $this->belongsToMany('School_tiped', 'work_tiped', 'user_id', 'sch_id');
	}

	public function departments() { 
		return $this->belongsToMany('Department_tiped', 'work_tiped', 'user_id', 'dep_id');
	}
	
}

class School_tiped extends Eloquent {
	
	protected $table = 'pub_uschool';

	public $timestamps = false;
		
}

class Department_tiped extends Eloquent {
	
	protected $table = 'pub_depcode_u';

	public $timestamps = false;
	
}