<?php
namespace Set;

use Eloquent;

class Wave extends Eloquent {

    protected $table = 'cdb.dbo.waves';

    public $timestamps = true;

    protected $fillable = array('ques', 'active', 'month', 'start', 'end', 'wait_start', 'method');

    protected $guarded = array('id');

    public function books()
    {
        return $this->hasMany('Set\Book', 'wave_id', 'id');
    }

    public function nanny_books()
    {
        return $this->hasMany('Set\Book', 'wave_id', 'id');
    }

    public function baby()
    {
        return $this->belongsToMany('Cdb\Baby', 'cdb.dbo.wave_controller', 'baby_id', 'wave_id');
    }

    public function visit()
    {
        return $this->hasMany('Cdb\Visit_parent', 'wave_id', 'id');
    }

    // public function getRepositoryAttribute()
    // {
    //     $repository = new \Cdb\Ques_repository();

    //     $repository->setTable($this->repository_name);

    //     return $repository;
    // }

}

class Book extends Eloquent {

    protected $table = 'interview_set_books';

    public $timestamps = false;

    protected $fillable = array('wave_id', 'book_id', 'title', 'start', 'rewrite', 'quit', 'type', 'class', 'result');

    public function is()
    {
        return $this->hasOne('Ques\Book', 'id', 'book_id');
    }

    public function wave()
    {
        return $this->hasOne('Set\Wave', 'id', 'wave_id');
    }

    public function questions()
    {
        return $this->hasMany('Set\Question', 'book_id', 'id');
    }

    public function rules()
    {
        return $this->morphToMany('Set\Rule', 'interview_skip');
    }

    public function getStartAttribute($value)
    {
        return (bool)$value;
    }

    public function getRewriteAttribute($value)
    {
        return (bool)$value;
    }

}

class Question extends Eloquent {

    protected $table = 'interview_set_questions';

    public $timestamps = false;

    protected $fillable = array('book_id', 'question_id', 'page', 'sorter', 'parent_answer_id', 'parent_question_id');

    protected $attributes = array('required' => true);

    public function is()
    {
        return $this->hasOne('Ques\Question', 'id', 'question_id');
    }

    public function book()
    {
        return $this->hasOne('Set\Book', 'id', 'book_id');
    }

    public function answers()
    {
        return $this->hasMany('Set\Answer', 'question_id', 'id');
    }

    public function questions()
    {
        return $this->hasMany('Set\Question', 'parent_question_id', 'id');
    }

    public function parentQuestion()
    {
        return $this->hasOne('Set\Question', 'id', 'parent_question_id');
    }

    public function parent()
    {
        return $this->hasOne('Set\Answer', 'id', 'parent_answer_id');
    }

    public function rules()
    {
        return $this->morphToMany('Set\Rule', 'interview_skip');
    }

    public function rule()
    {
        return $this->hasManyThrough('Set\Rule', 'Set\Answer');
    }

    public function subs()
    {
        return $this->hasManyThrough('Set\Question', 'Set\Answer');
    }

    public function scopePage($query, $page)
    {
        return $query->wherePage($page);
    }

    public function getParentQuestionIdAttribute($value)
    {
        return isset($value) ? $value : false;
    }

    public function getParentAnswerIdAttribute($value)
    {
        return isset($value) ? $value : false;
    }

    public function getRequiredAttribute($value)
    {
        return (bool)$value;
    }

}

class Answer extends Eloquent {

    protected $table = 'interview_set_answers';

    public $timestamps = false;

    protected $fillable = array('question_id', 'answer_id', 'improve');

    public function is()
    {
        return $this->hasOne('Ques\Answer', 'id', 'answer_id');
    }

    public function question()
    {
        return $this->hasOne('Set\Question', 'id', 'question_id');
    }

    public function childrens()
    {
        return $this->hasMany('Set\Question', 'parent_answer_id', 'id');
    }

    public function rules()
    {
        return $this->morphToMany('Set\Rule', 'interview_skip');
    }

    public function rule()
    {
        return $this->hasOne('Set\Rule', 'expression', 'expression');
    }

    public function getExpressionAttribute()
    {
        $parameter = (object)[$this->question_id => $this->id];
        $json = (object)['expression' => 'r1', 'parameters' => [$parameter]];
        return json_encode($json);
    }

}

class Rule extends Eloquent {

    protected $table = 'interview_rules';

    public $timestamps = true;

    protected $fillable = array('expression','answer_id', 'warning');

    protected $appends = array('is');

    public function skipQuestion()
    {
        return $this->morphedByMany('Set\Question', 'interview_skip');
    }

    public function skipAnswers()
    {
        return $this->morphedByMany('Set\Answer', 'interview_skip');
    }

    public function jumpBook()
    {
        return $this->morphedByMany('Set\Book', 'interview_skip');
    }

    public function openWave()
    {
        return $this->morphedByMany('Set\Wave', 'interview_skip');
    }

    public function answers()
    {
        return $this->belongsToMany('Set\Answer','interview_answers_in_rule', 'rule_id', 'answer_id' );
    }

    public function getIsAttribute($value)
    {
        return json_decode($this->expression);
    }

}