<?php
namespace app\library\files\v0;

use User;
use Files;
use DB, View, Schema, Response, Input, Session;
use ShareFile, RequestFile;
use Row\Sheet;
use Row\Table;
use Row\Column;
use Carbon\Carbon;

class RowsFile extends CommFile {

    protected $database = 'rows';

    protected $temp;

    public $rules = [  
        'gender'      => ['sort' => 1,  'type' => 'tinyInteger',             'title' => '性別: 1.男 2.女',               'validator' => 'in:1,2'],
        'gender_id'   => ['sort' => 2,  'type' => 'tinyInteger',             'title' => '性別: 1.男 2.女(身分證第2碼)',  'validator' => 'in:1,2'],
        'bool'        => ['sort' => 3,  'type' => 'boolean',                 'title' => '是(1)與否(0)',                  'validator' => 'boolean'],
        'stdidnumber' => ['sort' => 4,  'type' => 'string',   'size' => 10,  'title' => '身分證',                        'function' => 'stdidnumber'],
        'email'       => ['sort' => 5,  'type' => 'string',   'size' => 80,  'title' => '信箱',                          'validator' => 'email'],       
        'date_six'    => ['sort' => 6,  'type' => 'string',   'size' => 6,   'title' => '日期(yymmdd)',                  'validator' => ['regex:/^([0-9][0-9])(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01])$/']],
        'order'       => ['sort' => 7,  'type' => 'string',   'size' => 3,   'title' => '順序(1-99,-7)',                 'validator' => ['regex:/^([1-9]|[1-9][0-9]|[1-9][0-9][0-9]|-7)$/']],
        'score'       => ['sort' => 8,  'type' => 'string',   'size' => 3,   'title' => '成績(A++,A+,A,B++,B+,B,C,-7)',  'validator' => 'in:A++,A+,A,B++,B+,B,C,-7'],
        'score_six'   => ['sort' => 9,  'type' => 'string',   'size' => 2,   'title' => '成績(0~6,-7)',                  'validator' => 'in:0,1,2,3,4,5,6,-7'],
        'phone'       => ['sort' => 10, 'type' => 'string',   'size' => 20,  'title' => '手機',                          'regex' => '/^\w+$/'],
        'tel'         => ['sort' => 11, 'type' => 'string',   'size' => 20,  'title' => '電話',                          'regex' => '/^\w+$/'],
        'address'     => ['sort' => 12, 'type' => 'string',   'size' => 80,  'title' => '地址'],
        'schid_104'   => ['sort' => 13, 'type' => 'string',   'size' => 6,   'title' => '高中職學校代碼(104)', 'function' => 'schid_104'],
        'depcode_104' => ['sort' => 14, 'type' => 'string',   'size' => 6,   'title' => '高中職科別代碼(104)', 'function' => 'depcode_104'],
        'text'        => ['sort' => 15, 'type' => 'string',   'size' => 50,  'title' => '文字(50字以內)'],
        'nvarchar'    => ['sort' => 16, 'type' => 'string',   'size' => 500, 'title' => '文字(500字以內)'],
        'int'         => ['sort' => 17, 'type' => 'integer',                 'title' => '整數',                         'validator' => 'integer'],
        'float'       => ['sort' => 18, 'type' => 'string',   'size' => 80,  'title' => '小數',                         'validator' => ['regex:/^([0-9]|[1-9][0-9]{1,40})(\\.[0-9]{1,39})?$/']],
        'year_four'   => ['sort' => 19, 'type' => 'string',   'size' => 4,   'title' => '西元年(yyyy)',                 'validator' => ['regex:/^(19[0-9]{2})$/']],
        //師培
        'tted_sch'         => ['sort' => 10, 'type' => 'string',   'size' => 4,   'title' => 'TTED大專院校學校代碼',      'function' => 'tted_sch'],
        'tted_depcode_103' => ['sort' => 11, 'type' => 'string',   'size' => 6,   'title' => 'TTED大專院校系所代碼103年', 'function' => 'tted_depcode_103'],
        'tted_depcode_104' => ['sort' => 11, 'type' => 'string',   'size' => 6,   'title' => 'TTED大專院校系所代碼104年', 'function' => 'tted_depcode_104'],
        'stdschoolstage'   => ['sort' => 12, 'type' => 'tinyInteger',             'title' => 'TTED教育階段',              'validator' => 'in:1,2,3'],
        'schoolsys'        => ['sort' => 13, 'type' => 'tinyInteger',             'title' => 'TTED學制別',                'validator' => 'in:1,2'],
        'program'          => ['sort' => 14, 'type' => 'tinyInteger',             'title' => 'TTED修課資格',              'validator' => 'in:0,1,2,3'],
        'govexp'           => ['sort' => 15, 'type' => 'tinyInteger',             'title' => 'TTED公費生',                'validator' => 'in:0,1,2,3,4'],
        'other'            => ['sort' => 16, 'type' => 'tinyInteger',             'title' => 'TTED外加名額',              'validator' => 'in:0,1,2,3,4,5,6,7,8,9,10'],        
        'stdyear'          => ['sort' => 18, 'type' => 'string',   'size' => 1,   'title' => 'TTED年級',                  'validator' => 'in:1,2,3,4,5,6,7'],
    ]; 

