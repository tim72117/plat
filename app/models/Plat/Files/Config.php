<?php
namespace Plat\Files;

use Eloquent;

class Config extends Eloquent {

    protected $table = 'file_config';

    public $timestamps = false;

    protected $fillable = array('name', 'value');

}