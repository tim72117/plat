<?php
namespace app\library\files\v0;
use DB;
class RowsFile extends File {
	
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
		'get_columns',
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
	
	/**
	 * @return array
	 */	
	public function get_columns() {	}	
	
}