    function __construct(Files $file, User $user) 
    {
        parent::__construct($file, $user);

        $this->temp = (object)[]; 
    }

    public function is_full()
    {
        return false;
    }

    public function get_views()
    {
        return ['open', 'import', 'rows'];
    }
    
    public function create()
    {
        parent::create();

        $sheet = $this->add_sheet();

        $this->add_table($sheet);
    }

    public function open() 
    {
        $view = $this->isCreater() ? 'files.rows.table_editor' : 'files.rows.table_open';

        return $view;    
    }

    public function subs()
    {
        return View::make('files.rows.subs.' . Input::get('tool', ''))->render();
    }
    
    public function import()
    {
        return 'files.rows.table_import';
    }

    public function rows()
    {
        return 'files.rows.rows_editor';
    }

    public function generate_table()
    {
        return 'row_' . Carbon::now()->formatLocalized('%Y%m%d_%H%M%S') . '_' . strtolower(str_random(5));
    }

    private function add_sheet()
    {
        return $this->file->sheets()->create(['title' => '']);
    }

    private function add_table($sheet)
    {
        $sheet->tables()->create(['database' => $this->database, 'name' => $this->generate_table(), 'construct_at' => Carbon::now()->toDateTimeString()]);
    }

    private function init_sheets()
    {
        if (!$this->file->sheets()->getQuery()->exists()) {
            $this->add_sheet();
        }
        $this->file->sheets->each(function($sheet) {
            if (!$sheet->tables()->getQuery()->exists()) {
                $this->add_table($sheet);
            }
        });
    }

    public function get_file()
    { 
        $this->init_sheets();

        $sheets = $this->file->sheets()->with(['tables', 'tables.columns'])->get()->each(function($sheet) {
            $sheet->tables->each(function($table) use($sheet) {
                !$sheet->editable && $this->table_construct($table);
                if ($this->has_table($table)) {
                    $query = DB::table($table->database. '.dbo.' . $table->name)->whereNull('deleted_at');
                    if (!$this->isCreater() || !Input::get('editor', false)) {
                        $query->where('created_by', $this->user->id);
                    }
                    $table->count = $query->count();
                }
            });
        });

        $sheets->first()->selected = true;

        return [
            'title'    => $this->file->title,
            'sheets'   => $sheets->toArray(),
            'rules'    => $this->rules,            
            'comment'  => $this->get_information()->comment,
        ];
    }    

    public function update_sheet()
    {
        $sheet = $this->file->sheets()->with(['tables', 'tables.columns'])->find(Input::get('sheet')['id']);

        $sheet->update(['title' => Input::get('sheet')['title'], 'editable' => Input::get('sheet')['editable']]);

        return ['sheet' => $sheet->toArray()]; 
    }

