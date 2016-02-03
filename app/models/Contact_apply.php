<?php
namespace Project\Apply;

use Eloquent;

class User extends \User {

    public function departments() {
        return $this->belongsToMany('Project\Apply\Department', 'plat.dbo.work_apply', 'user_id', 'sch_id');
    }

    public function works() {
        return $this->hasMany('Project\Apply\Work', 'user_id', 'id');
    }

}

class Department extends Eloquent {

    protected $table = 'plat_public.dbo.university_school';

    public $timestamps = false;

}

class Work extends Eloquent {

    protected $table = 'plat.dbo.work_apply';

    public $timestamps = true;

    protected $fillable = array('sch_id', 'sch_name', 'type');

    public function departments() {
        return $this->hasMany('Project\Apply\Department', 'id', 'sch_id');
    }

}

class Struct {

    static function auth($member)
    {
        return array(
            'id'          => (int)$member->user_id,
            'actived'     => $member->user->actived && $member->actived,
            'password'    => $member->user->password=='',
            'email'       => $member->user->email,
            'name'        => $member->user->username,
            'departments' => Project\Apply\User::find($member->user_id)->departments->toArray(),
            'title'  => $member->contact->title,
            'tel'    => $member->contact->tel,
            'fax'    => $member->contact->fax,
            'email2' => $member->contact->email2,
        );
    }

}