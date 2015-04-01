<?php

class User_tiped extends User {

    public function schools() { 
		return $this->belongsToMany('School_tiped', 'work_tiped', 'user_id', 'sch_id');
	}
	
}

class School_tiped extends Eloquent {
	
	protected $table = 'pub_uschool';

	public $timestamps = false;
	
}