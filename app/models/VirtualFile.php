<?php
class Files extends Eloquent {
	protected $table = 'files';
	public $timestamps = false;
}

class Requester extends Eloquent {
	protected $table = 'auth_requester';
	public $timestamps = false;
	
	public function files() {
		return $this->hasMany('Files','owner','id_doc');
	}
	
	public function doc() {
		return $this->hasOne('VirtualFile','id','id_doc');
	}
	
	public function doc_requester() {
		return $this->hasOne('VirtualFile','id','id_requester');
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
	
	protected $fillable = array('id_user', 'id_file');
	
	public function user() {
		return $this->hasOne('User','id','id_user');
	}
	
	public function files() {
		return $this->hasMany('Files','owner');
	}
	
	public function requester() {
		return $this->hasOne('Requester','id_doc');
	}
	
	public function preparer() {
		return $this->hasMany('Requester','id_requester');
	}
	
	public function scopeFile($query) {
		return $query->leftJoin('files','docs.id_file','=','files.id');
	}

}