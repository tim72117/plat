<?php
namespace Plat;

use Eloquent;

class Group extends Eloquent {

    protected $table = 'groups';

    public $timestamps = false;

    public function users()
    {
        return $this->belongsToMany('App\User', 'user_in_group', 'group_id', 'user_id');
    }
}
