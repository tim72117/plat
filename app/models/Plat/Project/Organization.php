<?php
namespace Plat\Project;

use Eloquent;

class Organization extends Eloquent {

    protected $table = 'organizations';

    public $timestamps = false;

    protected $fillable = array();

    public function now()
    {
        return $this->hasOne('Plat\Project\OrganizationDetail', 'organization_id', 'id')->orderBy('year', 'desc');
    }

}

class OrganizationDetail extends Eloquent {

    protected $table = 'plat.dbo.organization_details';

    public $timestamps = false;

    protected $fillable = array();

    public function organization()
    {
        return $this->hasOne('Plat\Project\Organization', 'id', 'organization_id');
    }

}
