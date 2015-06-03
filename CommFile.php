<?php
namespace app\library\files\v0;
use Input, Auth, DB, Response, Validator, Files, ShareFile, Session, Illuminate\Filesystem\Filesystem, Illuminate\Support\MessageBag;

class CommFile {
	
	public $doc_id;
    
    public $storage_path;
	
	public function __construct($shareFile)
	{      
		if( gettype($shareFile) != 'object' )
			$shareFile = ShareFile::find($shareFile);//暫時

		$this->shareFile = $shareFile;

		$this->file = $this->shareFile->isFile;

		$this->user = Auth::user();

        $this->storage_path = storage_path() . '/file_upload';

        if( is_null($this->file) )
			throw new FileFailedException; 
	}

	public function get_views() 
	{
		return [];
	}
    
	public static function create($newFile)
	{        
        $file = Files::create([      
        	'title'      => $newFile->title,          
            'type'       => $newFile->type,
            'owner'      => 0,
            'created_by' => Auth::user()->id,
        ], [
            
        ]);
        
        $shareFile = ShareFile::create([
        	'file_id'    => $file->id,
            'target'     => 'user',
            'target_id'  => Auth::user()->id,            
            'created_by' => Auth::user()->id,
        ], [
            //'power'      => json_encode([]),
        ]); 
        
        return $shareFile;            
	}

	public function open()
	{ 
        return $this->download();
    }	
	
	public function delete()
	{    
        $shareFile = ShareFile::find($this->doc_id);

        return $this->doc_id;
    }
	
	public function rename() {	}

	public static function upload($visible = true)
	{
		if( Input::hasFile('file_upload') ) {			
			$file_upload = Input::file('file_upload');
			//$mime = $file->getMimeType();
			$name_real = $file_upload->getClientOriginalName();
			$user_id = Auth::user()->id;
			
			$validator = Validator::make(
				array('file_upload'     => $file_upload),
				array('file_upload'     => 'required|max:8000000'),
				array('file_upload.max' => '檔案太大')    
			);
			
			if( $validator->fails() ) {
                throw new ValidateException($validator);
			}			
			
			$name = hash_file('md5', $file_upload->getRealPath());
			
			$parts = array_slice(str_split($hash = md5($user_id), 2), 0, 2);
			$path = join('/', $parts);	    

			self::move($file_upload, $path . '/' . $name);

			$file = Files::updateOrCreate([
				'file'       => $path . '/' . $name,
				'created_by' => $user_id
			], [
				'title'      => $name_real,
				'type'       => 3,
				'owner'      => 0,
			]);

			return $file;
			
		}else{

            throw new ValidateException(new MessageBag(array('no_upload'=>'檔案大小錯誤')));
            
        }        
	}

	public static function move($file, $path)
	{
		$storage_path = storage_path() . '/file_upload';

		if( file_exists($storage_path . '/' . $path) )
			return;

		try	
		{
			$filesystem = new Filesystem();

			$filesystem->makeDirectory(dirname($storage_path . '/' . $path), 0777, true, true);									

			$file->move(dirname($storage_path . '/' . $path), basename($path));
		}
		catch (\Exception $e)
		{
			throw $e;
		}
	}
	
	public function download()
	{
        
		$file_path = $this->file->file;
        
        if( !file_exists($this->storage_path . '/' . $file_path) )
            throw new FileFailedException;
        
        return Response::download($this->storage_path . '/' . $file_path, $this->file->title);
	}
	
	public function save_as() { }
	
	public function share_to() { }    
}
