<?php
class Files extends Eloquent {
	
	protected $table = 'files';
	
	public $timestamps = true;
	
	protected $fillable = array('title', 'type', 'owner', 'file', 'created_by');

}

class Requester extends Eloquent {
	
	protected $table = 'auth_requester';
	
	public $timestamps = true;
	
	protected $fillable = array('preparer_doc_id', 'requester_doc_id', 'running');
	
	public function files() {
		return $this->hasMany('Files','owner','preparer_doc_id');
	}
	
	public function docPreparer() {
		return $this->hasOne('Apps','id','preparer_doc_id');
	}
	
	public function doc() {
		return $this->belongsTo('Apps','preparer_doc_id','id');//未使用未測試
	}
	
	public function docRequester() {
		return $this->hasOne('Apps','id','requester_doc_id');
	}
	
}

class RequestFile extends Eloquent {
    
    protected $table = 'files_requested';
    
    public $timestamps = true;
    
    protected $fillable = array('share_file_id', 'target', 'target_id', 'created_by', 'description');
    
	public function isFile() {
		return $this->hasOne('Files', 'id', 'file_id');
	}
    
}

class ShareFile extends Eloquent {
    
    protected $table = 'share_file_to';
    
    public $timestamps = true;
    
    protected $fillable = array('target', 'target_id', 'file_id', 'created_by', 'power');
    
	public function isFile() {
		return $this->hasOne('Files', 'id', 'file_id');
	}
    
}

class ShareApp extends Eloquent {
    
    protected $table = 'share_app_to';
    
    public $timestamps = true;
    
    protected $fillable = array('target', 'target_id', 'from_doc_id', 'to_doc_id', 'active', 'from_doc_id');
    
    public function target($target) {
        return $this->where('target', $target);
    }
    
    public function isApp() {
        return $this->hasOne('Apps', 'id', 'from_doc_id');
    }
    
}
	
class Apps extends Eloquent {

	protected $table = 'apps';
	
	public $timestamps = true;
	
	protected $fillable = array('user_id', 'file_id');
	
	public function user() {
		return $this->hasOne('User', 'id', 'user_id');
	}
	
	public function hasFiles() {
		return $this->hasMany('Files', 'owner');
	}
	
	public function isFile() {
		return $this->hasOne('Files', 'id', 'file_id');
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
		return $this->belongsToMany('Apps', 'auth_requester', 'requester_doc_id', 'preparer_doc_id');//未使用未測試
	}
	
	public static function getRequester() {

		return new Requester;
	}

}