<?php
namespace Row;

use Eloquent;

class Sheet extends Eloquent {

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
}

class Column extends Eloquent {

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

class Cencus extends Eloquent {

    protected $table = 'ques_doc';

    public $timestamps = false;

    protected $fillable = array('title', 'dir', 'edit');

    public function pages() {
        return $this->hasMany('Row\Pages', 'file_id', 'file_id');
    }
}

class Pages extends Eloquent {

    protected $table = 'ques_page';

    public $timestamps = true;

    protected $fillable = array('page', 'xml');
}

class Analysis extends Eloquent {

    protected $table = 'file_analysis';

    public $timestamps = false;

    public function pages() {
        return $this->hasMany('Row\Pages', 'file_id', 'file_id_ques');
    }

    public function ques() {
        return $this->hasOne('Files', 'id', 'file_id_ques');
    }
}