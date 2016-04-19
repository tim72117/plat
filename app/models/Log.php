<?php
namespace QuestionXML;

use Eloquent;

class Log extends Eloquent {

    protected $table = 'plat_log.dbo.ques_update_log';

    public $timestamps = true;

    protected $fillable = array('session', 'host');

}

class Report extends Eloquent {

    protected $table = 'plat_log.dbo.report';

    public $timestamps = false;

    protected $fillable = array('census_id', 'root', 'contact', 'text', 'explorer', 'solve', 'time', 'host');

    public function getSolveAttribute($value)
    {
        return (bool)$value;
    }

}
