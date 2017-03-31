<?php

namespace Plat\Files\Row;

use Eloquent;

class Skip extends Eloquent
{
    protected $table = 'row_table_column_skip';

    public $timestamps = false;

    protected $fillable = array('rules');

    public function getRulesAttribute($value)
    {
        return json_decode($value);
    }
}