<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $attributes = [
        'actived' => false,
    ];

    /**
     * Relations
     */
    public function groups() {
        return $this->belongsToMany('Plat\Group', 'user_own_group', 'user_id', 'group_id');
    }

    public function inGroups() {
        return $this->belongsToMany('Plat\Group', 'user_in_group', 'user_id', 'group_id');
    }

    public function members() {
        return $this->hasMany('Plat\Project\Member', 'user_id', 'id');
    }

    public function positions()
    {
        return $this->belongsToMany('Plat\Position', 'user_positions', 'user_id', 'position_id');
    }

    public function getActivedAttribute($value)
    {
        return (bool)$value;
    }
}
