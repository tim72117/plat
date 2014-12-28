<?php
namespace app\library\files\v0;
use DB, View, Schema, Response, ShareFile, RequestFile, Auth, Input, Illuminate\Filesystem\Filesystem;

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
        'save_struct',
        'requestTo',
        'export',
        'upload',
        'save_import_rows',
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
    
    public function create_table() {
        
        $filesystem = new Filesystem;
        
        $scheme = $this->get_struct(); 
     
        $name = hash('md5', json_encode($scheme));       
        
        $filesystem->put( storage_path() . '/rows/temp/' . $name, json_encode($scheme) );
        
        $commFile = new CommFile;
        
        return $commFile->createFile(storage_path() . '/rows/temp/', $name, Input::only('title')['title']);
        
    }
    
    public function save_table() {
        
        $shareFile = ShareFile::find($this->doc_id);
        
        if( $shareFile->created_by==Auth::user()->id ){
            
            $filesystem = new Filesystem;
            
            $scheme_old = $this->get_scheme(ShareFile::find($this->doc_id));
            
            $scheme = $this->get_struct($scheme_old);
            
            $file = $shareFile->isFile;
            
            $file->title = Input::only('title')['title'];
            
            $file->save();
            
            $filesystem->put( storage_path() . '/file_upload/' . $file->file, json_encode($scheme) );
            
        }
        return json_encode($scheme);
    }
    
    private function get_struct($scheme_old = null) {     
        
        $sheets = Input::only('sheets')['sheets'];
        
        $struct = (object)['power'=> (object)['edit_column'=>0, 'edit_row'=>false, 'edit'=>true], 'sheets' =>[]];
        
        foreach ($sheets as $index => $sheet) {
            
            $sheet_old = isset($scheme_old) ? $scheme_old->sheets[$index] : null;
       
            $name = ( isset($sheet['name']) && isset($sheet_old) && ($sheet['name'] == $sheet_old->tables[0]->name) ) ? $sheet['name'] : md5(uniqid(time(), true));

            $sheet_new = (object)[
                'tables' =>[(object)[
                    'database'   => 'rowdata',
                    'name'       => $name,
                    'primaryKey' => 'id',
                    'columns'    => []
                ]]
            ];

            foreach ($sheet['colHeaders'] as $columns) {
                array_push($sheet_new->tables[0]->columns, (object)[
                    'name'   => $columns["data"],
                    'title'  => $columns["title"],
                    'rules'  => $columns["rules"]["key"],
                    'types'  => $columns["types"]["type"],
                    'link'   => $columns["link"],
                    'unique' => $columns["unique"]
                ]);
            }
            
            array_push($struct->sheets, $sheet_new);
            
            $this->updateOrCreateScheme($sheet_new, $sheet_old);
            
        }
        
        return $struct;
        
    }
    
    private function updateOrCreateScheme($sheet_new, $sheet_old) {
                
        $columns_old = isset($sheet_old) ? $sheet_old->tables[0]->columns : [];
        $columns_new = $sheet_new->tables[0]->columns;

        if( DB::table('rowdata.dbo.sysobjects')->where('name', $sheet_new->tables[0]->name)->exists() ) {

            Schema::table('rowdata.dbo.'.$sheet_new->tables[0]->name, function($table) use($columns_old, $columns_new) {

                $columns_old_names = array_fetch($columns_old, 'name');
                $columns_new_names = array_fetch($columns_new, 'name');

                foreach ($columns_old_names as $old_name){
                    if( !in_array($old_name, $columns_new_names) ){

                        $table->dropColumn($old_name);

                    }else{

                    }
                }

                foreach ($columns_new as $column_new){
                    if( !in_array($column_new->name, $columns_old_names) ){

                        $this->add_scheme_column($table, $column_new->name, $column_new->types);

                    }else{

                    }
                }


            });

        }else{
            
            Schema::create('rowdata.dbo.'.$sheet_new->tables[0]->name, function($table) use($columns_new) {

                $table->increments('id');

                $table->timestamps();
                //$table->dateTime('created_at');
                //$table->dateTime('updated_at');
                $table->dateTime('deleted_at')->nullable();
                $table->integer('created_by');

                foreach ($columns_new as $column_new) {
                    $this->add_scheme_column($table, $column_new->name, $column_new->types);
                }

            });

        }
        
    }
    
    private function add_scheme_column($table, $name, $type) {
        if($type == 'int'){
            $table->integer($name)->nullable();
        }
        if($type == 'float'){
            $table->float($name)->nullable();
        }
        if($type == 'nvarchar'){
            $table->string($name, 50)->nullable();
        }
        if($type == 'varchar'){
            $table->string($name, 50)->nullable();
        }
        if($type == 'date'){
            $table->date($name)->nullable();
        }
        if($type == 'bit'){
            $table->integer($name)->nullable();
        }
        if($type == 'text'){
            $table->text($name)->nullable();
        }
    }
    
    public function open() {
        
        $shareFile = ShareFile::find($this->doc_id);  
        
        $scheme = $this->get_scheme($shareFile);
        
        if( $shareFile->created_by==Auth::user()->id && $scheme->power->edit ){
            return 'demo.page.table_editor';
        }else{
            return 'demo.page.table_open';
        }       
        
    }
    
    private function get_scheme($shareFile) {
        
        $file = $shareFile->isFile->file;
        
        $filesystem = new Filesystem;
        
        return json_decode($filesystem->get( storage_path() . '/file_upload/' . $file ));
    }
    
	public function get_columns() {	

        $shareFile = ShareFile::find($this->doc_id);
        
        $scheme = $this->get_scheme($shareFile);
        
        $sheets = $scheme->sheets;

        //var_dump($scheme);

        //$columns = DB::table('use_103.sys.columns')->whereRaw("object_id=OBJECT_ID('use_103.dbo.seniorOne103_userinfo')")->select('name', DB::raw("'' AS description"))->get('description', 'name');
        
        $power = array();
        
        $title = $shareFile->isFile->title;        
        
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

        return Response::json(['sheets'=>$sheets, 'title'=>$title]);
    }

    public function get_power() {
        
        $shareFile = ShareFile::find($this->doc_id);
        
        $file = $shareFile->isFile->file;
        
        $filesystem = new Filesystem;
        
        $scheme = json_decode($filesystem->get( storage_path() . '/file_upload/' . $file ));
        
        return Response::json($scheme->power);
    }
    
    private function get_rows_query() {
        $shareFile = ShareFile::find($this->doc_id);
        
        $scheme = $this->get_scheme($shareFile);
        
        $sheets = $scheme->sheets;        
        
        $tables = $sheets[0]->tables; 
        
        $power = array();
        
        foreach($tables as $index => $table){
            
            $database = $table->database;

            if( $index==0 ){
                $rows_query = DB::table($database.'.dbo.'.$table->name.' AS t0');
            }else{
                $rows_query->leftJoin($database.'.dbo.'.$table->name.' AS t'.$index, 't'.$index.'.'.$table->primaryKey, '=', 't0.'.$table->primaryKey);
            }    

            if( $shareFile->created_by==Auth::user()->id ) {              
                //$power = array_map(function($column){return $column->name;}, $table->columns);
                $power = array_merge($power, array_map(function($column)use($index){return 't'.$index.'.'.$column->name;}, $table->columns));
                //$power = array_fetch($table->columns, 'name');
            }else{
                $power = array_merge($power, array_map(function($column)use($index){return 't'.$index.'.'.$column->name;}, $table->columns));
            }

        }
        return [$rows_query, $power];
    }
    
    public function get_rows() {

        list($rows_query, $power) = $this->get_rows_query();
        
        $rows = $rows_query->select($power)->paginate(50);
        //$rows =  DB::connection('sqlsrv')->table($database.'.dbo.'.$table)->select($power)->paginate(50);//->forPage(2000, 20)->get();

        return Response::json($rows);
    }	
    
    public function get_import_rows() {
        
        list($rows_query, $power) = $this->get_rows_query();
        
        $rows = $rows_query->where('created_by', Auth::user()->id)->select($power)->paginate(50);

        return Response::json($rows);
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

	 public function save_import_rows() {

		$input_sheets = Input::only('sheets')['sheets'];
		$shareFile = ShareFile::find($this->doc_id);
        $scheme = $this->get_scheme($shareFile);
					
/*		foreach($input as $index => $data) { //run sheets
			foreach($data as $data){ 		 //run tables
					var_dump($data['rows']);
					var_dump($data['colHeaders']);
			}
		}
*/
		foreach($scheme->sheets as $index => $sheets){
            $table = $sheets->tables[0];
            empty($input_sheets[$index]['rows'][count($input_sheets[$index]['rows'])-1]) && array_pop($input_sheets[$index]['rows']);
            $colHeaders = array_fetch($table->columns, 'name');
            $data = array_map(function($row) use($colHeaders) {
                $row_insert = array_only($row, $colHeaders);
                $row_insert['created_by'] = Auth::user()->id;
                $row_insert['created_at'] = date("Y-n-d H:i:s");
                $row_insert['updated_at'] = date("Y-n-d H:i:s");
                return $row_insert;
            }, $input_sheets[$index]['rows']);
            
            $data_page_max = floor(count($input_sheets[$index]['rows']) / 50)+1;
            
            for($i=0 ; $i<$data_page_max ; $i++){
                $data_page = array_slice($data, $i*50, 50);
                DB::table($table->database.'.dbo.'.$table->name)->insert($data_page);
            }
            
		}
        
        return Response::json([]);
    }

    public function uploadRows() {
        return Input::file('file');
        //return Input::file('file_upload');
    }
	
}
