<?php
namespace app\library\files\v0;
use DB, View, Schema, Response, ShareFile, RequestFile, Files, Auth, Input, Session, Redirect, Carbon\Carbon, app\library\files\v0\FileProvider, Row\Sheet, Row\Table, Row\Column;

class RowsFile extends CommFile {

    public static $database = 'rows';

    public $columns;

    public $rules = [  
        'stdidnumber' => ['type' => 'string', 'title' => '身分證',                  'size' => 10, 'regex' => '/^\w+$/', 'function' => 'stdidnumber'],
        'email'       => ['type' => 'string', 'title' => '信箱',                    'size' => 50, 'validator' => 'email'],        
        'gender'      => ['type' => 'tinyInteger', 'title' => '性別: 1.男 2.女',         'regex' => '/^[1-2]{1}$/'],
        'gender_id'   => ['type' => 'tinyInteger', 'title' => '性別: 1.男 2.女(身分證第2碼)', 'regex' => '/^[1-2]{1}$/'],
        'date_six'    => ['type' => 'string',   'title' => '日期(yymmdd)',   'size' => 6, 'regex' => '/^[0-9]{6}$/'],
        'bool'        => ['type' => 'boolean',  'title' => '是(1)與否(0)',   'validator' => 'boolean'],
        'int'         => ['type' => 'integer',  'title' => '整數',           'regex' => '/^\d+$/'],
        'float'       => ['type' => 'float',    'title' => '小數',           'regex' => '/^[0-9]+.[0-9]+$/'],
        'score'       => ['type' => 'string',   'title' => '成績(A++,A+,A,B++,B+,B,C,-9)', 'size' => 3, 'validator' => 'in:A++,A+,A,B++,B+,B,C,-9'],
        'score_six'   => ['type' => 'string',   'title' => '成績(0~6,-9)', 'size' => 2, 'validator' => 'in:0,1,2,3,4,5,6,-9'],
        'phone'       => ['type' => 'string', 'title' => '手機',                    'size' => 20, 'regex' => '/^\w+$/'],
        'tel'         => ['type' => 'string', 'title' => '電話',                    'size' => 20, 'regex' => '/^\w+$/'],
        'address'     => ['type' => 'string', 'title' => '地址',                    'size' => 50],
        'text'        => ['type' => 'longText', 'title' => '文字(50字以內)', 'size' => 50],
        'nvarchar'    => ['type' => 'string',   'title' => '文字(50字以上)', 'size' => 500],
        'other'       => ['type' => 'string',   'title' => '其他',           'size' => 50],
    ]; 

    public function checker($name) {
        $checkers = [
            'stdidnumber' => function($column_value, $column, &$column_errors) {
                !check_id_number($column_value) && array_push($column_errors, $column->title . '無效');
            },
            'shid' => function($column_value, $column, &$column_errors) {
                $name = '學校代碼';
                $errors = [];    
                check_empty($n, $name, $errors);
                !preg_match("/^[0-9A-Za-z]{6}$/u", $n) && array_push($errors, $name . '錯誤');
                !array_key_exists($n, $sch_id) && array_push($errors, '不是本校學生');
                
                return $errors;
            },
            'depcode' => function($column_value, $column, &$column_errors) {
                $name = '科系代碼';
                $errors = [];
                check_empty($n, $name, $errors);
                !preg_match("/^[a-zA-Z0-9]{3,6}$/u", $n) && array_push($errors, $name . '錯誤');
                !in_array($n, $m) && array_push($errors, '非貴校科系代碼');
                
                return $errors;
            },
        ];
        return $checkers[$name];
    }
    
    function __construct($doc_id) 
    {
        $shareFile = ShareFile::find($doc_id);

        parent::__construct($shareFile);

        $this->information = json_decode($this->file->information);
    }

    public function is_full()
    {
        return false;
    }

    public function get_views()
    {
        return ['open', 'import'];
    }
    
