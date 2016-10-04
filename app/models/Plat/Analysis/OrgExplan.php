<?php
namespace Plat\Analysis;

use Eloquent;

class OrgExplan extends Eloquent{
    protected $table = 'analysis_tted.dbo.org_explan';

    public $timestamps = false;

    public function table() {
        return $this->belongsTo('Plat\Analysis\OrgTable');
    }
}