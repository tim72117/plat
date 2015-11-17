<?php

class User_yb extends User {

    public function schools() {
        return $this->belongsToMany('School_yb', 'work_yb', 'user_id', 'ushid');
    }

    public function contact() {
        return $this->hasOne('Contact_yb', 'user_id', 'id')->yb();
    }

    public function works() {
        return $this->hasMany('Work_yb', 'user_id', 'id');
    }
}

class Contact_yb extends Contact {

    public function scopeYb($query)
    {
        return $query->where('project', 'yearbook');
    }
}

class School_yb extends Eloquent {

    protected $table = 'pub_school_u';

    public $timestamps = false;

}

class Work_yb extends Eloquent
{
    protected $table = 'work_yb';

    protected $fillable = array('ushid', 'type');

    public function schools() {
        return $this->hasMany('School_yb', 'id', 'ushid');
    }
}

class Struct_yb
{
    static function auth($user, $groups)
    {
        return array(
            'id'         => (int)$user->id,
            'active'     => (bool)$user->active && (bool)$user->contact->active,
            'disabled'   => (bool)$user->disabled,
            'password'   => $user->password=='',
            'email'      => $user->email,
            'name'       => $user->username,
            'schools'    => $user->schools->sortBy('id')->map(function($school){
                                return array_only($school->toArray(), array('id', 'name', 'year'));
                            })->toArray(),
            'title'  => $user->contact->title,
            'tel'    => $user->contact->tel,
            'fax'    => $user->contact->fax,
            'email2' => $user->contact->email2,
        );
    }
}
