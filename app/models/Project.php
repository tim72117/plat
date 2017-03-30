<?php
namespace Plat;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Project extends Eloquent {

    protected $table = 'projects';

    public $timestamps = false;

    protected $fillable = array('code', 'name', 'register');

    public function members()
    {
        return $this->hasMany('Plat\Project\Member', 'project_id', 'id');
    }

    public function posts()
    {
        return $this->hasMany('Plat\Post', 'project_id', 'id')->orderBy('publish_at', 'desc');
    }

    public function positions()
    {
        return $this->hasMany('Plat\Position', 'project_id', 'id');
    }

    public function organizations()
    {
        return $this->hasMany('Plat\Project\Organization', 'project_id', 'id');
    }

    public function getRegisterAttribute($value)
    {
        return (bool)$value;
    }

}

class Post extends Eloquent {

    use SoftDeletes;

    protected $table = 'project_post';

    public $timestamps = true;

    protected $fillable = array('title', 'context', 'publish_at', 'display_at', 'created_by', 'perpetual');

    public function files()
    {
        return $this->belongsToMany('Files', 'project_post_file', 'post_id', 'file_id')->withPivot('id');
    }

    public function getDisplayAtAttribute($value)
    {
        return json_decode($value);
    }
}

class Position extends Eloquent {

    protected $table = 'project_positions';

    public $timestamps = false;

    protected $fillable = array('title');

}
