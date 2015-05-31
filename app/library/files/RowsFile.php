<?php
namespace app\library\files\v0;
use DB, View, Schema, Response, ShareFile, RequestFile, Files, Auth, Input, Session, Redirect, Carbon\Carbon, PHPExcel_IOFactory, Illuminate\Filesystem\Filesystem, app\library\files\v0\FileProvider;

class RowsFile extends CommFile {
	
	/**
	 * @var array 2 dimension
	 */
	public $data;	
	
	/**
	 * @var array 1 dimension
	 */
	public $columns;
    
    public $database = 'rows';
	
	public static $intent = array(
		'import',
        'open',		
		'get_sheets',
        'get_rows',
        'get_import_rows',
        'createTable',
        'save_struct',
        'requestTo',
        'export',
        'upload',
        'save_import_rows',
        'delete',
        'get_compact_files',
	);

    public $types = [
        'address'     => ['string', 50],
        'phone'       => ['string', 20],
        'tel'         => ['string', 20],
        'email'       => ['string', 50],
        'stdidnumber' => ['string', 10],
        'gender'      => ['tinyInteger'],
        'date_six'    => ['string', 6],
        'bool'        => ['boolean'],
        'int'         => ['integer'],
        'float'       => ['float'],
        'text'        => ['longText'],
        'nvarchar'    => ['string', 50],
        'other'       => ['string', 500],
    ];
    
    function __construct($doc_id) 
    {        
        if( $doc_id == '' )
            return false;

        $this->user = Auth::user();
        
        $this->shareFile = ShareFile::find($doc_id);  
        
        $this->file = $this->shareFile->isFile;        
    }
	
	public static function get_intent() 
    {
		return array_unique(array_merge(parent::$intent, self::$intent));
	}

    public function open() 
    {        
        $schema = $this->get_schema();
        
        if( $this->shareFile->created_by == $this->user->id && $schema->power->editable ){
            //return 'demo.page.table_info';
            return 'demo.page.table_editor';
        }else{
            return 'demo.page.table_open';
        }        
    }
    
	public function import() 
    {        
        return 'demo.page.table_import';        
    }
    
    public function get_rows_count() 
    {        
        $schema = $this->get_schema();
        
        $database = $schema->sheets[0]->tables[0]->database;

        $table = $schema->sheets[0]->tables[0];
        
        $work_schools = ['011C31' => '測試'];
        
        $rows_count = DB::table($database . '.dbo.' . $table->name)->whereNull('deleted_at')->where('created_by', $this->user->id)->count();
        
        return ['rows_count' => $rows_count]; 
    }
    
