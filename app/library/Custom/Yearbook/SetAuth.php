<?php

namespace Plat\Files\Custom\Yearbook;

use Input;
use DB;
use Cache;
use Auth;
use Plat\Member;
use Project\Yearbook\Struct;

class SetAuth {

    public $full = false;

    public function open()
    {
        return 'files.custom.setAuth_yb';
    }

    public function getProfiles()
    {
        $members = Member::where('project_id', 8)->with(['user', 'contact'])->get();

        $profiles = $members->map(function($member) {
            return Struct::auth($member, []);
        });

        return ['profiles' => $profiles];
    }

    public function active()
    {
        $member = Member::find(Input::get('member_id'));

        $member->user->actived = Input::get('actived') ? true : $member->user->actived;

        $member->actived = Input::get('actived');

        $member->push();

        return ['profiles' => Struct::auth($member, [])];
    }

    public function disable()
    {
        $member = Member::find(Input::get('member_id'));

        $disabled = isset($member) ? $member->delete() : false;

        return ['disabled' => $disabled];
    }

}