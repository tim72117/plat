<?php

class Group extends Eloquent {
	
	protected $table = 'group';
	public $timestamps = false;
	
	public function user() {
		return $this->hasOne('User', 'id', 'id_user_target');
	}
	
	public function docs_target() {
		return $this->hasMany('VirtualFile', 'id_user', 'id_user_target');
	}
	
}