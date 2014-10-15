<?php
namespace app\library\files\v0;
use DB, View, Response, VirtualFile, ShareFile, RequestFile, DOMDocument, Auth, Illuminate\Filesystem\Filesystem;

class RowsFile extends CommFile {
	
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
        'open',
		'export',
		'receives',
		'get_columns',
        'get_rows',
        'get_import_rows',
        'create',
	);
	
	public static function get_intent() {
		return array_unique(array_merge(parent::$intent,self::$intent));
	}
	
	/**
	 * @var string
	 * @return
	 */	
	public function import() {
        $requested_file_id = $this->doc_id;
        $requestFile = RequestFile::find($requested_file_id);
        $shareFile = ShareFile::find($requestFile->share_file_id);
        
        $fileProvider = FileProvider::make();
        $link = [
            'get_columns'     => $fileProvider->doc_intent_key('get_columns', $shareFile->id, 'app\\library\\files\\v0\\RowsFile'),
            'get_import_rows' => $fileProvider->doc_intent_key('get_import_rows', $requestFile->id, 'app\\library\\files\\v0\\RowsFile')
        ];
        
        View::share('link', $link);        
        
        return 'demo.use.page.table_import';
    }
    
    public function createTable($tables, $title) {
        
        $filesystem = new Filesystem;
        
        $scheme = (object)['database'=>'', 'tables'=>$tables];
        
        $name = hash('md5', json_encode($scheme));       
        
        $filesystem->put( storage_path() . '/rows/temp/' . $name, json_encode($scheme) );
        
        $commFile = new CommFile;
        
        return $commFile->createFile(storage_path() . '/rows/temp/', $name, $title);
        
    }
    
    public function open() {
        return 'demo.use.page.table';
    }
	
	public function export() {	}
	
	/**
	 * @return array
	 */	
	public function get_columns() {	

        $shareFile = ShareFile::find($this->doc_id);
        $file = $shareFile->isFile->file;
        
        $filesystem = new Filesystem;
        
        $scheme = json_decode($filesystem->get( storage_path() . '/file_upload/' . $file ));
        
        //$database = $columns_json->database;

        //$columns = DB::table('use_103.sys.columns')->whereRaw("object_id=OBJECT_ID('use_103.dbo.seniorOne103_userinfo')")->select('name', DB::raw("'' AS description"))->get('description', 'name');
        
        if( isset($shareFile->power) ) {
            $power = json_decode($shareFile->power);

            foreach($scheme->tables as $index => $table){
                foreach($table->columns as $index => $column){
                    //if( !in_array($column->name, $power) )
                        //unset($columns_json->columns[$index]);
                }
            }
        }
        
        //$filesystem->put( storage_path() . '/' . $file, json_encode($columns) );
        //
        

        //var_dump(DB::getQueryLog());
        return Response::json($scheme->tables);
    }	
    public function get_rows() {
        
        $file = ShareFile::find($this->doc_id)->isFile->file;
        
        $filesystem = new Filesystem;
        
        $scheme = json_decode($filesystem->get( storage_path() . '/file_upload/' . $file ));
        
        $database = $scheme->database;
        
        $columns =  DB::connection('sqlsrv_')->table($database.'.dbo.seniorOne103_userinfo')->paginate(50);//->forPage(2000, 20)->get();
        
        return Response::json($columns);
    }	
    
    public function get_import_rows() {
        
        $requested_file_id = $this->doc_id;
        
        $columns =  DB::connection('sqlsrv_')->table('use_103.dbo.seniorOne103_userinfo')->where('created_by', Auth::user()->id)->paginate(50);//->forPage(2000, 20)->get();
        
        return Response::json($columns);
    }
	
}
