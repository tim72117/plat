<?php

namespace Plat\Files;

use Eloquent;

class Module extends Eloquent {

    protected $table = 'file_modules';

    public $timestamps = false;

    protected $fillable = array('class');

}