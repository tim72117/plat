<?php
namespace app\library\files\v0;
use Session, URL, Redirect, Response;
class FileActiver {	
	
	/**
	 * @var array 2 dimension
	 */
	public $file_list;
	public $intent_key;
	public $file_id;
    
    public static function active($intent_key) {
        
        $intent = Session::get('file')[$intent_key];
        
        return $intent;        
    }
	
	public function accept($intent_key) {	
		
			
		$this->intent_key = $intent_key;
		$intent = Session::get('file')[$intent_key];
		$this->file_id = $intent['file_id'];
		$active = $intent['active'];
		
		if( is_null($this->file_id) && $active=='upload' ){
			$file = new $intent['fileClass']($this->file_id);
            
			$file_id = $file->$active(true);
			
			Session::flash('upload_file_id', $file_id);		

			return Redirect::back();
		}
		
		if( $active=='download' ){
			$file = new $intent['fileClass']($this->file_id);
			$file_fullPath = $file->$active(true);
			return call_user_func_array('Response::download', $file_fullPath);
		}
		
		if( $intent['fileClass']=='app\\library\\files\\v0\\CustomFile' ){
			$file = new $intent['fileClass']($this->file_id);
			return $file->$active($this->file_id);
		}
		
		//$builderName = $intent['fileClass'].'Builder';
		
		//return $builderName::view($intent);
		//$file = new $intent['fileClass'];
		//$file->$active();
		//echo $active_uniqid;
	}
	
	public function get_post_url() {
		return URL::to('user/doc/'.$this->intent_key);
	}
	
	
}