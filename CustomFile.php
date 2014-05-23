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
		$file_id = $this->upload();	
		if( $file_id ){
			Session::flash('upload_file_id', $file_id);		
		}
		$intent_key = Request::segment(3);
		return Redirect::to('user/doc/'.Input::get('intent_key'));	

	}
	
	public function export() {	}
	
	public function open($file_id) {
		$docs = DB::table('doc')->where('id',$file_id)->select('file')->first();
		return View::make('demo.use.main')->nest('context',$docs->file);
	}
	
	/**
	 * @return array
	 */	
	public function get_columns() {	}	
	
}
