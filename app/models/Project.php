<?php
namespace Plat;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Auth;

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

    public function organizations()
    {
        return $this->hasMany('Plat\Project\Organization', 'project_id', 'id');
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

    protected $attributes = ['actived' => false];

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

    public function organizations()
    {
        return $this->belongsToMany('Plat\Project\Organization', 'works', 'member_id','organization_id');
    }

    public function scopeLogined($query)
    {
        return $query->where('actived', true)->whereNotNull('logined_at');
    }

    public function getActivedAttribute($value)
    {
        return (bool)$value;
    }

    public static function boot()
    {
        parent::boot();

        Member::updated(function($member) {
            if ($member->isDirty('actived')) {
                Log\Change::create([
                    'table_name' => 'user_member',
                    'column_name' => 'actived',
                    'row_id' => $member->id,
                    'origin' => $member->getOriginal('actived'),
                    'created_by' => Auth::user()->id,
                ]);
            }
        });

        Member::deleted(function($member) {
            Log\Change::create([
                'table_name' => 'user_member',
                'column_name' => 'deleted_at',
                'row_id' => $member->id,
                'origin' => '',
                'created_by' => Auth::user()->id,
            ]);
        });
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
