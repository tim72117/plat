<?php
namespace app\library\files\v0;
use DB,View,Response;
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
		return array_merge(parent::$intent,self::$intent);
	}
	
	/**
	 * @var string
	 * @return
	 */	
	public function import() {	}
	
	public function export() {	}
	
	public function open($file_id) {
		$docs = DB::table('doc')->where('id',$file_id)->select('file')->first();
		$contents = View::make('demo.use.main')->nest('context',$docs->file);
		
		$response = Response::make($contents, 200);
		$response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
		$response->header('Pragma', 'no-cache');
		$response->header('Last-Modified', gmdate( 'D, d M Y H:i:s' ).' GMT');
		return $response;
	}
	
	/**
	 * @return array
	 */	
	public function get_columns() {	}	
	
}
