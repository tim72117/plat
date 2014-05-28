<?php

class Group extends Eloquent {
	
	protected $table = 'group';
	public $timestamps = false;

	
	public function usersDocs() {
		return $this->belongsToMany('User', 'user_in_group', 'id_group', 'id_user')->with('docs');//->leftJoin('docs','users.id','=','docs.id_user');
	}
	
	public function users() {
		return $this->belongsToMany('User', 'user_in_group', 'id_group', 'id_user');
	}	
	
}