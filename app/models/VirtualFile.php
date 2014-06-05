<?php
class Files extends Eloquent {
	
	protected $table = 'files';
	
	public $timestamps = true;
	
	protected $fillable = array('title', 'type', 'owner', 'file');
}

class Requester extends Eloquent {
	
	protected $table = 'auth_requester';
	
	public $timestamps = true;
	
	public function files() {
		return $this->hasMany('Files','owner','preparer_doc_id');
	}
	
	public function docPreparer() {
		return $this->hasOne('VirtualFile','id','preparer_doc_id');
	}
	
	public function docRequester() {
		return $this->hasOne('VirtualFile','id','requester_doc_id');
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
	
	protected $fillable = array('user_id', 'file_id');
	
	public function user() {
		return $this->hasOne('User','id','user_id');
	}
	
	public function hasFiles() {
		return $this->hasMany('Files','owner');
	}
	
	public function isFiles() {
		return $this->hasOne('Files','owner','file_id');
	}
	
	public function requester() {
		return $this->hasOne('Requester','preparer_doc_id');
	}
	
	public function preparers() {
		return $this->hasMany('Requester','requester_doc_id');
	}
	
	public function scopeFile($query) {
		return $query->leftJoin('files','docs.file_id','=','files.id');
	}

}