    public static function create($newFile)
    {        
        $shareFile = parent::create($newFile);

        $shareFile->isFile->information = '{"power":{"editable":true},"comment":""}';

        $shareFile->push();

        $sheet = $shareFile->isFile->sheets()->create(['title' => '']);

        $sheet->tables()->create(['database' => self::$database, 'name' => self::generate_table()]);

        return $shareFile;
    }

    public function open() 
    {
        $view = $this->shareFile->created_by == $this->user->id ? 'files.rows.table_editor' : 'files.rows.table_open';

        return $view;    
    }

    public function subs() 
    {
        return  View::make('files.rows.subs.' . Input::get('tool', ''))->render();  
    }
    
	public function import() 
    {        
        return 'files.rows.table_import';        
    }

    public static function generate_table()
    {
        return 'row_' . Carbon::now()->formatLocalized('%Y%m%d_%H%M%S') . '_' . strtolower(str_random(5));
    }
    
    public function get_status() 
    {        
        $sheets = $this->shareFile->isFile->sheets()->with(['tables', 'tables.columns'])->get();

        foreach($sheets as $sheet) {
            foreach($sheet->tables as $table) {
                if (Schema::hasTable($table->name)) {
                    $rows_count = DB::table($table->name)->whereNull('deleted_at')->where('created_by', $this->user->id)->count();
                } else {
                    $rows_count = 0;
                }
            }
        }

        return ['rows_count' => $rows_count, 'sheets' => $sheets->toArray(), 'comment' => $this->information->comment];
    }

    public function get_file()
    {       
        if ($this->shareFile->isFile->sheets->isEmpty()) {
            $this->to_new();
        }
        $sheets = $this->shareFile->isFile->sheets()->with(['tables', 'tables.columns'])->get()->toArray();
        return [
            'title'    => $this->file->title,
            'sheets'   => $sheets,
            'rules'    => $this->rules,            
            'comment'  => isset($this->information->comment) ? $this->information->comment : '',
            'editable' => isset($this->information->power->editable) ? $this->information->power->editable : true,
        ];
    }    

    public function update_sheet()
    {
        $sheet = $this->file->sheets()->with(['tables', 'tables.columns'])->find(Input::get('sheet')['id']);

        $sheet->update(['title' => Input::get('sheet')['title']]);

        return ['sheet' => $sheet]; 
    }

    public function remove_column()
    {
        Column::find(Input::get('id'))->delete();

        return $this->get_file();
    }

    public function update_column()
    {
        $input = array_only(Input::get('column'), array('name', 'title', 'rules', 'unique', 'encrypt', 'isnull'));

        if (!isset(Input::get('column')['id'])) {
            $column = $this->file->sheets         
            ->find(Input::get('sheet_id'))->tables
            ->find(Input::get('table_id'))->columns()->create($input);
        } else {   
            $column = $this->file->sheets
            ->find(Input::get('sheet_id'))->tables
            ->find(Input::get('table_id'))->columns
            ->find(Input::get('column')['id']);
            $column->update($input);
        }

        return ['column' => $column];
    }

