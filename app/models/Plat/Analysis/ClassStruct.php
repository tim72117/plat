<?php
namespace Plat\Analysis;

use Eloquent;

class ClassStruct extends Eloquent{
    protected $table = 'analysis_tted.dbo.class_struct';

    public $timestamps = false;

    public function tables() {
        return $this->hasMany('Plat\Analysis\TableStruct');
    }
}