    public function import_upload() 
    {        

        $upload_file_id = $this->upload(false);
        
        $upload_file = Files::find($upload_file_id)->file;

        $schema = $this->get_schema();

        $database = $schema->sheets[0]->tables[0]->database;

        $table = $schema->sheets[0]->tables[0];     
        
        require_once base_path() . '/app/views/demo/page/check_function.php';
        
        $rows = \Excel::selectSheetsByIndex(0)->load(storage_path() . '/file_upload/' . $upload_file, function($reader) {


        })->toArray();
        
        //$reader = PHPExcel_IOFactory::createReaderForFile( storage_path() . '/file_upload/' . $upload_file );
        //$reader->setReadDataOnly(true);
        //$objPHPExcel = PHPExcel_IOFactory::load( storage_path() . '/file_upload/' . $upload_file );
        
        //var_dump($rows);exit;

        //$workSheet = $objPHPExcel->getActiveSheet();
        
        //$column_max_name = num2alpha(count($schema->sheets[0]->tables[0]->columns));
        
        //$rows = $workSheet->rangeToArray('A2:' . $column_max_name . $workSheet->getHighestRow(), '', false, true, false);
        //$rows = $workSheet->toArray(null, true, true, true);
        //unset($reader);

        $columns = array_map(function($column, $key) use($database, $table, $rows)
        {            
            //$index = num2alpha($key);
                
            if( $column->unique ) 
            {
                $cells = array_pluck($rows, $key);
                
                $column->repeats = array_count_values(array_map('strval', $cells));                
                
                $column->uniques = array_filter($cells, function($cell) use($column)
                {
                    $column_value = remove_space($cell);
                    
                    $column_checked = $this->check_column($column, $column_value);
                    
                    return empty($column_checked);                    
                });    
                
                $column->exists = DB::table($database . '.dbo.' . $table->name)->whereIn($column->name, $column->uniques)->lists('created_by', $column->name);
            }
            
            return (object)[
                //'index'   => $index,
                'name'    => $column->name,  
                'title'   => $column->title,              
                'rules'   => $column->rules,                
                'unique'  => $column->unique,                
                'encrypt' => $column->encrypt,
                'uniques' => isset($column->uniques) ? $column->uniques : [],
                'repeats' => isset($column->repeats) ? $column->repeats : [],
                'exists'  => isset($column->exists) ? $column->exists : [],
            ];            
        }, $table->columns, array_pluck($table->columns, 'name'));//var_dump($columns);exit;
        
        $table_schema = array_fetch($columns, 'name');

        $work_schools = ['011C31' => '測試'];//User_use::find($this->user->id)->schools->lists('sname', 'id');

        $udepcode = DB::table('use_103.dbo.list_department_103')->wherein('shid', array_keys($work_schools))->distinct()->lists('depcode');

        $param['sch_id'] = $work_schools;
        $param['udepcode_list'] = $udepcode;   

        $rows_message = [];
    
        function combine_table($table, $input) {
            extract($input);
            return compact($table);
        }        
        
        $rows_insert = [];
    
        foreach($rows as $row_index => $row) {
            
            $row_message = (object)['index'=> $row_index, 'pass' => true, 'empty' => false, 'errors' => [], 'exist' => false, 'status' => false];

            $row_filtered = array_filter($row);

            if( empty($row_filtered) ) {
                $row_message->empty = true;
                array_push($rows_message, $row_message);
                continue;
            }
            
            $exist = false;
            $row_valid = [];

            foreach($columns as $column_index => $column)
            {
                $row_valid[$column->name] = isset( $row[$column->name] ) ? remove_space($row[$column->name]) : '';//var_dump($row_valid[$column->name]);exit;

                $cloumn_errors = $this->check_column($column, $row_valid[$column->name]);

                if( $column->unique )
                {
                    $primary = $column->name;
                        
                    $exist = isset($column->exists) && array_key_exists($row_valid[$column->name], $column->exists);
                    
                    if( $exist && $column->exists[$row_valid[$column->name]] != $this->user->id )
                    {
                        $cloumn_errors = ['此學生資料已由他人上傳，欲更新資料請與本中心聯繫。'];
                    }
                }
                
                !empty($cloumn_errors) && $row_message->pass = false;
                
                array_push($row_message->errors, (object)['value' => $row_valid[$column->name], 'errors' => $cloumn_errors]);
            }     
            
            $row_message->exist = $exist;
            
            array_push($rows_message, $row_message);
    
            if( !$row_message->pass ) continue;
            
            $row_reduced = combine_table($table_schema, $row_valid);
            
            $row_reduced['file_id'] = $this->file->id;
            $row_reduced['updated_by'] = $this->user->id;
            $row_reduced['updated_at'] = Carbon::now()->toDateTimeString(); 
            
            if( $exist )
            {
                $row_message->status = DB::table($database . '.dbo.' . $table->name)
                    ->where($primary, $row_reduced[$primary])
                    ->update($row_reduced);
            }
            else
            {
                $row_reduced['created_by'] = $this->user->id;
                $row_reduced['created_at'] = Carbon::now()->toDateTimeString();
                array_push($rows_insert, $row_reduced);
            }         
        }    
        
        foreach(array_chunk($rows_insert, 50) as $rows)
        {
            DB::table($database . '.dbo.' . $table->name)->insert($rows);
        }        
        
        $message = compact('rows_message', 'columns');
        
        //exit;
        
        Session::flash('message', $message);
        
        return Redirect::back();
    }    
    
    public function check_column($column, $column_value)
    {
        $cloumn_errors = [];

        check_empty($column_value, $column->title, $cloumn_errors);

        if( isset( $column->repeats[$column_value] ) && $column->repeats[$column_value] > 1 )
        {
            array_push($cloumn_errors, $column->title . '資料重複');
        }   

        if( empty( $cloumn_errors ) )
        {                    
            function_exists('check_' . $column->rules) && call_user_func_array('check_' . $column->rules, array($column_value, $column->title, &$cloumn_errors));                
        }

        return $cloumn_errors; 
    }
	
