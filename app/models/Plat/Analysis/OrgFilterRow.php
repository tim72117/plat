<?php
namespace Plat\Analysis;

use Eloquent;

class OrgFilterRow extends Eloquent{
    protected $table = 'analysis_tted.dbo.org_filter_row';

    public $timestamps = false;

    public function table() {
        return $this->belongsTo('Plat\Analysis\OrgTable');
    }
}