    public function remove_column()
    {
        $table = $this->file->sheets->find(Input::get('sheet_id'))->tables->find(Input::get('table_id'));

        $table->columns->find(Input::get('column')['id'])->delete();

        return ['table' => $table->load('columns')->toArray()];
    }

    public function update_column()
    {
        $input = array_only(Input::get('column'), array('name', 'title', 'rules', 'unique', 'encrypt', 'isnull'));

        $table = $this->file->sheets->find(Input::get('sheet_id'))->tables->find(Input::get('table_id'));

        if (isset(Input::get('column')['id'])) {
            $column = $table->columns->find(Input::get('column')['id']);
            $column->update($input);            
        } else {   
            $column = $table->columns()->create($input);
        }        

        return ['column' => $column];
    }

    public function update_comment()
    {
        $information = $this->get_information();

        $information->comment = urldecode(base64_decode(Input::get('comment', '')));

        $this->put_information($information);

        return ['comment' => $information->comment];
    }

    private function put_information($information)
    {        
        $this->file->information = json_encode($information);
        
        $this->file->save();        
    }    

    private function get_information()
    {
        return isset($this->file->information) ? json_decode($this->file->information) : (object)['comment' => ''];
    }

    public function import_upload() 
    {
        if (!Input::hasFile('file_upload'))
            throw new ValidateException(new MessageBag(array('no_file_upload' => '檔案錯誤')));

        $file = new Files(['type' => 3, 'title' => Input::file('file_upload')->getClientOriginalName()]);

        $file_upload = new \app\library\files\v0\CommFile($file, $this->user);

        $file_upload->upload(Input::file('file_upload'));

        $table = $this->file->sheets[0]->tables[0]; 

        $table_columns = $table->columns->fetch('name')->toArray();      
        
        $rows = \Excel::load(storage_path() . '/file_upload/' . $file_upload->file->file, function($reader) {
            
        })->get($table_columns)->toArray();
        
        $head = head($rows);

        //check excel column head
        $checked_head = $table->columns->filter(function($column) use($head) {
            return !array_key_exists($column->name, $head ? $head : []);
        });

        if (!$checked_head->isEmpty()) {
            return ['messages' => ['head' => $checked_head]];
        }        

        $columns = $table->columns->map(function($column) use($table, $rows, $head)
        {           
                
            if( $column->unique ) 
            {                 
                $cells = array_pluck($rows, $column->name);                
                
                $repeats = array_count_values(array_map('strval', $cells));                     
                
                $uniques = array_filter($cells, function($cell) use($column)
                {
                    $column_value = remove_space($cell);
                    
                    $column_checked = $this->check_column($column, $column_value);
                    
                    return empty($column_checked);                    
                });    
                
                $exists = DB::table($table->database . '.dbo.' . $table->name)->whereIn('C' . $column->id, $uniques)->lists('created_by', 'C' . $column->id);
            }
            
            return (object)[
                'id'      => $column->id,
                'name'    => $column->name,  
                'title'   => $column->title,              
                'rules'   => $column->rules,                
                'unique'  => $column->unique,                
                'encrypt' => $column->encrypt,
                'isnull'  => $column->isnull,
                'uniques' => isset($uniques) ? $uniques : [],
                'repeats' => isset($repeats) ? $repeats : [],
                'exists'  => isset($exists) ? $exists : [],
            ];            
        });

        $messages = [];
        $rows_insert = [];

        foreach ($rows as $row_index => $row)
        {
            $row_filted = array_filter(array_map('strval', $row));

            $messages[$row_index] = (object)['pass' => false, 'limit' => false, 'empty' => empty($row_filted), 'updated' => false, 'exists' => [], 'errors' => [], 'row' => []];            
            
            //skip if empty
            if ($messages[$row_index]->empty) continue;

            foreach ($columns as $column)
            {             
                $value = $messages[$row_index]->row['C' . $column->id] = isset($row[$column->name]) ? remove_space($row[$column->name]) : '';                            

                if ($column->unique && array_key_exists($value, $column->exists))
                {
                    $messages[$row_index]->limit = $messages[$row_index]->limit || $column->exists[$value] != $this->user->id;

                    array_push($messages[$row_index]->exists, 'C' . $column->id);
                }

                if (!$column->isnull || !empty($value))
                {
                    $column_errors = $this->check_column($column, $value);

                    !empty($column_errors) && $messages[$row_index]->errors[$column->id] = $column_errors;
                }
            }

            $messages[$row_index]->pass = !$messages[$row_index]->limit && empty($messages[$row_index]->errors);
    
            //skip if not pass
            if (!$messages[$row_index]->pass) continue;           
            
            $messages[$row_index]->row['file_id'] = $file_upload->file->id;
            $messages[$row_index]->row['updated_by'] = $this->user->id;
            $messages[$row_index]->row['updated_at'] = Carbon::now()->toDateTimeString();            
            
            if (!empty($messages[$row_index]->exists))
            {
                $query = DB::table($table->database . '.dbo.' . $table->name);
                foreach ($messages[$row_index]->exists as $exist_id)
                {
                    $query->where($exist_id, $messages[$row_index]->row[$exist_id]);
                }
                $messages[$row_index]->updated = $query->update($messages[$row_index]->row);
            }
            else
            {
                $messages[$row_index]->row['created_by'] = $this->user->id;
                $messages[$row_index]->row['created_at'] = Carbon::now()->toDateTimeString();
                array_push($rows_insert, $messages[$row_index]->row);
            }         
        }   

        if (!$table->lock && count($rows_insert)>0) {
            $table->lock = true;
            $table->save();            
        }

        foreach(array_chunk($rows_insert, 50) as $rows_part)
        {
            DB::table($table->database . '.dbo.' . $table->name)->insert($rows_part);
        }

        return ['messages' => $messages];
    }    
    
