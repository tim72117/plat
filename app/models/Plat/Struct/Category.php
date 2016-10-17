<?php
namespace Plat\Struct;

use Eloquent;

class Category extends Eloquent{
    protected $table = 'analysis_tted.dbo.class_struct';

    public $timestamps = false;

    public function tables() {
        return $this->hasMany('Plat\Analysis\TableStruct');
    }
}