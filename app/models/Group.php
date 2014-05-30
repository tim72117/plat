<?php

class Group extends Eloquent {
	
	protected $table = 'group';
	public $timestamps = false;

	
	public function usersDocs() {
		return $this->belongsToMany('User', 'user_in_group', 'group_id', 'user_id')->with('docs');//->leftJoin('docs','users.id','=','docs.id_user');
	}
	
	public function users() {
		return $this->belongsToMany('User', 'user_in_group', 'group_id', 'user_id');
	}	
	
}