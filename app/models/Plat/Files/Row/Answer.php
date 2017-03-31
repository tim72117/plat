<?php

namespace Plat\Files\Row;

use Eloquent;

class Answer extends Eloquent
{
    protected $table = 'row_table_column_answers';

    public $timestamps = false;

    protected $fillable = array('value', 'title');
}