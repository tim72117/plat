<?php

namespace Plat\Files;

use Eloquent;

class FileType extends Eloquent {

    protected $table = 'file_type';

    public $timestamps = false;

    protected $fillable = array();

    public function modules()
    {
        return $this->hasMany('Plat\Files\Module', 'type_id', 'id');
    }
}