    private function check_column($column, $column_value)
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

    private function has_table($table)
    {
        return DB::table($table->database . '.INFORMATION_SCHEMA.COLUMNS')->where('TABLE_NAME', $table->name)->exists();
    }

    private function table_construct($table)
    {
        if (!isset($table->builded_at) || Carbon::parse($table->builded_at)->diffInSeconds(new Carbon($table->construct_at), false) > 0) {
            $this->table_build($table);
        }
    }
    
    private function table_build($table)
    {
        $this->has_table($table) && Schema::drop($table->database . '.dbo.' . $table->name);

        Schema::create($table->database . '.dbo.' . $table->name, function($query) use($table) {
            $query->increments('id');

            foreach ($table->columns as $column) {
                $this->column_bulid($query, 'C' . $column->id, $column->rules);
            }

            $query->integer('file_id');
            $query->dateTime('updated_at');
            $query->dateTime('created_at');   
            $query->dateTime('deleted_at')->nullable(); 
            $query->integer('updated_by');
            $query->integer('created_by');
            $query->integer('deleted_by')->nullable();
        });

        $table->update(['builded_at' => Carbon::now()->toDateTimeString()]);
    }

    private function column_bulid($query, $name, $rule_key, $indexs = [])
    {
        if (isset($this->rules[$rule_key])) {
            $rule = $this->rules[$rule_key];
            $para = isset($rule['size']) ? [$name, $rule['size']] : [$name];
            call_user_func_array([$query, $rule['type']], $para);
            foreach ($indexs as $index) {
                $query->$index();          
            }  
        }    
    }

    public function request_to()
    {         
        $input = Input::only('groups', 'description');

        $myGroups = $this->user->groups;
        
        if ($this->isCreater()) {  
            foreach($input['groups'] as $group) {
                if (count($group['users']) == 0 && $myGroups->contains($group['id'])){                    
                    RequestFile::updateOrCreate(
                        ['target' => 'group', 'target_id' => $group['id'], 'doc_id' => $this->doc->id, 'created_by' => $this->user->id],
                        ['description' => $input['description']]
                    );
                }
                if (count($group['users']) != 0){
                    foreach($group['users'] as $user){
                        RequestFile::updateOrCreate(
                            ['target' => 'user', 'target_id' => $user['id'], 'doc_id' => $this->doc->id, 'created_by' => $this->user->id], 
                            ['description' => $input['description']]
                        );
                    }
                }
            }          
        }

        return Response::json(Input::all());
    } 

