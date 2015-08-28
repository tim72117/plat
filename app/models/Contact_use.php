<?php
class User_use extends User 
{
    public function contact() { 
		return $this->hasOne('Contact_use', 'user_id', 'id')->use();
	}

    public function schools() {
		return $this->belongsToMany('School_use', 'work', 'user_id', 'sch_id');
	}

    public function works() {
		return $this->hasMany('Work_use', 'user_id', 'id');
	}

    public function dascontact() { 
		return $this->hasOne('Contact_das', 'user_id', 'id')->use();
	}
    
    public function dasworks(){
        return $this->hasMany('Work_das', 'user_id', 'id');
    }
}

class Contact_use extends Contact
{
    public function scopeUse($query) {
        return $query->where('project', 'use');
    }
}

class Work_use extends Eloquent
{
    protected $table = 'work';

    public function schools() {
		return $this->hasMany('School_use', 'id', 'sch_id');
	}
}

class School_use extends Eloquent
{
	protected $table = 'pub_school';

	public $timestamps = false;	
}

class Work_das extends Eloquent
{
    protected $table = 'work_das';

    public function schools() {
		return $this->hasMany('School_use', 'id', 'ushid');
	}
    public $timestamps = false;	
}

class Contact_das extends Contact
{
    public function scopeUse($query) {
        return $query->where('project', 'das');
    }
}

class Struct_use
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
            'schools'    => $user->schools->map(function($school) {
                                return array_only($school->toArray(), array('id', 'sname', 'year'));
                            })->all(),
            'title'  => $user->contact->title,
            'tel'    => $user->contact->tel,
            'fax'    => $user->contact->fax,
            'email2' => $user->contact->email2,
            'groups' => [
                '1' => ['selected' => in_array(1, $groups)],
                '5' => ['selected' => in_array(5, $groups)],
            ],
        );   
    }
}
