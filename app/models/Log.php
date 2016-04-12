<?php
namespace QuestionXML;

use Eloquent;

class Log extends Eloquent {

    protected $table = 'plat_log.dbo.ques_update_log';

    public $timestamps = true;

    protected $fillable = array('session', 'host');

}
