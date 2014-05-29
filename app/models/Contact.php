<?php
class Contact extends Eloquent {
	
	protected $table = 'contact';
	
	public $timestamps = true;
	
	protected $fillable = array('sch_id', 'department', 'department_class', 'title', 'tel', 'fax', 'schpeo', 'senior1', 'senior2', 'tutor', 'parent', 'created_ip');
	
	protected $guarded = array('id');

	
}