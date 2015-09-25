<?php
class User_tted extends User {

    public function schools() { 
		return $this->belongsToMany('School_tted', 'work_tted', 'user_id', 'ushid')->where('year', '103');
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