<?php
namespace app\library\files\v0;
use Input, Auth, DB, Validator, VirtualFile, Files, ShareFile, Illuminate\Filesystem\Filesystem, Illuminate\Support\MessageBag;
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
		'createFile',
		'delete',
		'rename',
		'save',
		'save_as',
		'share_to',				
		'open',
		'download',
	);	
	
	public function __construct($doc_id = null){
		$this->doc_id = $doc_id;
	}
	
	public static function get_intent() {
		return self::$intent;
	}
	
	/**
	 * @param string
	 * @return
	 */
	public function createFile($original_path, $name, $title) {
        
		$user_id = Auth::user()->id;	
        
        $storage_path = storage_path().'/file_upload';
        
        $parts = array_slice(str_split($hash = md5($user_id), 2), 0, 2);
		
        $path = join('/', $parts);
        
        $filesystem = new Filesystem();   
            
        try	
        {				
            $filesystem->makeDirectory(dirname($storage_path.'/'.$path.'/'.$name), 0777, true, true);									

            $filesystem->move($original_path . $name, $storage_path.'/'.$path. '/' . $name);
        }
        catch (\Exception $e)
        {
            throw $e;
        } 
        
        $file = Files::updateOrCreate([                
            'type'       =>   5,
            'owner'      =>   0,
            'file'       =>   $path.'/'.$name,
            'created_by' =>   $user_id,
        ],[
            'title'      =>   $title,
        ]);
        
        $shareFile = ShareFile::updateOrCreate([
            'target'     => 'user',
            'target_id'  => $user_id,
            'file_id'    => $file->id,
            'created_by' => $user_id,
        ],[
            //'power'      => json_encode([]),
        ]); 
        
        return $shareFile->id;
            
	}
	
	public function delete() {	}
	
	public function rename() {	}
	
	public function open() { 
        return $this->download();
    }
	

	public function upload($visible = true) {	

		if( Input::hasFile('file_upload') ){
			
			$file = Input::file('file_upload');
			//$mime = $file->getMimeType();
			$name_real = $file->getClientOriginalName();
			$user_id = Auth::user()->id;
			
            //未處理
			if( is_null($this->doc_id) ){
				$doc_id = $user_id;
			}else{
				$doc_id = $this->doc_id;
			}

			
			$validator = Validator::make(
					array('file_upload' => Input::file('file_upload')),
					array('file_upload' => 'required|max:8000'),
					[
                        'file_upload.max' => '檔案太大',
                    ]
			);
			
			if( $validator->fails() ){
                throw new ValidateException($validator);
			}			
			
			$storage_path = storage_path().'/file_upload';
			$name = hash_file('md5', $file->getRealPath());			
			
			$parts = array_slice(str_split($hash = md5($user_id), 2), 0, 2);
			$path = join('/', $parts);
			    
			
			if( !Files::where('file', $path.'/'.$name)->exists() ){
				
                $filesystem = new Filesystem();   
                
				try	
				{				
					$filesystem->makeDirectory(dirname($storage_path.'/'.$path.'/'.$name), 0777, true, true);									

					$file->move($storage_path.'/'.$path, $name);
					
					$file = new Files(array(
						'title'      =>   $name_real,
						'type'       =>   3,
						'owner'      =>   $doc_id,
						'file'       =>   $path.'/'.$name,
                        'created_by' =>   $user_id,
					));

					$file->save();

					//$file_id = DB::table('auth')->insertGetId(array(
					//	'id_user'   =>   $user_id,
					//	'id_doc'    =>   $id_doc,
					//	'visible'   =>   $visible,
					//));	
                    
                    DB::table('log_file')->insert(array('file_id'=>$file->id,'active'=>'upload'));

					return $file->id;			

				}
				catch (\Exception $e)
				{
					throw $e;
				}
				
			}else{
				
				$file = Files::where('file', $path.'/'.$name)->first();
                
				$file->touch();
                
                DB::table('log_file')->insert(array('file_id'=>$file->id,'active'=>'reUpload'));
                
				return $file->id;
				
			}
			
		}else{
            
            throw new ValidateException(new MessageBag(array('no_upload'=>'檔案大小錯誤')));
            
        }	
        
	}
	
	public function download() {
		$storage_path = storage_path().'/file_upload';
        
		$file = Files::find($this->doc_id);
        
		$file_path = $file->file;
        
        if( !file_exists($storage_path.'/'.$file_path) )
            throw new FileFailedException;
        
        return array('path'=>$storage_path.'/'.$file_path, 'name'=>$file->title);
	}
	
	public function save_as() { }
	
	public function share_to() { }
	
	public function get_auth() {
		return $this->auth;
	}
	
	
	
}
