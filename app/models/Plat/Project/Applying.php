<?php

namespace Plat\Project;

use Eloquent;

class Applying extends Eloquent
{
    protected $table = 'member_applying';

    public $timestamps = true;

    protected $fillable = array('id', 'member_id');

    public function member()
    {
        return $this->hasOne('Plat\Project\Member', 'id', 'member_id');
    }

    public function getIdAttribute($value)
    {
        return $value;
    }
}