    public function import_upload() 
    {        
        $rows_message = [];

        $upload_file = $this->upload(false);

        $table = $this->file->sheets[0]->tables[0]; 

        $table_columns = $table->columns->fetch('name')->toArray();      
        
        $rows = \Excel::load(storage_path() . '/file_upload/' . $upload_file->file, function($reader) {
            
        })->get($table_columns)->toArray();
        
        $head = head($rows);          

        //check excel column head
        $check_head = $table->columns->filter(function($column) use($head) {
            return !array_key_exists($column->name, $head);
        });

        if (!$check_head->isEmpty()) {
            return ['messages' => ['head' => $check_head]];
        }        

        $columns = $table->columns->map(function($column) use($table, $rows, $head)
        {           
                
            if( $column->unique ) 
            {                 
                $cells = array_pluck($rows, $column->name);                
                
                $column->repeats = array_count_values(array_map('strval', $cells));                     
                
                $column->uniques = array_filter($cells, function($cell) use($column)
                {
                    $column_value = remove_space($cell);
                    
                    $column_checked = $this->check_column($column, $column_value);
                    
                    return empty($column_checked);                    
                });    
                
                $column->exists = DB::table($table->database . '.dbo.' . $table->name)->whereIn('C' . $column->id, $column->uniques)->lists('created_by', 'C' . $column->id);
            }
            
            return (object)[
                'id'      => $column->id,
                'name'    => $column->name,  
                'title'   => $column->title,              
                'rules'   => $column->rules,                
                'unique'  => $column->unique,                
                'encrypt' => $column->encrypt,
                'uniques' => isset($column->uniques) ? $column->uniques : [],
                'repeats' => isset($column->repeats) ? $column->repeats : [],
                'exists'  => isset($column->exists) ? $column->exists : [],
            ];            
        });

        $work_schools = ['011C31' => '測試'];//User_use::find($this->user->id)->schools->lists('sname', 'id');
        $udepcode = DB::table('use_103.dbo.list_department_103')->wherein('shid', array_keys($work_schools))->distinct()->lists('depcode');
        $param['sch_id'] = $work_schools;
        $param['udepcode_list'] = $udepcode;
    
        $rows_insert = [];
        foreach ($rows as $row_index => $row)
        {
            $row_filted = array_filter($row);

            $rows_message[$row_index] = (object)['pass' => false, 'limit' => false, 'empty' => empty($row_filted), 'updated' => false, 'exists' => [], 'errors' => [], 'row' => []];            
            
            //skip if empty
            if ($rows_message[$row_index]->empty) continue;

            foreach ($columns as $column)
            {             
                $value = $rows_message[$row_index]->row['C' . $column->id] = isset($row[$column->name]) ? remove_space($row[$column->name]) : '';                            

                if ($column->unique && array_key_exists($value, $column->exists))
                {
                    $rows_message[$row_index]->limit = $rows_message[$row_index]->limit || $column->exists[$value] != $this->user->id;

                    array_push($rows_message[$row_index]->exists, 'C' . $column->id);
                }    

                $column_errors = $this->check_column($column, $value);

                if (!empty($column_errors)) 
                {
                    $rows_message[$row_index]->errors[$column->id] = $column_errors;
                }
            }

            $rows_message[$row_index]->pass = !$rows_message[$row_index]->limit && empty($rows_message[$row_index]->errors);
    
            //skip if not pass
            if (!$rows_message[$row_index]->pass) continue;            
            
            $rows_message[$row_index]->row['file_id'] = $this->file->id;
            $rows_message[$row_index]->row['updated_by'] = $this->user->id;
            $rows_message[$row_index]->row['updated_at'] = Carbon::now()->toDateTimeString();            
            
            if (!empty($rows_message[$row_index]->exists))
            {
                $query = DB::table($table->database . '.dbo.' . $table->name);
                foreach ($rows_message[$row_index]->exists as $exist_id)
                {
                    $query->where($exist_id, $rows_message[$row_index]->row[$exist_id]);
                }
                $rows_message[$row_index]->updated = $query->update($rows_message[$row_index]->row);
            }
            else
            {
                $rows_message[$row_index]->row['created_by'] = $this->user->id;
                $rows_message[$row_index]->row['created_at'] = Carbon::now()->toDateTimeString();
                array_push($rows_insert, $rows_message[$row_index]->row);
            }         
        }    
        //var_dump($rows_message);exit;
        foreach(array_chunk($rows_insert, 50) as $rows_part)
        {
            DB::table($table->database . '.dbo.' . $table->name)->insert($rows_part);
        }     
        
        return ['messages' => $rows_message];
    }    
    
