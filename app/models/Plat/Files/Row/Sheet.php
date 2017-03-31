<?php

namespace Plat\Files\Row;

use Eloquent;
use Files;

class Sheet extends Eloquent
{
    protected $table = 'row_sheets';

    public $timestamps = true;

    protected $fillable = array('title', 'editable', 'fillable');

    public function tables()
    {
        return $this->hasMany(Table::class, 'sheet_id', 'id');
    }

    public function file()
    {
        return $this->belongsTo(Files::class, 'file_id', 'id');
    }

    public function getEditableAttribute($value)
    {
        return (boolean)$value;
    }
}