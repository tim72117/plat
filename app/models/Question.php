<?php
namespace Ques;

use Eloquent;

class Book extends Eloquent {

    protected $table = 'file_book';

    public $timestamps = false;

    protected $fillable = array('file_id', 'title');

    protected $guarded = array('id');

    public function wave()
    {
        return $this->hasOne('Set\Wave', 'id', 'wave_id');
    }

    public function set()
    {
        return $this->hasOne('Set\Book', 'book_id', 'id');
    }

    public function questions()
    {
        return $this->hasMany('Ques\Question', 'book_id', 'id');
    }

    public function getRewriteAttribute($value)
    {
        return (bool)$value;
    }

}

class Question extends Eloquent {

    protected $table = 'interview_questions';

    public $timestamps = true;

    protected $fillable = array('book_id', 'title', 'type', 'image_id', 'parent_answer_id', 'parent_question_id');

    public function book()
    {
        return $this->hasOne('Ques\Book', 'id', 'book_id');
    }

    public function answers()
    {
        return $this->hasMany('Ques\Answer', 'question_id', 'id');
    }

    public function sets()
    {
        return $this->hasMany('Set\Question', 'question_id', 'id');
    }

    public function questions()
    {
        return $this->hasMany('Ques\Question', 'parent_question_id', 'id');
    }

    public function getParentQuestionIdAttribute($value)
    {
        return isset($value) ? $value : false;
    }

    public function getParentAnswerIdAttribute($value)
    {
        return isset($value) ? $value : false;
    }

}

class Answer extends Eloquent {

    protected $table = 'interview_answers';

    public $timestamps = false;

    protected $fillable = array('question_id', 'value', 'title');

    public function question()
    {
        return $this->hasOne('Ques\Question', 'id', 'ques_id');
    }

    public function install()
    {
        return $this->hasOne('Ques\Install', 'id', 'install_id');
    }

}

class Install extends Eloquent {

    protected $table = 'interview_install';

    public $timestamps = true;

    protected $fillable = array('answer_id', 'column_name');

    protected $guarded = array('id');

}

class Image extends Eloquent {

    protected $table = 'interview_images';

    public $timestamps = false;

    protected $fillable = array('path');

}
