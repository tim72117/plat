<?php
namespace Row;
use Eloquent;

class Sheet extends Eloquent
{
	protected $table = 'row_sheets';

	public $timestamps = true;
    
    protected $fillable = array('title', 'editable');

    public function tables() {
    	return $this->hasMany('Row\Table', 'sheet_id', 'id');
    }
}

class Table extends Eloquent
{
	protected $table = 'row_tables';

	public $timestamps = true;
    
    protected $fillable = array('database', 'name', 'builded_at');

    public function columns() {
    	return $this->hasMany('Row\Column', 'table_id', 'id');
    }
}

class Column extends Eloquent
{
	protected $table = 'row_columns';

	public $timestamps = true;
    
    protected $fillable = array('name', 'title', 'rules', 'unique', 'encrypt', 'isnull');
}