<?php
namespace app\library\files\v0;
use Session;
class FileActiver {	
	
	/**
	 * @var array 2 dimension
	 */
	public $file_list;
	
	public function accept($intent_key) {
		
		$intent = Session::get('file')[$intent_key];
		$file_id = $intent['file_id'];
		$active = $intent['active'];
		$builderName = $intent['fileClass'].'Builder';
		
		return $builderName::view($intent);
		//$file = new $intent['fileClass'];
		//$file->$active();
		//echo $active_uniqid;
	}
	
	
}