<?php
namespace Row;
use Eloquent;

class Sheet extends Eloquent
{
	protected $table = 'row_sheets';

	public $timestamps = true;
    
    protected $fillable = array('title', 'editable');

    public function getEditableAttribute($value) {
        return (boolean)$value;
    }

    public function tables() {
    	return $this->hasMany('Row\Table', 'sheet_id', 'id');
    }
}

class Table extends Eloquent
{
	protected $table = 'row_tables';

	public $timestamps = true;
    
    protected $fillable = array('database', 'name', 'lock', 'builded_at', 'construct_at');

    public function getLockAttribute($value) {
        return (boolean)$value;
    }

    public function columns() {
    	return $this->hasMany('Row\Column', 'table_id', 'id');
    }
}

class Column extends Eloquent
{
	protected $table = 'row_columns';

	public $timestamps = true;
    
    protected $fillable = array('name', 'title', 'rules', 'unique', 'encrypt', 'isnull');

    function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->deleting(function($model) {
            return $model->inTable->update(['construct_at' => \Carbon\Carbon::now()->toDateTimeString()]);
        });

        $this->saving(function($model) {
            $rules_updated = $model->isDirty('rules') && $model->inTable->update(['construct_at' => \Carbon\Carbon::now()->toDateTimeString()]);
            return $model->isDirty('rules') ? $rules_updated : true;
        });    
    }

    public function inTable() {
        return $this->belongsTo('Row\Table', 'table_id');
    }
}