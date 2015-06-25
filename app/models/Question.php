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
}

class Answer extends Eloquent
{
	protected $table = 'ques_answers';
	
	public $timestamps = false;
	
	protected $fillable = array('ques_id', 'value', 'title');
}