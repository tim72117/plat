<?php
namespace app\library\files\v0;
use Input, Auth, DB, Validator, VirtualFile, Requester, Illuminate\Filesystem\Filesystem;
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
	
	public $file_id;
	
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
		'request_to',
		'open',
	);	
	
	public function __construct($file_id){
		$this->file_id = $file_id;
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
			$virtualFile = VirtualFile::find($this->file_id);
			
			$validator = Validator::make(
					array('file_upload' => Input::file('file_upload')),
					array('file_upload' => 'required|mimes:xls,xlsx'),
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

					$id_doc = DB::table('files')->insertGetId(array(
						'title'   =>   $name_real,
						'type'    =>   3,
						'owner'   =>   $virtualFile->id,
						'file'    =>   $path.'/'.$name,
					));	

					//$file_id = DB::table('auth')->insertGetId(array(
					//	'id_user'   =>   $id_user,
					//	'id_doc'    =>   $id_doc,
					//	'visible'   =>   $visible,
					//));	

					return $id_doc;			

				}
				catch (\Exception $e)
				{
					throw $e;
				}
				
			}else{
				
				$validator->getMessageBag()->add('file_upload', '檔案已上傳');
				return $validator;
				
			}
			
		}		
	}
	
	//public function save() {	}
	
	public function save_as() { }
	
	public function share_to() { }
	
	public function request_to() {
		
		foreach(Input::get('group') as $target){
			$this_file = VirtualFile::find($this->file_id);
			
			$doc = new VirtualFile(array(
				'id_user'  =>  $target,
				'id_file'  =>  $this_file->id_file,
			));
			$doc->save();

			$requester = new Requester;
			$requester->id_doc = $doc->id;
			$requester->id_requester = $this->file_id;
			$requester->save();
		}

	}
	
	public function get_auth() {
		return $this->auth;
	}
	
	
	
}
