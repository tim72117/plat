<?php
namespace app\library\files\v0;
use DB, View, Response, Session, Request, Redirect, Input, VirtualFile, Requester;

class CustomFile extends CommFile {
	
	/**
	 * @var array 2 dimension
	 */
	public $data;	
	
	/**
	 * @var array 1 dimension
	 */
	public $columns;	
	
	public static $intent = array(
		'import',
		'export',
		'receives',
		'get_columns',
		'request_to',
		'request_end',
		'open',
	);
	
	public static function get_intent() {
		return array_unique(array_merge(parent::$intent,self::$intent));
	}
	
	/**
	 * @var string
	 * @return
	 */	
	public function import() {		
		
		$id_doc_new = $this->upload(false);	
		$intent_key = Request::segment(3);

		$returner = Redirect::to('user/doc/'.Input::get('intent_key'));
		
		if( $id_doc_new && is_numeric($id_doc_new) ){
			Session::flash('upload_file_id', $id_doc_new);		
		}
		
		if( is_object($id_doc_new) && get_class($id_doc_new)=='Illuminate\Validation\Validator' ){
			$returner->withErrors($id_doc_new);
		}		
		
		return Redirect::to('user/doc/'.Input::get('intent_key'));	

	}
	
	public function export() {	}
	
	public function open($file_id) {		
		View::share('file_id',$file_id);
		$doc = VirtualFile::where('docs.id',$file_id)->File()->first();
		if( is_null($doc) )
			throw new FileFailedException;
		//var_dump(VirtualFile::has('isFiles')->where('docs.id',$file_id)->first());exit;
		return $doc->file;
	}	
	
	
	public function request_to() {		
		foreach(Input::get('group') as $target){
			$this_doc = VirtualFile::find($this->file_id);
			
			$doc = new VirtualFile(array(
				'user_id'  =>  $target,
				'file_id'  =>  $this_doc->file_id,
			));
			$doc->save();

			$requester = new Requester;
			$requester->preparer_doc_id = $doc->id;
			$requester->requester_doc_id = $this->file_id;
			$requester->save();
		}
		return Redirect::to('user/doc/'.Input::get('intent_key'));
	}
	
	public function request_end() {
		//未完成 file 轉移
		$docs_id = Input::get('doc');
		$docs = VirtualFile::with('requester')->whereIn('id', $docs_id)->get();
		foreach($docs as $doc){
			$requester = $doc->requester;
			if( $requester->requester_doc_id==$this->file_id ){
				$doc->requester->delete();
				$doc->delete();
			}
		}
		return Redirect::to('user/doc/'.Input::get('intent_key'));	
	}
	
	/**
	 * @return array
	 */	
	public function get_columns() {	}	
	
}