    public function check_column($column, $column_value)
    {
        $column_errors = [];

        check_empty($column_value, $column->title, $column_errors);

        if( isset( $column->repeats[$column_value] ) && $column->repeats[$column_value] > 1 )
        {
            array_push($column_errors, $column->title . '資料重複');
        }   

        if( empty( $column_errors ) )
        {   
            $rules = $this->rules[$column->rules];
            if (isset($rules['regex']) && !preg_match($rules['regex'], $column_value)) {
                array_push($column_errors, $column->title . '格式錯誤');
            }
            if (isset($rules['validator'])) {
                $validator = \Validator::make([$column->id => $column_value], [$column->id => $rules['validator']]);
                $validator->fails() && array_push($column_errors, $column->title . '格式錯誤');
            }
            if (isset($rules['function'])) {
                call_user_func_array($this->checker($rules['function']), array($column_value, $column, &$column_errors));
            }           
        }

        return $column_errors; 
    }

    public function put_information($information, $title = null)
    {        
        isset($title) && $this->file->title = $title;

        $this->file->information = json_encode($information);
        
        $this->file->save();        
    }    

    public function has_table($table)
    {
        return DB::table($table->database . '.INFORMATION_SCHEMA.COLUMNS')->where('TABLE_NAME', $table->name)->exists();
    }
    
