<?php
namespace app\library\files\v0;
use DB, View, Response, Session, Request, Redirect, Input;
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
		$doc = DB::table('docs')->leftJoin('doc','docs.id_doc','=','doc.id')->where('docs.id',$file_id)->select('doc.file')->first();
		return View::make('demo.use.main')->nest('context',$doc->file);
	}
	
	/**
	 * @return array
	 */	
	public function get_columns() {	}	
	
}
