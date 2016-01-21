<?php
namespace Doc;

use Eloquent;

class Config extends Eloquent
{
    protected $table = 'file_config';

    public $timestamps = true;

    protected $fillable = array('name', 'value');
}