    public function create_file()
    {        
        $commFile = new CommFile;
        
        $doc_id = $commFile->createFile('');
        
        $this->shareFile = ShareFile::find($doc_id);
        
        $schema = (object)['power'=> (object)['editable' => true], 'sheets' =>[]];

        $this->put_schema($schema, Input::only('title')['title']);        
        
        $fileProvider = FileProvider::make();
        
        $intent_key = $fileProvider->doc_intent_key('open', $this->shareFile->id, 'app\\library\\files\\v0\\RowsFile');
        
        return Response::json(['shareFile' => [
            'id'         => $this->shareFile->id,
            'title'      => $this->file->title,
            'type'       => $this->file->type,
            'created_by' => $this->shareFile->created_by,
            'created_at' => $this->shareFile->created_at->toIso8601String(),
            'link'       => ['open' => 'file/' . $intent_key . '/open'],
            'intent_key' => $intent_key,
            'tools'      => [],
            'shared'     => []
        ]]);
    }   
  
    public function get_file()
    {
        return Response::json((object)['file' => (object)['title' => $this->file->title, 'schema' => $this->get_schema()]]);
    }    
    
    public function save_file()
    {        
        if( $this->shareFile->created_by == $this->user->id )
        {            
            $file = Input::get('file');

            $schema = $this->get_schema();

            $schema->sheets = $this->get_sheets_from_view();
            
            $this->put_schema($schema, $file['title']);            
        }
        
        return Response::json($schema);        
    }
        
    public function get_schema()
    {        
        return json_decode($this->file->information);
    }
    
    public function put_schema($schema, $title = null)
    {        
        isset($title) && $this->file->title = $title;

        $this->file->information = json_encode($schema);
        
        $this->file->save();        
    }    
    
    public function create_tables($tables)
    {     
        foreach($tables as $table)
        {
            Schema::create($table->database . '.dbo.' . $table->name, function($query) use($table) 
            {                
                $query->increments('id');

                foreach($table->columns as $column)
                {
                    $this->add_schema_column($query, $column->name, $this->types[$column->rules]);
                }

                $query->integer('file_id');
                $query->dateTime('updated_at');
                $query->dateTime('created_at');   
                $query->dateTime('deleted_at')->nullable(); 
                $query->integer('updated_by');
                $query->integer('created_by');
                $query->integer('deleted_by')->nullable();
            });
        }
    }

    public function drop_tables($schema)
    {
        foreach($schema->sheets as $sheet)
        {
            foreach($sheet->tables as $table)
            {       
                Schema::drop($table->database . '.dbo.' . $table->name);
            }            
        }
    }
    
    public function get_sheets_from_view()
    {
        return array_map(function($sheet) {
            return [
                'name'      => $sheet['name'],
                'editable'  => isset($sheet['editable']) ? $sheet['editable'] : false,
                'tables'    => array_map(function($table) {
                    return (object)[
                        'database'   => $this->database,
                        'name'       => 'row_' . Carbon::now()->formatLocalized('%Y%m%d_%H%M%S_') . $this->user->id . '_' . strtolower(str_random(5)),
                        'primaryKey' => 'id',
                        'encrypt'    => false,
                        'columns'    => array_map(function($columns) {
                            return (object)[
                                'name'   => $columns["name"],
                                'title'  => $columns["title"],
                                'rules'  => $columns["rules"],                                
                                'unique'  => isset($columns["unique"]) ? $columns["unique"] : false,
                                'encrypt' => isset($columns["encrypt"]) ? $columns["encrypt"] : false,
                                'isnull'  => isset($columns["isnull"]) ? $columns["isnull"] : false,
                                //'link'   => $columns["link"],                 
                            ];
                        }, array_filter($table['columns'])),                    
                    ];
                }, $sheet['tables']),
            ];
        }, Input::get('file')['schema']['sheets']);     
    } 
    
    private function add_schema_column($table, $name, $type, $indexs = [])
    {   
        if( isset($type[1]) )  {
            $schema = $table->$type[0]($name, $type[1]);
        }
        else
        {
            $schema = $table->$type[0]($name);
        }
        foreach($indexs as $index) {        
            $schema->$index();          
        }      
    }
    
    public function request_to()
    {
        $schema = $this->get_schema();
        if( $schema->power->editable ) {
            foreach($schema->sheets as $sheet)
            {
                $this->create_tables($sheet->tables);
            }
        }

        $schema->power->editable = false;

        $this->put_schema($schema);            
        
        $input = Input::only('groups', 'description');

        $myGroups = $this->user->groups;
        
        if( $this->shareFile->created_by == $this->user->id ) {            
            foreach($input['groups'] as $group) {                
                if( $myGroups->contains($group['id']) ) {
                    RequestFile::updateOrCreate(['target' => 'group', 'target_id' => $group['id'], 'doc_id' => $this->shareFile->id, 'created_by' => $this->user->id], ['description' => $input['description']]);
                }                
            }            
        }

        return Response::json(Input::all());
    }    
    
