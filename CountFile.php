<?php
namespace app\library\files\v0;
use DB, View, Response, Config, Schema, Session, Input, ShareFile, Auth, Question, Answer, Carbon\Carbon;

class CountFile extends CommFile {
	
	/**
	 * @var rows
	 */
	public $rows;

	/**
	 * @var info
	 */
	public $info;

	public static $intent = array(
        'open',	
        'create'
	);
        
    function __construct($doc_id) 
    {
        $shareFile = ShareFile::find($doc_id);

        parent::__construct($shareFile);

        $this->information = json_decode($this->file->information);
    }
	
	public static function get_intent() 
    {
		return array_merge(parent::$intent,self::$intent);
	}
	     
    public function get_views() 
    {
        return ['open'];
    }
    
    public static function create($newFile) 
    {
        $shareFile = parent::create($newFile);

        return $shareFile;
    }
    
    public function open()
    {        
        return 'files.count.count'; 
    }

    function decodeInput($input)
    {        
        return json_decode(urldecode(base64_decode($input)));
    }

    public function get_columns()
    {
        $columns = DB::table($this->information->database . '.INFORMATION_SCHEMA.COLUMNS')->where('TABLE_NAME', $this->information->table)
            ->where('COLUMN_NAME', '<>', '身分識別碼')
            ->select('COLUMN_NAME')->get();

        return ['columns' => $columns];
    }

    public function get_variable()
    {
        $column_name = Input::get('name');

        $variables = DB::table($this->information->database . '.dbo.' . $this->information->table)
            ->groupBy($column_name)
            ->orderBy($column_name)
            ->select($column_name . ' AS name')            
            ->get();

        return ['variables' => $variables];    
    }

    public function get_frequence()
    {
        $column_name = Input::get('name');

        $columns = $this->decodeInput(Input::get('columns'));        

        $frequences_query = DB::table($this->information->database . '.dbo.' . $this->information->table)
            ->groupBy($column_name)
            ->select(DB::raw('count(*) AS total, ' . $column_name . ' AS name'));

        foreach($columns as $column) {
            $filters = [];
            if( array_key_exists('variables', $column) )                
            foreach($column->variables as $variable) {
                isset($variable->selected) && $variable->selected && array_push($filters, $variable->name);
            }  

            count($filters) > 0 && $frequences_query->whereIn($column->COLUMN_NAME, $filters);

            //var_dump($filters);
        }   

        $frequences = $frequences_query->get();

        return ['frequences' => $frequences];
    }

    public function get_crosstable()
    {
        $column_name1 = Input::get('name1');
        $column_name2 = Input::get('name2');

        $frequences = DB::table($this->information->database . '.dbo.' . $this->information->table)
            ->groupBy($column_name1, $column_name2)
            ->select(DB::raw('count(*) AS total, ' . $column_name1 . ' AS name1, ' . $column_name2 . ' AS name2'))            
            ->get();

        $columns_horizontal = [];
        $columns_vertical = [];
        $crosstable = [];

        foreach($frequences as $frequence) {
            $columns_horizontal = array_add($columns_horizontal, $frequence->name1, $frequence->name1);
            $columns_vertical = array_add($columns_vertical, $frequence->name2, $frequence->name2);
            $crosstable = array_add($crosstable, $frequence->name1, []);
            $crosstable[$frequence->name1][$frequence->name2] = $frequence->total;
        }

        return ['crosstable' => $crosstable, 'columns_horizontal' => $columns_horizontal, 'columns_vertical' => $columns_vertical]; 
    }
}