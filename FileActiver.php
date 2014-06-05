<?php
namespace app\library\files\v0;
use Session, URL, Redirect;
class FileActiver {	
	
	/**
	 * @var array 2 dimension
	 */
	public $file_list;
	public $intent_key;
	public $file_id;
	
	public function accept($intent_key) {
		
		
			
		$this->intent_key = $intent_key;
		$intent = Session::get('file')[$intent_key];
		$this->file_id = $intent['file_id'];
		$active = $intent['active'];
		
		if( is_null($this->file_id) && $active=='upload' ){
			$file = new $intent['fileClass']($this->file_id);
			$file_id = $file->$active(true);
			if( $file_id && is_numeric($file_id) ){
				Session::flash('upload_file_id', $file_id);		
			}
			$returner = Redirect::to('page/upload');
			if( is_object($file_id) && get_class($file_id)=='Illuminate\Validation\Validator' ){
				$returner->withErrors($file_id);
			}	
			return $returner;	
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