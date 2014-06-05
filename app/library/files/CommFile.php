<?php
namespace app\library\files\v0;
use Input, Auth, DB, Validator, VirtualFile, Illuminate\Filesystem\Filesystem;
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
	
	public $doc_id;
	
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
	
	public function __construct($doc_id){
		$this->doc_id = $doc_id;
	}
	
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
	
	public function upload($visible = true) {	
		

		if (Input::hasFile('file_upload')){
			
			$file = Input::file('file_upload');
			$mime = $file->getMimeType();
			$name_real = $file->getClientOriginalName();
			$id_user = Auth::user()->id;
			
			if( is_null($this->doc_id) ){
				$doc_id = $id_user;
			}else{
				$doc_id = $this->doc_id;
			}
			
			
			$validator = Validator::make(
					array('file_upload' => Input::file('file_upload')),
					array('file_upload' => 'required|mimes:xls,xlsx,pdf'),
					array('file_upload.mimes' => '檔案格式錯誤')
			);
			
			if( $validator->fails() ){
				return $validator;
			}
			
			
			$storage_path = storage_path().'/file_upload';
			$name = hash_file('md5', $file->getRealPath());			
			
			$parts = array_slice(str_split($hash = md5($id_user), 2), 0, 2);
			$path = join('/', $parts);
			
			
			$filesystem = new Filesystem();
			
			if( !DB::table('files')->where('file', $path.'/'.$name)->exists() ){
				
				try	
				{				
					$filesystem->makeDirectory(dirname($storage_path.'/'.$path.'/'.$name), 0777, true, true);									

					$file->move($storage_path.'/'.$path, $name);				

					$file_id = DB::table('files')->insertGetId(array(
						'title'   =>   $name_real,
						'type'    =>   3,
						'owner'   =>   $doc_id,
						'file'    =>   $path.'/'.$name,
					));	

					//$file_id = DB::table('auth')->insertGetId(array(
					//	'id_user'   =>   $id_user,
					//	'id_doc'    =>   $id_doc,
					//	'visible'   =>   $visible,
					//));	

					return $file_id;			

				}
				catch (\Exception $e)
				{
					throw $e;
				}
				
			}else{
				
				$file = DB::table('files')->where('file', $path.'/'.$name)->first();
				return $file->id;
				$validator->getMessageBag()->add('file_upload', '檔案已上傳');
				return $validator;
				
			}
			
		}		
	}
	
	//public function save() {	}
	
	public function save_as() { }
	
	public function share_to() { }
	
	public function get_auth() {
		return $this->auth;
	}
	
	
	
}
