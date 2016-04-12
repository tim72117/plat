<?php
namespace Project\Used;

use Eloquent;

class User extends \User {

    public function schools() {
        return $this->belongsToMany('Project\Used\School', 'work_used', 'user_id', 'sch_id');
    }

    public function works() {
        return $this->hasMany('Project\Used\Work', 'user_id', 'id');
    }

}

class School extends Eloquent {

    protected $table = 'plat_public.dbo.secondary_school';

    public $timestamps = false;

}

class Work extends Eloquent {

    protected $table = 'work_used';

    public $timestamps = true;

    protected $fillable = array('sch_id', 'department_class');

    public function schools() {
        return $this->hasMany('Project\Used\School', 'id', 'sch_id');
    }

}

class Struct {

    static function auth($member, $groups)
    {
        return array(
            'id'        => (int)$member->user_id,
            'member_id' => (int)$member->id,
            'actived'   => $member->user->actived && $member->actived,
            'password'  => $member->user->password=='',
            'email'     => $member->user->email,
            'name'      => $member->user->username,
            'title'  => $member->contact->title,
            'tel'    => $member->contact->tel,
            'fax'    => $member->contact->fax,
            'email2' => $member->contact->email2,
            'inGroups'  => $member->user->inGroups,
            'schools'   => \Project\Used\User::find($member->user_id)->schools->map(function($school) {
                                return array_only($school->toArray(), array('id', 'name', 'year'));
                            })->all(),
        );
    }

}
