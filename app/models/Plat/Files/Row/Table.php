<?php

namespace Plat\Files\Row;

use Eloquent;

class Table extends Eloquent
{
    protected $table = 'row_tables';

    public $timestamps = true;

    protected $fillable = array('database', 'name', 'lock', 'builded_at', 'construct_at');

    public function columns() {
        return $this->hasMany(Column::class, 'table_id', 'id');
    }

    public function depend_tables()
    {
        return $this->belongsToMany(Table::class, 'row_table_depend', 'table_id', 'depend_table_id');
    }

    public function sheet()
    {
        return $this->belongsTo(Sheet::class, 'sheet_id', 'id');
    }

    public function getLockAttribute($value) {
        return (boolean)$value;
    }
}