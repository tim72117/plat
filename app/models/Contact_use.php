<?php
namespace Project\Use;

use Eloquent;

class User extends \User {

    public function schools() {
        return $this->belongsToMany('Project\Use\School', 'work', 'user_id', 'sch_id');
    }

    public function works() {
        return $this->hasMany('Project\Use\Work', 'user_id', 'id');
    }

}

class School extends Eloquent {

    protected $table = 'pub_school';

    public $timestamps = false;

}

class Work extends Eloquent {

    protected $table = 'plat.dbo.work_use';

    public $timestamps = true;

    protected $fillable = array('schid', 'type');

    public function schools() {
        return $this->hasMany('Project\Use\School', 'id', 'sch_id');
    }

}

class Struct {

    static function auth($member, $groups)
    {
        return array(
            'id'         => (int)$member->user_id,
            'active'     => $member->user->actived && $member->actived,
            'password'   => $member->user->password=='',
            'email'      => $member->user->email,
            'name'       => $member->user->username,
            'schools'    => Project\Use\User::find($member->user_id)->schools->map(function($school) {
                                return array_only($school->toArray(), array('id', 'sname', 'year'));
                            })->all(),
            'title'  => $member->contact->title,
            'tel'    => $member->contact->tel,
            'fax'    => $member->contact->fax,
            'email2' => $member->contact->email2,
            'groups' => [
                '1'  => ['selected' => in_array(1, $groups)],
                '5'  => ['selected' => in_array(5, $groups)],
                '23' => ['selected' => in_array(23, $groups)],
            ],
        );
    }

}
