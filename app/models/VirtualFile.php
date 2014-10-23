<?php
class Files extends Eloquent {
	
	protected $table = 'files';
	
	public $timestamps = true;
	
	protected $fillable = array('title', 'type', 'owner', 'file', 'created_by');

}

class RequestFile extends Eloquent {
    
    protected $table = 'docs_requested';
    
    public $timestamps = true;
    
    protected $fillable = array('doc_id', 'target', 'target_id', 'created_by', 'description');
    
	public function isDoc() {
		return $this->hasOne('ShareFile', 'id', 'doc_id');
	}
    
}

class ShareFile extends Eloquent {
    
    protected $table = 'docs';
    
    public $timestamps = true;
    
    protected $fillable = array('target', 'target_id', 'file_id', 'created_by', 'power');
    
	public function isFile() {
		return $this->hasOne('Files', 'id', 'file_id');
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
	
	public function scopeFile($query) {
		return $query->leftJoin('files','docs.file_id','=','files.id');
	}

}

class RequestApp extends Eloquent {
    
    protected $table = 'apps_requested';
    
    public $timestamps = true;
    
    protected $fillable = array('app_id', 'target', 'target_id', 'created_by', 'description');
    
	public function isApp() {
		return $this->hasOne('Apps', 'id', 'app_id');
	}
    
}

class ShareApp extends Eloquent {
    
    protected $table = 'apps_shared';
    
    public $timestamps = true;
    
    protected $fillable = array('target', 'target_id', 'app_id', 'active');
    
    public function target($target) {
        return $this->where('target', $target);
    }
    
    public function isApp() {
        return $this->hasOne('Apps', 'id', 'app_id');
    }
    
}