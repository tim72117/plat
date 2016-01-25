<?php
namespace Project\Cher;

use Eloquent;

class User extends \User {

    public function departments() {
        return $this->belongsToMany('Project\Cher\Department', 'work_tiped', 'user_id', 'sch_id');
    }

    public function works() {
        return $this->hasMany('Project\Cher\Work', 'user_id', 'id');
    }

}

class Department extends Eloquent {

    protected $table = 'public.dbo.university_school';

    public $timestamps = false;

}

class Work extends Eloquent {

    protected $table = 'plat.dbo.work_tiped';

    public $timestamps = true;

    protected $fillable = array('sch_id', 'dep_id', 'sch_name', 'type');

    public function departments() {
        return $this->hasMany('Project\Cher\Department', 'id', 'sch_id');
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
            'departments' => Project\Cher\User::find($member->user_id)->departments->toArray(),
            'title'  => $member->contact->title,
            'tel'    => $member->contact->tel,
            'fax'    => $member->contact->fax,
            'email2' => $member->contact->email2,
        );
    }

}