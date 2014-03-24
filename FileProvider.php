<?php
namespace app\library\files\v0;
use DB, Session;
class FileProvider {
	
	private $files = array();
	
	/**
	 * @var string
	 * @return
	 */	
	public function lists() {
		//get lists and put session
		$fileClass = 'app\library\files\v0\RowsFile';
		if( class_exists($fileClass) ){
			$actives = $fileClass::get_intent();
		}		
		
		$docs = DB::table('doc')->where('owner',1)->select('id','title','folder','ctime')->get();
		foreach($docs as $doc){
			echo $doc->title.'<br />';
			foreach($actives as $active){
				$intent_key = $this->get_intent_uniqid();
				echo '<a href="fileManager/'.$intent_key.'">'.$active.'</a><br />';
				$intent = array('active'=>$active,'file_id'=>$doc->id,'fileClass'=>$fileClass);
				$this->files[$intent_key] = $intent;
			}
		}
		


		
		$this->save_intent();
	}
	
	private function auth_file() {
		
	}
	
	private function save_intent() {	
		Session::put('file',$this->files);
	}
	
	private function get_intent_uniqid() {
		
		while( true ){
			$key = md5(uniqid(rand()));		
			//$key = md5(uniqid());		
			if( !array_key_exists($key, $this->files) ){
				return $key;
			}
		}
		
	}
	
	
}
