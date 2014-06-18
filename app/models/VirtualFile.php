<?php
class Files extends Eloquent {
	
	protected $table = 'files';
	
	public $timestamps = true;
	
	protected $fillable = array('title', 'type', 'owner', 'file');
	
}

class Requester extends Eloquent {
	
	protected $table = 'auth_requester';
	
	public $timestamps = true;
	
	protected $fillable = array('preparer_doc_id', 'requester_doc_id', 'running');
	
	public function files() {
		return $this->hasMany('Files','owner','preparer_doc_id');
	}
	
	public function docPreparer() {
		return $this->hasOne('VirtualFile','id','preparer_doc_id');
	}
	
	public function doc() {
		return $this->belongsTo('VirtualFile','preparer_doc_id','id');//未使用未測試
	}
	
	public function docRequester() {
		return $this->hasOne('VirtualFile','id','requester_doc_id');
	}
	
}

class Sharer extends Eloquent {
	
	protected $table = 'share';
	
	public $timestamps = true;
	
	protected $fillable = array('from_doc_id', 'shared_user_id', 'accept');
	
	public function fromUser() {
		return $this->hasOne('User','id','shared_user_id');
	}
	
	public function fromDoc() {
		return $this->hasOne('VirtualFile','id','from_doc_id');
	}
	
}
	
class VirtualFile extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'docs';
	
	public $timestamps = true;
	
	protected $fillable = array('user_id', 'file_id');
	
	public function user() {
		return $this->hasOne('User','id','user_id');
	}
	
	public function hasFiles() {
		return $this->hasMany('Files','owner');
	}
	
	public function isFile() {
		return $this->hasOne('Files','id','file_id');
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
	
	public function docPreparer() {
		return $this->belongsToMany('VirtualFile', 'auth_requester', 'requester_doc_id', 'preparer_doc_id');//未使用未測試
	}
	
	public static function getRequester() {

		return new Requester;
	}

}