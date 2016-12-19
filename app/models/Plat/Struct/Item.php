<?php
namespace Plat\Struct;

use Eloquent;

class Item extends Eloquent{
    protected $table = 'analysis_tted.dbo.item_struct';

    public $timestamps = false;

    public function row() {
        return $this->belongsTo('Plat\Analysis\RowStruct','row_struct_id','id');
    }
}