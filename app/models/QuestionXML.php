<?php
namespace QuestionXML;

use Eloquent;

class Census extends Eloquent {

    protected $table = 'ques_doc';

    public $timestamps = false;

    protected $fillable = array('title', 'dir', 'edit');

    public function pages() {
        return $this->hasMany('QuestionXML\Pages', 'file_id', 'file_id');
    }
}

class Pages extends Eloquent {

    protected $table = 'ques_page';

    public $timestamps = true;

    protected $fillable = array('page', 'xml');
}

class Analysis extends Eloquent {

    protected $table = 'file_analysis';

    public $timestamps = false;

    protected $fillable = array('site', 'title', 'time_start', 'time_end', 'method', 'target_people', 'quantity_total', 'quantity_gets');

    public function pages() {
        return $this->hasMany('QuestionXML\Pages', 'file_id', 'file_id_ques');
    }

    public function ques() {
        return $this->hasOne('Files', 'id', 'file_id_ques');
    }
}
