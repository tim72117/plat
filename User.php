<?php
namespace app\library\files\v0;
use DB;
class User {
	
	/**
	 * @var string
	 */
	public $name;
	
	/**
	 * @var int
	 */
	public $id;
	
	/**
	 * @var array
	 */
	public $contace;
	
	/**
	 * @var array
	 */
	public $fileProvider;
	
	/**
	 * @var array
	 */
	public $fileManager;
	
	public function get_file_provider() {
		$this->fileProvider = new FileProvider();
		return $this->fileProvider;
	}
	
	/**
	 * @var string
	 * @return
	 */
	public function friend_add() {
		$this->fileManager = new FileManager();
	}
	
	public function friend_delete() {
		
	}
	
	public function friend_group() {
		
	}	
	
	public function update_contact() {
		
	}
	
	public function save_contact() {
		
	}
	
	
}
