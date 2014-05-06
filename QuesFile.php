<?php
namespace app\library\files\v0;
class QuesFile extends CommFile {
	
	/**
	 * @var rows
	 */
	public $rows;

	/**
	 * @var info
	 */
	public $info;

	public static $intent = array(
		'read_info',
		'save_info',
		'count',
	);
	
	public static function get_intent() {
		return array_merge(parent::$intent,self::$intent);
	}
	
	/**
	 * @param string
	 * @return
	 */
	public function read_info() {
		parent::create();
	}
	
	public function save_info() {	}
	
	public function count() {	}
	
	
}
