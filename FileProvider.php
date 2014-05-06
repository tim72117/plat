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
		
	
		
		$docs = DB::table('doc')->leftJoin('doc_type','doc.type','=','doc_type.id')->select('doc.id','title','doc_type.class')->get();
		
		
		
		$packageDocs = array();
		
		foreach($docs as $doc){
			$fileClass = 'app\\library\\files\\v0\\'.$doc->class;
			if( class_exists($fileClass) ){

				$actives = $fileClass::get_intent();
				$packageDoc = array('title'=>$doc->title, 'actives'=>array());
				foreach($actives as $active){
					$intent_key = $this->get_intent_uniqid();
					array_push($packageDoc['actives'], array('intent_key'=>$intent_key, 'active'=>$active));
					$intent = array('active'=>$active,'file_id'=>$doc->id,'fileClass'=>$fileClass);
					$this->files[$intent_key] = $intent;
				}
				array_push($packageDocs, $packageDoc);
			}	

		}
		
		$this->save_intent();
		return $packageDocs;
	}
	
	public function create_list() {		
		$create_list = array();
		$fileClasss = array('QuesFile','RowsFile');		
		foreach($fileClasss as $fileClass){
			$intent_key = $this->get_intent_uniqid();
			array_push($create_list, array('intent_key'=>$intent_key, 'active'=>$fileClass.'_create'));
			$intent = array('active'=>'create','file_id'=>null,'fileClass'=>$fileClass);			
			$this->files[$intent_key]= $intent;
		}
		$this->save_intent();
		return $create_list;
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
