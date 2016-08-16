<?php
namespace Plat\Analysis;

use Eloquent;

class RowStruct extends Eloquent{
    protected $table = 'analysis_tted.dbo.row_struct';

    public $timestamps = false;

    public function table() {
        return $this->belongsTo('Plat\Analysis\TableStruct');
    }

    public function items() {
        return $this->hasMany('Plat\Analysis\ItemStruct','row_struct_id','id');
    }
}