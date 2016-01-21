<?php
namespace Project\Yearbook;

use Eloquent;

class User extends \User {

    public function schools() {
        return $this->belongsToMany('Project\Yearbook\School', 'work_yb', 'user_id', 'ushid');
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

    protected $fillable = array('ushid', 'type');

    public function schools() {
        return $this->hasMany('Project\Yearbook\School', 'id', 'ushid');
    }

}

class Struct {

    static function auth($member, $groups)
    {
        return array(
            'id'         => (int)$member->user_id,
            'actived'    => $member->user->actived && $member->actived,
            'password'   => $member->user->password=='',
            'email'      => $member->user->email,
            'name'       => $member->user->username,
            'schools'    => Project\Yearbook\User::find($member->user_id)->schools->sortBy('id')->filter(function($school) {
                                return $school->year == '103';
                            })->map(function($school){
                                return array_only($school->toArray(), array('id', 'name', 'year'));
                            })->toArray(),
            'title'  => $member->contact->title,
            'tel'    => $member->contact->tel,
            'fax'    => $member->contact->fax,
            'email2' => $member->contact->email2,
        );
    }

}
