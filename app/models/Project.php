<?php
namespace Doc;

use Eloquent;

class Project extends Eloquent {

    protected $table = 'projects';

    public $timestamps = false;

    protected $fillable = array('code', 'name', 'register');

    public function members()
    {
        return $this->hasMany('Contact', 'project', 'code');
    }
}