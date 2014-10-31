<?php
namespace app\library\files\v0;
use Session, URL, Redirect, Response;
class FileActiver {	
	
	/**
	 * @var array 2 dimension
	 */
	public $intent;
    
    public function __construct($intent_key){
        
        $this->intent = Session::get('file')[$intent_key];
        
    }
	
	public function accept($method) {
        
        $active = $this->intent['active'];
        
        $app_id = isset($this->intent['app_id']) ? $this->intent['app_id'] : null;
        
        $file = new $this->intent['fileClass']($app_id);
        
        return $file->$method();
		
	}
	
}