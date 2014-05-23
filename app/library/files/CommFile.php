<?php
namespace app\library\files\v0;
use Input, Auth, DB, Illuminate\Filesystem\Filesystem;
class CommFile {
	
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
		'open',
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
	
	public function open($file_id) {  }
	
	public function upload() {	
		if (Input::hasFile('file_upload')){
			
			$file = Input::file('file_upload');
			$storage_path = storage_path();
			$name = hash_file('md5', $file->getRealPath());
			
			$key = Auth::user()->id;
			$parts = array_slice(str_split($hash = md5($key), 2), 0, 2);
			$path = 'file_upload/'.join('/', $parts);
			
			
			$filesystem = new Filesystem();
			
			if( !DB::table('doc')->where('file', $path.'/'.$name)->exists() ){
				
				try	
				{				
					$filesystem->makeDirectory(dirname($storage_path.'/'.$path.'/'.$name), 0777, true, true);			

					$name_real = $file->getClientOriginalName();
					$mime = $file->getMimeType();

					$file->move($storage_path.'/'.$path, $name);				

					$file_id = DB::table('doc')->insertGetId(array(
						'title'   =>   $name_real,
						'type'    =>   3,
						'owner'   =>   1,
						'file'    =>   $path.'/'.$name,
					));	

					DB::table('auth')->insert(array(
						'id_user'   =>   $key,
						'id_doc'    =>   $file_id,
					));	

					return $file_id;			

				}
				catch (\Exception $e)
				{
					throw $e;
				}
				
			}else{
				return false;
			}
			
		}		
	}
	
	//public function save() {	}
	
	public function save_as() { }
	
	public function share_to() {	}
	
	public function get_auth() {
		return $this->auth;
	}
	
	
	
}
