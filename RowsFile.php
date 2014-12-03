<?php
namespace app\library\files\v0;
use DB, View, Response, ShareFile, RequestFile, Auth, Input, Illuminate\Filesystem\Filesystem;

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
		'get_columns',
        'get_rows',
        'get_import_rows',
        'createTable',
        'requestTo',
        'export',
        'upload',
	);
	
	public static function get_intent() {
		return array_unique(array_merge(parent::$intent,self::$intent));
	}
	
	/**
	 * @var string
	 * @return
	 */	
	public function import() {
        
        //$requestFile = RequestFile::find($value['requested_file_id']);
        
        $shareFile = ShareFile::find($this->doc_id);   
        
        return 'demo.page.table_import';
        
    }
    
    public function createTable($tables, $title) {
        
        $filesystem = new Filesystem;
        
        $scheme = (object)['database'=>'', 'tables'=>$tables];
        
        foreach($scheme->tables as $index => $table) {
            //$scheme->tables[$index]->name;
            //--------待處理
        }
        
        $name = hash('md5', json_encode($scheme));       
        
        $filesystem->put( storage_path() . '/rows/temp/' . $name, json_encode($scheme) );
        
        $commFile = new CommFile;
        
        return $commFile->createFile(storage_path() . '/rows/temp/', $name, $title);
        
    }
    
    public function open() {
        
        return 'demo.page.table_open';
        
    }
	
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
        
        if( $shareFile->created_by!=Auth::user()->id && isset($shareFile->power) ) {
            $power = json_decode($shareFile->power);

            foreach($scheme->tables as $index => $table) {
                foreach($table->columns as $index => $column) {
                    if( !in_array($column->name, $power) ) {
                        unset($table->columns[$index]);
                    }
                }
            }
        }
        
        //$filesystem->put( storage_path() . '/' . $file, json_encode($columns) );
        //
        

        //var_dump(DB::getQueryLog());
        return Response::json($scheme->tables);
    }
    
    private function get_scheme($shareFile) {
        
        $file = $shareFile->isFile->file;
        
        $filesystem = new Filesystem;
        
        return json_decode($filesystem->get( storage_path() . '/file_upload/' . $file ));
    }
    
    public function get_power() {
        
        $shareFile = ShareFile::find($this->doc_id);
        
        $file = $shareFile->isFile->file;
        
        $filesystem = new Filesystem;
        
        $scheme = json_decode($filesystem->get( storage_path() . '/file_upload/' . $file ));
        
        return Response::json($scheme->power);
    }
    
    public function get_rows() {
        
        $shareFile = ShareFile::find($this->doc_id);
        
        $file = $shareFile->isFile->file;
        
        $filesystem = new Filesystem;
        
        $scheme = json_decode($filesystem->get( storage_path() . '/file_upload/' . $file ));
        
        $database = $scheme->database;
        
        $tables = $scheme->tables;    
        
        $power = array();
          
        foreach($tables as $index => $table){
            if( $index==0 ){
                $rows_query = DB::table($database.'.dbo.'.$table->name.' AS t0');
            }else{
                $rows_query->leftJoin($database.'.dbo.'.$table->name.' AS t'.$index, 't'.$index.'.'.$table->primaryKey, '=', 't0.'.$table->primaryKey);
            }   
            
            if( $shareFile->created_by==Auth::user()->id ) {
                foreach($table->columns as $column){
                    array_push($power, $column);
                }                
                //$power = array_fetch($table->columns, 'name');
            }else{
                //$power = json_decode($shareFile->power);
            }
        }
        
        
        $rows = $rows_query->paginate(50);
        //$rows =  DB::connection('sqlsrv')->table($database.'.dbo.'.$table)->select($power)->paginate(50);//->forPage(2000, 20)->get();
        
        return Response::json($rows);
    }	
    
    public function get_import_rows() {
        
        $requested_file_id = $this->doc_id;
        
        $columns =  DB::connection('sqlsrv')->table('ques_admin.dbo.contact')->paginate(50);//->forPage(2000, 20)->get();
        
        return Response::json($columns);
    }
    
    public function requestTo() {
        
        $input = Input::only('groups', 'file_id', 'description');
        $user = Auth::user();
        $myGroups = $user->groups;
        $file = ShareFile::find($this->doc_id);
        
        if( $file && $file->created_by == $user->id ) {
            
            foreach($input['groups'] as $group) {
                
                if( isset($group['selected']) && $group['selected'] && $myGroups->contains($group['id']) ) {
                    RequestFile::updateOrCreate(['target' => 'group', 'target_id' => $group['id'], 'doc_id' => $this->doc_id, 'created_by' => $user->id], ['description' => $input['description']]);
                }
                
            }
            
        }

        return Response::json(Input::all());
    }
    
	
	public function export() {
        
        $shareFile = ShareFile::find($this->doc_id);
        
        $scheme = $this->get_scheme($shareFile);        
                
        $database = $scheme->database;
        
        $table = $scheme->table;
        
        if( $shareFile->created_by==Auth::user()->id ) {
            $power = array_fetch($scheme->tables[0]->columns, 'name');
        }else{
            $power = json_decode($shareFile->power);
        }        
        
        $rows =  DB::connection('sqlsrv')->table($database.'.dbo.'.$table)->select($power)->get();//->paginate(50);//->forPage(2000, 20)->get();
        
        
        
        $output = '';
        $output .= implode(",", array_keys((array)$rows[0]));
        $output .=  "\n"; 
        foreach($rows as $row){               
            $output .= iconv("UTF-8", "big5//IGNORE", implode(",", (array)$row));
            $output .= "\n";
        }
        $headers = array(
            //'Content-Encoding' => 'UTF-8',
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ExportFileName.csv"',
        );

        return Response::make($output, 200, $headers);
        
    }

    public function uploadRows() {
        return Input::file('file');
        //return Input::file('file_upload');
    }
	
}
