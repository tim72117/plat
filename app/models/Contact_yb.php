<?php
namespace Yearbook;

use Eloquent;

class User extends \User {

    public function schools() {
        return $this->belongsToMany('Yearbook\School', 'work_yb', 'user_id', 'ushid');
    }

    public function contact() {
        return $this->hasOne('Yearbook\Contact', 'user_id', 'id')->yb();
    }

    public function works() {
        return $this->hasMany('Yearbook\Work', 'user_id', 'id');
    }

}

class Contact extends \Contact {

    public function scopeYb($query)
    {
        return $query->where('project', 'yearbook');
    }

}

class School extends Eloquent {

    protected $table = 'public.dbo.university_school';

    public $timestamps = false;

}

class Work extends Eloquent {

    protected $table = 'work_yb';

    protected $fillable = array('ushid', 'type');

    public function schools() {
        return $this->hasMany('Yearbook\School', 'id', 'ushid');
    }

}

class Struct {

    static function auth($user, $groups)
    {
        return array(
            'id'         => (int)$user->id,
            'actived'    => (bool)$user->active && (bool)$user->contact->active,
            'password'   => $user->password=='',
            'email'      => $user->email,
            'name'       => $user->username,
            'schools'    => $user->schools->sortBy('id')->filter(function($school) {
                                return $school->year == '103';
                            })->map(function($school){
                                return array_only($school->toArray(), array('id', 'name', 'year'));
                            })->toArray(),
            'title'  => $user->contact->title,
            'tel'    => $user->contact->tel,
            'fax'    => $user->contact->fax,
            'email2' => $user->contact->email2,
        );
    }

}
