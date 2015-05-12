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
    
    public function hasSharedDocs() {
        return $this->hasMany('ShareFile', 'file_id', 'file_id')->where('created_by', '=', Auth::user()->id)->where(function($query){
            $query->where('target', '<>', 'user')->orWhere('target_id', '<>', Auth::user()->id);
        });
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

class Struct_file
{  
    static function open($shareFile) 
    {
    	$fileProvider = app\library\files\v0\FileProvider::make();

		$link = [];
	    
	    switch($shareFile->isFile->type) {
	        case 1:
	            $intent_key = $fileProvider->doc_intent_key('open', $shareFile->id, 'app\\library\\files\\v0\\QuesFile');
	            $link['open'] = 'file/'.$intent_key.'/open';
	            $tools = ['codebook', 'receives', 'spss', 'report'];
	        break;
	        case 5:
	            $intent_key = $fileProvider->doc_intent_key('open', $shareFile->id, 'app\\library\\files\\v0\\RowsFile');
	            $link['open'] = 'file/'.$intent_key.'/open';
	        break;
	        case 7:
	            $intent_key = $fileProvider->doc_intent_key('open', $shareFile->id, 'app\\library\\files\\v0\\AnalysisFile');
	            $link['open'] = 'file/'.$intent_key.'/open';
	        break;
	        case 8:
	            $intent_key = $fileProvider->doc_intent_key('open', $shareFile->id, 'app\\library\\files\\v0\\ExamFile');
	            $link['open'] = 'file/'.$intent_key.'/open';
	        break;
	        default:              
	            $intent_key = $fileProvider->doc_intent_key('open', $shareFile->id, 'app\\library\\files\\v0\\CommFile');    
	            $link['open'] = 'file/'.$intent_key.'/open';
	        break;    
	    }

	    return [
	        'id'         => $shareFile->id,
	        'title'      => $shareFile->isFile->title,
	        'created_by' => $shareFile->created_by,
	        'created_at' => $shareFile->created_at->toIso8601String(),
	        'link'       => $link,
	        'type'       => $shareFile->isFile->type,
	        'intent_key' => $intent_key,
	        'tools'      => isset($tools) ? $tools : [],
	        'shared'     => array_count_values($shareFile->hasSharedDocs->map(function($sharedDocs){
	            return $sharedDocs->target;
	        })->all())
	    ];
    }
}