    public function generate_uniques()
    {
        $table = $this->file->sheets->each(function($sheet) {
            $sheet->tables->each(function($table) {                
                $columns = $table->columns->filter(function($column) {
                    return $column->unique && $column->rules=='stdidnumber';
                });    

                list($query, $power) = $this->get_rows_query([$table]); 

                $rows = $query->whereNotExists(function($query) use($table, $columns) {
                    $query->from($table->database . '.dbo.' . $table->name . '_map AS map');
                    foreach($columns as $column) {
                        $query->whereRaw('C' . $column->id . ' = map.stdidnumber');
                    }
                    $query->select(DB::raw(1));
                })
                ->whereNull('deleted_at')
                ->select($columns->map(function($column) { return 'C' . $column->id . ' AS stdidnumber'; })->toArray())
                ->get();

                foreach(array_chunk($rows, 50) as $part) {
                    $newcids = array_map(function($row) {
                        return [
                            'stdidnumber' => $row->stdidnumber,
                            'newcid' => createnewcid(strtoupper($row->stdidnumber))
                        ];
                    }, $part);

                    DB::table($table->database . '.dbo.' . $table->name . '_map')->insert($newcids);
                }
            }); 
        }); 
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
    
    public function export_my_rows() 
    {
        \Excel::create('sample', function($excel) {

            $excel->sheet('sample', function($sheet) {

                $tables = $this->file->sheets->find(Input::get('sheet_id'))->tables;

                list($query, $power) = $this->get_rows_query($tables);

                $head = $tables[0]->columns->map(function($column) { return 'C' . $column->id . ' AS ' . $column->name; })->toArray();
                
                $rows = array_map(function($row) {
                    return array_values(get_object_vars($row));
                }, $query->where('created_by', $this->user->id)->whereNull('deleted_at')->select($head)->get());

                array_unshift($rows, $tables[0]->columns->fetch('name')->toArray());                  

                $sheet->freezeFirstRow();

                $sheet->fromArray($rows, null, 'A1', false, false);

            });

        })->download('xls');
    }

    //uncomplete only first sheet, only first table
    public function get_rows()
    {
        $tables = $this->file->sheets->first()->tables;

        list($query, $power) = $this->get_rows_query($tables);

        $head = $tables[0]->columns->map(function($column) { return 'C' . $column->id; })->toArray();

        Input::has('searchText') && $tables[0]->columns->each(function($column) use($query) {
            $column->unique && $query->where('C' . $column->id, Input::get('searchText'));
        });

        $query->whereNull('deleted_at')->select($head)->addSelect('id');     

        return [
            'paginate' => $this->isCreater()
            ? $query->addSelect('created_by')->paginate(10)->toArray()
            : ['data' => $query->where('created_by', $this->user->id)->get()]
        ];
    } 

    //uncomplete only first sheet
    public function delete_rows()
    {
        $tables = $this->file->sheets->first()->tables->filter(function($table) {                
            $uniques = $table->columns->filter(function($column) { return $column->unique; })->map(function($column) {
                return 'C' . $column->id;
            })->toArray();

            $updates = array_merge(array_fill_keys($uniques, ''), ['deleted_by' => $this->user->id, 'deleted_at' => Carbon::now()->toDateTimeString()]);

            $query = DB::table($table->database . '.dbo.' . $table->name);

            !$this->isCreater() && $query->where('created_by', $this->user->id);

            return $query->whereIn('id', Input::get('rows'))->update($updates);
        });

        return ['tables' => $tables];
    }

    //uncomplete
    private function get_rows_query($tables) 
    {        
        foreach($tables as $index => $table) {
            if( $index==0 ){
                $query = DB::table($table->database . '.dbo.' . $table->name.' AS t0');
            }else{
                //join not complete
                //$rows_query->leftJoin($table->database . '.dbo.' . $table->name . ' AS t' . $index, 't' . $index . '.' . $table->primaryKey, '=', 't0.'.$table->primaryKey);
            }
        }
        $power = [];

        return [$query, $power];
    }

    public function checker($name)
    {
        $checkers = [
            'stdidnumber' => function($column_value, $column, &$column_errors) {
                !check_id_number($column_value) && array_push($column_errors, $column->title . '無效');
            },
            'schid_104' => function($column_value, $column, &$column_errors) {
                !isset($this->temp->works) && $this->temp->works = \User_use::find($this->user->id)->works->lists('sch_id');
                !in_array($column_value, $this->temp->works, true) && array_push($column_errors, '不是本校代碼');
            },
            'depcode_104' => function($column_value, $column, &$column_errors) {
                !isset($this->temp->dep_codes) && $this->temp->dep_codes = DB::table('rows.dbo.row_20150910_175955_h23of')
                    ->whereIn('C246', \User_use::find($this->user->id)->works->lists('sch_id'))->lists('C248');
                !in_array($column_value, $this->temp->dep_codes, true) && array_push($column_errors, '不是本校科別代碼');
            },
            'tted_sch' => function($column_value, $column, &$column_errors) {
                !isset($this->temp->schools) && $this->temp->schools = \User_tted::find($this->user->id)->works->lists('ushid');
                !in_array($column_value, $this->temp->schools, true) && array_push($column_errors, '不是本校代碼');
            },
            'tted_depcode_103' => function($column_value, $column, &$column_errors) {
                !isset($this->temp->dep_codes_103) && $this->temp->dep_codes_103 = DB::table('pub_depcode_tted')->whereIn('sch_id', \User_tted::find($this->user->id)->works->lists('ushid'))->where('year','=','103')->lists('id');
                !in_array($column_value, $this->temp->dep_codes_103, true) && array_push($column_errors, '不是本校系所代碼');
            },
            'tted_depcode_104' => function($column_value, $column, &$column_errors) {
                !isset($this->temp->dep_codes_104) && $this->temp->dep_codes_104 = DB::table('pub_depcode_tted')->whereIn('sch_id', \User_tted::find($this->user->id)->works->lists('ushid'))->where('year','=','103')->lists('id');
                !in_array($column_value, $this->temp->dep_codes_104, true) && array_push($column_errors, '不是本校系所代碼');
            },
        ];
        return $checkers[$name];
    }
    
    //uncomplete deprecated shareFile
    public function get_compact_files() 
    {
        $inGroups = $this->user->inGroups->lists('id');
        
        $myRowFiles = ShareFile::with('isFile')->whereHas('isFile', function($query){
            $query->where('type', '=', 5);
        })->where(function($query) {
            $query->where('target', 'user')->where('target_id', $this->user->id);
        })->orWhere(function($query) use($inGroups) {
            count($inGroups)>0 && $query->where('target', 'group')->whereIn('target_id', $inGroups)->where('created_by', '!=', $this->user->id);
        })->orderBy('created_at', 'desc')->get();
        
        $files = $myRowFiles->map(function($myRowFile) {    
            return [
                'title'  => $myRowFile->is_file->title,
                'doc_ic' => $myRowFile->id
            ];
        });
        return $files;
    }
    
    //uncomplete deprecated doc_id shareFile get_information
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
    
    //uncomplete deprecated doc_id shareFile get_information
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
    
    // uncomplete
    public function delete() 
    {
        //$this->file->delete();

        //$this->doc->delete();
        $this->doc->shareds->each(function($requested) {
            $requested->delete();
        });       

        $this->doc->requesteds->each(function($requested) {
            $requested->delete();
        });       

        return ['deleted' => true];
    }

    //deprecated
    private function drop_tables($schema)
    {
        foreach($schema->sheets as $sheet)
        {
            foreach($sheet->tables as $table)
            {       
                Schema::drop($table->database . '.dbo.' . $table->name);
            }            
        }
    }
}
