<?php
namespace app\library\files\v0;

use User;
use Files;
use Input, DB, Response, Validator, Session;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\MessageBag;

class CommFile {

	protected $doc;
    
    public $storage_path;
	
	public function __construct(Files $file, User $user)
	{ 
        if (is_null($file))
			throw new FileFailedException; 

		$this->file = $file;

		$this->user = $user;

        $this->storage_path = storage_path() . '/file_upload';
	}

	public function get_views() 
	{
		return [];
	}
    
	public static function create($fileInfo)
	{
        $file = Files::create([
        	'title'      => $fileInfo->title,
            'type'       => $fileInfo->type,
            'owner'      => 0,
            'created_by' => $this->user->id,
        ], []);

        return new self($file);
	}

	public function open()
	{ 
        return $this->download();
    }	
	
    //uncomplete
	public function delete()
	{    
        return $this->doc->id;
    }
	
	public function rename()
	{
		if ($this->isCreater()) {
			$this->file->title = Input::get('title');

			$this->doc->touch();

			$this->doc->push();
		}

		return ['file' => \Struct_file::open($this->doc)];
	}

	public static function upload($visible = true)
	{
		if( Input::hasFile('file_upload') ) {			
			$file_upload = Input::file('file_upload');
			//$mime = $file->getMimeType();
			$name_real = $file_upload->getClientOriginalName();
			$user_id = $this->user->id;
			
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

	public function getId()
	{
		return $this->file->id;
	}

	public function setDoc($doc)
	{
		$this->doc = $doc;
	}

    public function isCreater()
    {
        return isset($this->doc) && $this->doc->created_by == $this->user->id;
    }
	
	public function save_as() { }
	
	public function share_to() { }    
}