    public function create_schema_table($table)
    {  
        $this->has_table($table) && Schema::drop($table->database . '.dbo.' . $table->name);
        Schema::create($table->database . '.dbo.' . $table->name, function($query) use($table) 
        {                
            $query->increments('id');

            foreach($table->columns as $column)
            {
                $this->add_schema_column($query, 'C' . $column->id, $this->rules[$column->rules]);
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
 
    private function add_schema_column($query, $name, $rule, $indexs = [])
    {   
        if( isset($rule['size']) )  {
            $schema = $query->$rule['type']($name, $rule['size']);
        } else {
            $schema = $query->$rule['type']($name);
        }
        foreach($indexs as $index) {        
            $schema->$index();          
        }      
    }

    public function request_to()
    {
        if( $this->information->power->editable ) {
            foreach($this->file->sheets as $sheet) {                
                foreach($sheet->tables as $table) {                    
                    $this->create_schema_table($table);
                }                
            }
        }

        $this->information->power->editable = false;

        $this->put_information($this->information);            
        
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
        $schema = $this->get_information();
        
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

    public function get_columns($schema)
    {
        $table = $schema->sheets[0]->tables[0];

        return $table->columns;
    }

    public function export_sample()
    {
        \Excel::create('sample', function($excel) {

            $excel->sheet('sample', function($sheet) {

                $table = $this->file->sheets[0]->tables[0];                

                $sheet->freezeFirstRow();

                $sheet->fromArray($table->columns->fetch('name')->toArray());

            });

        })->download('xls');
    }

    public function export_describe()
    {
        \Excel::create('describe', function($excel) {

            $excel->sheet('describe', function($sheet) {

                $schema = $this->get_information();

                $table = $schema->sheets[0]->tables[0];

                $sheet->rows([
                    array_pluck($table->columns, 'name'),
                    array_pluck($table->columns, 'title'),
                    // array_pluck($table->columns, 'describe'),
                ]);

            });

        })->download('xlsx');
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

        $schema = $this->get_information();

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
        $sheet_compact = $this->get_information($shareFile_compact)->sheets[$sheet_index_compact];        
        $table_compact = $sheet_compact->tables[0];
        
        list($rows_query, $power) = $this->get_rows_query($index);          
        
        $shareFile = ShareFile::find($this->doc_id);        
        $sheet = $this->get_information($shareFile)->sheets[$index];  
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
        $sheet_compact = $this->get_information($shareFile_compact)->sheets[$sheet_index_compact];        
        $table_compact = $sheet_compact->tables[0];
        
        list($rows_query, $power) = $this->get_rows_query($index);    
        
        $shareFile = ShareFile::find($this->doc_id);        
        $sheet = $this->get_information($shareFile)->sheets[$index];  
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

    public function to_new()
    {
        if (isset($this->information->sheets)) {
            foreach($this->information->sheets as $sheet) {
                $aa = $this->shareFile->isFile->sheets()->save(new Sheet(['title' => $sheet->name, 'editable' => $sheet->editable]));
                foreach($sheet->tables as $table) {
                    //var_dump($table);exit;
                    $bb = $aa->tables()->save(new Table(['database' => $table->database, 'name' => $table->name]));
                    foreach($table->columns as $column) {
                        $bb->columns()->save(new Column([
                            'name'    => $column->name,
                            'title'   => $column->title,
                            'rules'   => $column->rules,
                            'unique'  => $column->unique,
                            'encrypt' => $column->encrypt,
                            'isnull'  => $column->isnull,
                        ]));
                    }
                }
            }
            $this->information->sheets = null;
            $this->put_information($this->information);
        }
    }
    
    //deprecated (update comment)
    public function save_file()
    {
        if( $this->shareFile->created_by == $this->user->id )
        {
            $sheets = $this->file->sheets;
            foreach (Input::get('file')['sheets'] as $sheet) {
                $sheets->find($sheet['id'])->update(['title' => $sheet['title']]);
                foreach ($sheet['tables'] as $table) {
                    $tables = $sheets->find($sheet['id'])->tables;
                    $tables->find($table['id']);
                    foreach ($table['columns'] as $column) {
                        $columns = $tables->find($table['id'])->columns;
                        if (isset($column['id'])) {
                            $columns->find($column['id'])->update([
                                'name'  => $column['name'],
                                'title' => $column['title'],
                            ]);
                        }
                    }
                }
            }

            $this->information->comment = isset(Input::get('file')['comment']) ? Input::get('file')['comment'] : '';

            $this->put_information($this->information);
        }

        return $this->get_file();
    }

    //deprecated
    public function get_information()
    {        
        return json_decode($this->file->information);
    }
       
    //deprecated
    public function update_sheets($sheets_old, $sheets)
    {        
        return array_map(function($sheet) use($sheets_old) {
            $sheet_org = array_first($sheets_old, function($key, $sheet_org) use($sheet) {
                return isset($sheet['id']) && isset($sheet_org->id) ? $sheet['id'] == $sheet_org->id : false;
            });

            return [
                'id'        => isset($sheet_org) ? $sheet_org->id : strtolower(str_random(10)),
                'name'      => isset($sheet['name']) ? $sheet['name'] : '',
                'editable'  => isset($sheet['editable']) ? $sheet['editable'] : false,
                'tables'    => array_map(function($table) use($sheet_org) {                    
                    $table_org = isset($sheet_org) && isset($sheet_org->tables[0]) ? $sheet_org->tables[0] : null;                    
                    return (object)[
                        'database'   => isset($table_org) && isset($table_org->database) ? $table_org->database : $this->database,
                        'name'       => isset($table_org) && isset($table_org->name) 
                                        ? $table_org->name : 'row_' . Carbon::now()->formatLocalized('%Y%m%d_%H%M%S_') . $this->user->id . '_' . strtolower(str_random(5)),
                        'primaryKey' => 'id',
                        'encrypt'    => false,
                        'columns'    => array_map(function($columns) {
                            return (object)[
                                'name'     => $columns["name"],
                                'title'    => $columns["title"],
                                // 'describe' => $columns["describe"],
                                'rules'    => $columns["rules"],
                                'unique'   => isset($columns["unique"]) ? $columns["unique"] : false,
                                'encrypt'  => isset($columns["encrypt"]) ? $columns["encrypt"] : false,
                                'isnull'   => isset($columns["isnull"]) ? $columns["isnull"] : false,
                                //'link'   => $columns["link"],
                            ];
                        }, array_filter($table['columns'])),
                    ];
                }, $sheet['tables']),
            ];
        }, $sheets);
    }

}
