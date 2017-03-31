<?php

namespace Plat\Files\Row;

use Eloquent;

class Column extends Eloquent
{
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
        return $this->belongsTo(Table::class, 'table_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'column_id', 'id');
    }

    public function skip()
    {
        return $this->hasOne(Skip::class, 'column_id', 'id');
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