    private function get_rows_query($index_sheet) 
    {        
        $schema = $this->get_schema();
        
        $sheets = $schema->sheets;        
        
        $tables = $sheets[$index_sheet]->tables; 
        
        $power = array();
        
        foreach($tables as $index => $table){
            
            $database = $table->database;

            if( $index==0 ){
                $rows_query = DB::table($database.'.dbo.'.$table->name.' AS t0');
            }else{
                $rows_query->leftJoin($database.'.dbo.'.$table->name.' AS t'.$index, 't'.$index.'.'.$table->primaryKey, '=', 't0.'.$table->primaryKey);
            }    

            if( $this->shareFile->created_by == $this->user->id ) {              
                //$power = array_map(function($column){return $column->name;}, $table->columns);
                $power = array_merge($power, array('t0.id'), array_map(function($column)use($index){return 't'.$index.'.'.$column->name;}, $table->columns));
                //$power = array_fetch($table->columns, 'name');
            }else{
                $power = array_merge($power, array('t0.id'), array_map(function($column)use($index){return 't'.$index.'.'.$column->name;}, $table->columns));
            }

        }
		
        return [$rows_query, $power];
    }
    
    public function get_rows() 
    {        
        //權限未設定
        
        $index = Input::get('index');

        list($rows_query, $power) = $this->get_rows_query($index);
        
        $rows = $rows_query->select($power)->paginate(Input::get('limit'));
        //$rows =  DB::connection('sqlsrv')->table($database.'.dbo.'.$table)->select($power)->paginate(50);//->forPage(2000, 20)->get();

        return Response::json($rows);
    }	
    
    public function get_import_rows() 
    {        
        $index = Input::only('index')['index'];
        
        list($rows_query, $power) = $this->get_rows_query($index);
        
        $rows = $rows_query->select($power)->paginate(Input::only('limit')['limit']);//->where('created_by', $this->user->id)

        return Response::json($rows);
    }

	public function export() 
    {        
        //權限未設定

        $index = Input::only('index')['index'];

        list($rows_query, $power) = $this->get_rows_query($index);        
        
//        if( $shareFile->created_by==$this->user->id ) {
//            $power = array_fetch($schema->tables[0]->columns, 'name');
//        }else{
//            $power = json_decode($shareFile->power);
//        }        
        
        $rows = $rows_query->select($power)->get();
        
        $output = '';
        $output .= implode(",", array_keys((array)$rows[0]));
        $output .=  "\n"; 
        foreach($rows as $row){       
            $row_new = [];
            foreach($row as $column){ 
                array_push($row_new, preg_replace(array("/\"/", "/,/", "/'/", "/\s/"), "" , $column));
            }
            $output .= "\"".iconv("UTF-8", "big5//IGNORE", implode("\",=\"", $row_new))."\"";
            $output .= "\n";
        }
        $headers = array(
            //'Content-Encoding' => 'UTF-8',
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ExportFileName.csv"',
        );

        return Response::make($output, 200, $headers);        
    }

	 public function save_import_rows() 
     {
		$input_sheets = Input::only('sheets')['sheets'];

        $schema = $this->get_schema();

		foreach($schema->sheets as $index => $sheets){
			
            $table = $sheets->tables[0];
            empty($input_sheets[$index]['rows'][count($input_sheets[$index]['rows'])-1]) && array_pop($input_sheets[$index]['rows']);
			
			$row_insert = array();
            $row_update = array();
			
			foreach($input_sheets[$index]['rows'] as $row){
                if( !empty($row) ) 
				if( isset($row['id']) ){
                    $row_update[$row['id']] = (array)$row;					
				}else{                    
                    array_push($row_insert , (array)$row); 		
                }
			}

            $colHeaders = array_fetch($table->columns, 'name');
			
			$data = array_map(function($row_insert) use($colHeaders) {		
                $row_insert = array_only($row_insert, $colHeaders);
                $row_insert['created_by'] = $this->user->id;
                $row_insert['created_at'] = date("Y-n-d H:i:s");
                $row_insert['updated_at'] = date("Y-n-d H:i:s");
                return $row_insert;
            }, $row_insert);
			
			$data_page_max = count($row_insert)==0 ? 0 : floor(count($row_insert) / 50)+1;
            
            for($i=0 ; $i<$data_page_max ; $i++){
                $data_page = array_slice($data, $i*50, 50);
                DB::table($table->database.'.dbo.'.$table->name)->insert($data_page);
			}
			
            foreach($row_update as $id => $row){
                
                $data = array_only($row, $colHeaders);
                $data['updated_at'] = date("Y-n-d H:i:s");
                
				DB::table($table->database.'.dbo.'.$table->name)->where('id', $id)->update($data);
                
			}
            
		}
		
        return Response::json([]);
    }
    
