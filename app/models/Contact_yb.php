<?php
namespace Project\Yearbook;

use Eloquent;

class User extends \User {

    public function schools() {
        return $this->belongsToMany('Project\Yearbook\School', 'plat.dbo.work_yb', 'user_id', 'sch_id');
    }

    public function works() {
        return $this->hasMany('Project\Yearbook\Work', 'user_id', 'id');
    }

}

class School extends Eloquent {

    protected $table = 'public.dbo.university_school';

    public $timestamps = false;

}

class Work extends Eloquent {

    protected $table = 'plat.dbo.work_yb';

    public $timestamps = true;

    protected $fillable = array('sch_id', 'type');

    public function schools() {
        return $this->hasMany('Project\Yearbook\School', 'id', 'sch_id');
    }

}

class Struct {

    static function auth($member, $groups)
    {
        return array(
            'user_id'   => (int)$member->user_id,
            'member_id' => (int)$member->id,
            'actived'   => $member->user->actived && $member->actived,
            'password'  => $member->user->password=='',
            'email'     => $member->user->email,
            'name'      => $member->user->username,
            'title'     => $member->contact->title,
            'tel'       => $member->contact->tel,
            'fax'       => $member->contact->fax,
            'email2'    => $member->contact->email2,
            'schools'   => User::find($member->user_id)->schools->sortBy('id')->filter(function($school) {
                                return $school->year == '103';
                            })->map(function($school){
                                return array_only($school->toArray(), array('id', 'name', 'year'));
                            })->toArray(),
        );
    }

}
