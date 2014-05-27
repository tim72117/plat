<?php
class doc extends Eloquent {
	protected $table = 'doc';
	public $timestamps = false;
}

class Requester extends Eloquent {
	protected $table = 'auth_requester';
	public $timestamps = false;
	
	public function docs() {
		return $this->hasMany('doc','owner','id_auth');
	}
}
	
class VirtualFile extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'docs';
	
	public $timestamps = false;
	
	//public function docs() {
	//	return $this;
	//}
	
	public function requester() {
		return $this->hasOne('Requester','id_requester');
		//return $this->leftJoin('auth_requester','auth.id','=','auth_requester.id_auth');
	}

}