<?php
namespace Plat\Analysis;

use Eloquent;

class Explan extends Eloquent{
    protected $table = 'analysis_tted.dbo.explan';

    public $timestamps = false;

    public function table() {
        return $this->belongsTo('Plat\Analysis\TableStruct');
    }
}