<?php
namespace Plat\Analysis;

use Eloquent;

class TableStruct extends Eloquent {

    protected $table = 'analysis_tted.dbo.table_struct';

    public $timestamps = false;

    public function classes() {
        return $this->belongsTo('Plat\Analysis\ClassStruct');
    }

    public function rows() {
        return $this->hasMany('Plat\Analysis\RowStruct');
    }

    public function explanations() {
        return $this->hasMany('Plat\Analysis\Explan');
    }

    public function items() {
        return $this->hasManyThrough('Plat\Analysis\ItemStruct','Plat\Analysis\RowStruct','row_struct_title','title');
    }
}