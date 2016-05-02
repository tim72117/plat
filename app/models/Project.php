<?php
namespace Plat;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Project extends Eloquent {

    protected $table = 'projects';

    public $timestamps = false;

    protected $fillable = array('code', 'name', 'register');

    public function members()
    {
        return $this->hasMany('Plat\Member', 'project_id', 'id');
    }

    public function posts()
    {
        return $this->hasMany('Plat\Post', 'project_id', 'id')->orderBy('publish_at', 'desc');
    }

    public function positions()
    {
        return $this->hasMany('Plat\Position', 'project_id', 'id');
    }

    public function getRegisterAttribute($value)
    {
        return (bool)$value;
    }

}

class Member extends Eloquent {

    use SoftDeletingTrait;

    protected $table = 'user_member';

    public $timestamps = true;

    protected $fillable = array('user_id', 'project_id', 'actived');

    public function user()
    {
        return $this->hasOne('User', 'id', 'user_id');
    }

    public function project()
    {
        return $this->hasOne('Plat\Project', 'id', 'project_id');
    }

    public function contact()
    {
        return $this->hasOne('Plat\Contact', 'member_id', 'id');
    }

    public function applying()
    {
        return $this->hasOne('Plat\Applying', 'member_id', 'id');
    }

    public function scopeLogined($query)
    {
        return $query->where('actived', true)->whereNotNull('logined_at');
    }

    public function getActivedAttribute($value)
    {
        return (bool)$value;
    }

}

class Applying extends Eloquent {

    protected $table = 'member_applying';

    public $timestamps = true;

    protected $fillable = array('id', 'member_id');

    public function member()
    {
        return $this->hasOne('Plat\Member', 'id', 'member_id');
    }

}

class Post extends Eloquent {

    use SoftDeletingTrait;

    protected $table = 'project_post';

    public $timestamps = true;

    protected $fillable = array('title', 'context', 'publish_at', 'display_at', 'created_by');

}

class Position extends Eloquent {

    protected $table = 'project_positions';

    public $timestamps = false;

    protected $fillable = array('title');

}
