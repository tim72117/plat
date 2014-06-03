<?php

class School extends Eloquent {
	
	protected $table = 'pub_school';
	
	public $timestamps = false;
	
}

class Contact_sch_use extends Eloquent {
	
	protected $table = 'contact_sch_use';
	
	public $timestamps = false;
	
}

class Contact extends Eloquent {
	
	protected $table = 'contact';
	
	public $timestamps = true;
	
	protected $fillable = array('sch_id', 'department', 'department_class', 'title', 'tel', 'fax', 'schpeo', 'senior1', 'senior2', 'tutor', 'parent', 'sname', 'created_by', 'created_ip');
	
	protected $guarded = array('id');
	

	
}

