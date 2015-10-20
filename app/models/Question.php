<?php

class Question extends Eloquent
{	
	protected $table = 'ques_new';
	
	public $timestamps = false;

	protected $fillable = array('title', 'type');
	
    public function answers() {
        return $this->hasMany('Answer', 'ques_id', 'id')->orderBy('value');
    }
	
    public function subs() {
        return $this->hasMany('Question', 'parent', 'id');
    }

    public function questions() {
        return $this->hasMany('Question', 'parent', 'id');
    }

     public function ques_page() {
         return $this->belongsToMany ('Ques_page', 'Ques_middle', 'page_id', 'ques_id');
     }
}

class Answer extends Eloquent
{
	protected $table = 'ques_answers';
	
	public $timestamps = false;
	
	protected $fillable = array('ques_id', 'value', 'title');
}

class Ques_middle extends Eloquent
{
	protected $table = 'ques_middle';
	
	public $timestamps = false;
	
	protected $fillable = array( 'id', 'page_id','ques_id');
}

class Ques_page extends Eloquent
{   

    protected $table = 'ques_page_new';
    
    public $timestamps = true;
    
    protected $fillable = array('file_id', 'value', 'rewrite');
    
    protected $guarded = array('id');

    public function questions() {
        return $this->belongsToMany ('Question', 'Ques_middle', 'page_id', 'ques_id');
    }
}