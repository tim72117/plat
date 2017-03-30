<?php

namespace Plat\Project;

use Eloquent;
use Plat\Log;
use Auth;

class Member extends Eloquent {

    protected $table = 'user_member';

    public $timestamps = true;

    protected $attributes = ['actived' => false];

    protected $fillable = array('user_id', 'project_id', 'actived');

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
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
        return $this->hasOne('Plat\Project\Applying', 'member_id', 'id');
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