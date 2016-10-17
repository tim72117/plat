<?php
namespace Plat\Struct;

use Eloquent;

class Table extends Eloquent {

    protected $table = 'analysis_tted.dbo.table_struct';

    public $timestamps = false;

    public function classes() {
        return $this->belongsTo('Plat\Struct\Category');
    }

    public function rows() {
        return $this->hasMany('Plat\Struct\Row', 'table_struct_id', 'id');
    }

    public function explanations() {
        return $this->hasMany('Plat\Struct\Explan');
    }

    public function items() {
        return $this->hasManyThrough('Plat\Struct\Item', 'Plat\Struct\Row', 'row_struct_title', 'title');
    }
}