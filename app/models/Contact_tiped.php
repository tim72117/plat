<?php
class User_tiped extends User
{
    public function contact() { 
		return $this->hasOne('Contact_tiped', 'user_id', 'id')->use();
	}

    public function schools() { 
		return $this->belongsToMany('School_tiped', 'work_tiped', 'user_id', 'sch_id');
	}

	public function departments() { 
		return $this->belongsToMany('Department_tiped', 'work_tiped', 'user_id', 'dep_id');
	}
	
}

class Contact_tiped extends Contact
{
    public function scopeUse($query) {
        return $query->where('contact.project', 'tiped');
    }
}

class School_tiped extends Eloquent {
	
	protected $table = 'plat_resource.dbo.university_school';

	public $timestamps = false;
		
}

class Department_tiped extends Eloquent {
	
	protected $table = 'plat_resource.dbo.university_depcode';

	public $timestamps = false;
	
}

class Struct_tiped
{  
    static function auth($user) 
    {
        return array(
            'id'          => (int)$user->id,
            'active'      => (bool)$user->active,
            'disabled'    => (bool)$user->disabled,
            'password'    => $user->password=='',
            'email'       => $user->email,
            'name'        => $user->username,
            'schools'     => $user->schools->toArray(),
            'departments' => $user->departments->toArray(),
            'title'  => $user->contact->title,
            'tel'    => $user->contact->tel,
            'fax'    => $user->contact->fax,
            'email2' => $user->contact->email2,
        );   
    }
}