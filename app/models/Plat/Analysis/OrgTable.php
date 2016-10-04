<?php
namespace Plat\Analysis;

use Eloquent;

class OrgTable extends Eloquent {

    protected $table = 'analysis_tted.dbo.org_table';

    public $timestamps = false;

    public function rows() {
        return $this->hasMany('Plat\Analysis\OrgFilterRow');
    }

    public function explanations() {
        return $this->hasMany('Plat\Analysis\OrgExplan');
    }
}