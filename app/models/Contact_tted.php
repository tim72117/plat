<?php
class User_tted extends User {

    public function schools() { 
		return $this->belongsToMany('School_tted', 'work_tted', 'user_id', 'ushid');
	}
    
    public function contact() { 
		return $this->hasOne('Contact_tted', 'user_id', 'id')->tted();
	}	

    public function works() {
        return $this->hasMany('Work_tted', 'user_id', 'id');
    }
}

class Contact_tted extends Contact {
    
    public function scopeTted($query)
    {
        return $query->where('project', 'tted');
    }
}

class School_tted extends Eloquent {
	
	protected $table = 'pub_school_u';

	public $timestamps = false;
	
}

class Work_tted extends Eloquent
{
    protected $table = 'work_tted';

    public function schools() {
        return $this->hasMany('School_tted', 'id', 'ushid');
    }
}

class Struct_tted
{
    static function auth($user, $groups)
    {
        return array(
            'id'         => (int)$user->id,
            'active'     => (bool)$user->active,
            'disabled'   => (bool)$user->disabled,
            'password'   => $user->password=='',
            'email'      => $user->email,
            'name'       => $user->username,
            'schools'    => $user->schools->map(function($school){
                                return array_only($school->toArray(), array('id', 'name', 'year'));
                            }),
            'title'  => $user->contact->title,
            'tel'    => $user->contact->tel,
            'fax'    => $user->contact->fax,
            'email2' => $user->contact->email2,
            'groups' => $user->groups->lists('id'),
        );
    }
}