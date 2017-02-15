<?php

namespace Plat\Files;

use Eloquent;

class Tag extends Eloquent {

    protected $table = 'file_tags';

    public $timestamps = false;

}