<?php
namespace app\library\files\v0;
class File {
	
	/**
	 * @var string
	 */
	public $name;
	
	/**
	 * @var int
	 */
	public $id;
	
	/**
	 * @var auth
	 */
	private $auth;
	
	/**
	 * @var active
	 */
	public static $intent = array(
		'create',
		'delete',
		'rename',
		'save',
		'save_as',
		'share_to',
	);
	
	public static function get_intent() {
		return self::$intent;
	}
	
	/**
	 * @param string
	 * @return
	 */
	public function create() {
		echo 'create';
	}
	
	public function delete() {	}
	
	public function rename() {	}
	
	public function upload() {	}
	
	//public function save() {	}
	
	public function save_as() { }
	
	public function share_to() {	}
	
	public function get_auth() {
		return $this->auth;
	}
	
	
	
}