    public function get_compact_files() 
    {
        $fileProvider = FileProvider::make();
        
        $inGroups = $this->user->inGroups->lists('id');
        
        $myRowFiles = ShareFile::with('isFile')->whereHas('isFile', function($query){
            $query->where('type', '=', 5);
        })->where(function($query) {
            $query->where('target', 'user')->where('target_id', $this->user->id);
        })->orWhere(function($query) use($inGroups) {
            count($inGroups)>0 && $query->where('target', 'group')->whereIn('target_id', $inGroups)->where('created_by', '!=', $this->user->id);
        })->orderBy('created_at', 'desc')->get();
        
        $files = $myRowFiles->map(function($myRowFile) use($fileProvider){    
            $intent_key = $fileProvider->doc_intent_key('open', $myRowFile->id, 'app\\library\\files\\v0\\RowsFile');
            return [
                'title' => $myRowFile->is_file->title,
                'intent_key' => $intent_key
            ];
        });
        return $files;
    }
    
    public function get_compact_sheet() 
    {        
        $index = Input::only('index')['index'];
        $intent_key_compact = Input::only('intent_key_compact')['intent_key_compact'];
        $sheet_index_compact = Input::only('sheet_index_compact')['sheet_index_compact'];
        
        $doc_id_compact = Session::get('file')[$intent_key_compact]['doc_id'];
        $shareFile_compact = ShareFile::find($doc_id_compact);
        $sheet_compact = $this->get_schema($shareFile_compact)->sheets[$sheet_index_compact];        
        $table_compact = $sheet_compact->tables[0];
        
        list($rows_query, $power) = $this->get_rows_query($index);          
        
        $shareFile = ShareFile::find($this->doc_id);        
        $sheet = $this->get_schema($shareFile)->sheets[$index];  
        $table = $sheet->tables[0];
        
        $power = array_merge($power, array_map(function($column){return 'compact.'.$column->name;}, $table_compact->columns));
        
        $sheet_new = [
            'compact' => true,
            'sheetName' => $shareFile->is_file->title.' - '.$shareFile_compact->is_file->title,
            'tables' => [[
                'columns' => array_merge($table->columns, array_map(function($column){$column->compact = true;return $column;}, $table_compact->columns)),
                'tablename' => ''
            ]]            
        ];
        
        return Response::json(['sheet_compact'=>$sheet_new]);
    }
    
    public function get_compact_rows() 
    {        
        $sheet_info = Input::only('sheet_info')['sheet_info'];

        $index = $sheet_info['source_index'];
        $intent_key_compact = $sheet_info['compact_intent_key'];
        $sheet_index_compact = $sheet_info['compact_sheet_index'];
        
        $doc_id_compact = Session::get('file')[$intent_key_compact]['doc_id'];
        $shareFile_compact = ShareFile::find($doc_id_compact);
        $sheet_compact = $this->get_schema($shareFile_compact)->sheets[$sheet_index_compact];        
        $table_compact = $sheet_compact->tables[0];
        
        list($rows_query, $power) = $this->get_rows_query($index);    
        
        $shareFile = ShareFile::find($this->doc_id);        
        $sheet = $this->get_schema($shareFile)->sheets[$index];  
        $table = $sheet->tables[0];
        
        $columns_compacted = array_diff(array_fetch($table_compact->columns, 'name'), array_fetch($table->columns, 'name'));
        
        $power = array_merge($power, array_map(function($column){return 'compact.'.$column;}, $columns_compacted));
        $rows = $rows_query->leftJoin($table_compact->database.'.dbo.'.$table_compact->name.' AS compact', 'compact.newcid', '=', 't0.newcid')
            ->where('t0.created_by', $this->user->id)
            ->select($power)->paginate(Input::only('limit')['limit']);
        
        return Response::json($rows);
    }
    
    public function delete() 
    {
        $this->file->delete();

        $this->shareFile->delete();

        return $this->shareFile->id;
    }
	
}
