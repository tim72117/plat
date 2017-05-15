<?php
namespace Row;

use Eloquent;

class Sheet extends Eloquent {

    protected $table = 'row_sheets';

    public $timestamps = true;

    protected $fillable = array('title', 'editable', 'fillable');

    public function getEditableAttribute($value) {
        return (boolean)$value;
    }

    public function getFillableAttribute($value) {
        return (boolean)$value;
    }

    public function tables() {
        return $this->hasMany('Row\Table', 'sheet_id', 'id');
    }

    public function file()
    {
        return $this->belongsTo('Files', 'file_id', 'id');
    }

}

class Table extends Eloquent {

    protected $table = 'row_tables';

    public $timestamps = true;

    protected $fillable = array('database', 'name', 'lock', 'builded_at', 'construct_at');

    public function getLockAttribute($value) {
        return (boolean)$value;
    }

    public function columns() {
        return $this->hasMany('Row\Column', 'table_id', 'id');
    }

    public function depend_tables()
    {
        return $this->belongsToMany('Row\Table', 'row_table_depend', 'table_id', 'depend_table_id');
    }

    public function sheet()
    {
        return $this->belongsTo('Row\Sheet', 'sheet_id', 'id');
    }

}

class Column extends Eloquent {

    protected $table = 'row_columns';

    public $timestamps = true;

    protected $fillable = array('name', 'title', 'rules', 'unique', 'encrypt', 'isnull', 'readonly');

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

    public function answers()
    {
        return $this->hasMany('Row\Answer', 'column_id', 'id');
    }

    public function skip()
    {
        return $this->hasOne('Plat\Files\Row\Skip', 'column_id', 'id');
    }

    public function getUniqueAttribute($value)
    {
        return (boolean)$value;
    }

    public function getEncryptAttribute($value)
    {
        return (boolean)$value;
    }

    public function getIsnullAttribute($value)
    {
        return (boolean)$value;
    }

    public function getReadonlyAttribute($value)
    {
        return (boolean)$value;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower($value);
    }

    public function setUniqueAttribute($value)
    {
        $this->attributes['unique'] = isset($value) ? $value : false;
    }

    public function setEncryptAttribute($value)
    {
        $this->attributes['encrypt'] = isset($value) ? $value : false;
    }

    public function setIsnullAttribute($value)
    {
        $this->attributes['isnull'] = isset($value) ? $value : false;
    }

    public function setReadonlyAttribute($value)
    {
        $this->attributes['readonly'] = isset($value) ? $value : false;
    }

}

class Answer extends Eloquent {

    protected $table = 'row_table_column_answers';

    public $timestamps = false;

    protected $fillable = array('value', 'title');

}
