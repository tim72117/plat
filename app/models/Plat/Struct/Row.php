<?php
namespace Plat\Struct;

use Eloquent;

class Row extends Eloquent{
    protected $table = 'analysis_tted.dbo.row_struct';

    public $timestamps = false;

    public function table() {
        return $this->belongsTo('Plat\Analysis\TableStruct');
    }

    public function items() {
        return $this->hasMany('Plat\Analysis\ItemStruct','row_struct_id','id');
    }
}