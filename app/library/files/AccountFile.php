<?php
namespace Plat\Files;

use User;
use Files;
use DB, View, Response, Config, Schema, Session, Input, Auth, Request;
use ShareFile;
use Carbon\Carbon;
use Plat\Contact;
use Plat\Member;
use Plat\Project;

class AccountFile extends CommFile {

    function __construct(Files $file, User $user)
    {
        parent::__construct($file, $user);

        $this->configs = $this->file->configs->lists('value', 'name');
    }

    public function is_full()
    {
        return false;
    }

    public function get_views()
    {
        return ['open'];
    }

    public function open()
    {
        //$this->file->configs()->save(new \Doc\Config(['name' => 'project', 'value' => 2]));

        // $project = Project::find($this->configs['project']);

        // $users = $project->members->map(function($member) {
        //     var_dump($member->user);exit;
        // });

        return 'files.account.setAuth';

        return 'files.account.profile';
    }

    public function changeName()
    {
        return View::make('files.account.changeName');
    }

    public function get_accounts()
    {
        $project = Project::find($this->configs['project']);

        $users = $project->members->load('user', 'user.schools')->map(function($member) {
            //var_dump($member->user);
            var_dump(DB::getQueryLog());exit;
            return $this->account_to_view($member->user, $member, []);
        });

        //var_dump(DB::getQueryLog());exit;
        return ['members' => $users->toArray()];
    }

    public function get_account()
    {
        $user = \User_use::with('contact', 'works')->find($this->user->id);

        return ['user' => $user];
    }

    public function account_to_view($user, $contact, $groups)
    {
        return array(
            'id'         => (int)$user->id,
            'active'     => (bool)$user->active,
            'disabled'   => (bool)$user->disabled,
            'password'   => $user->password=='',
            'email'      => $user->email,
            'name'       => $user->username,
            // 'schools'    => $user->schools->map(function($school){
            //                     return array_only($school->toArray(), array('id', 'name', 'year'));
            //                 }),
            'title'  => $contact->title,
            'tel'    => $contact->tel,
            'fax'    => $contact->fax,
            'email2' => $contact->email2,
            //'groups' => $user->groups->lists('id'),
        );
    }

    public function apply_change_user()
    {
        var_dump($this->user->applying);exit;
        $query = DB::table('users_apply')->where('user_id', $this->user->id)->where('applied', false);
        if ($query->exists()) {
            $query->update([
                'username' => Input::get('user')['username'],
                'email' => Input::get('user')['email'],
                'updated_at' => new Carbon,
            ]);
        } else {
            DB::table('users_apply')->insert([
                'user_id' => $this->user->id,
                'username' => Input::get('user')['username'],
                'email' => Input::get('user')['email'],
                'updated_at' => new Carbon,
                'created_at' => new Carbon,
            ]);
        }
    }

    public function save_contact()
    {
        $user = \User_use::find(Auth::user()->id);

        $contact = array_only(Input::get('contact'), ['title', 'tel', 'fax', 'email2']);

        $validator = $user->contact()->getRelated()->validator($contact);

        if ($validator->fails()) {
            return ['messages' => $validator->messages()];
        } else {
            return ['status' => $user->contact()->update($contact)];
        }
    }

    public function get_register_das()
    {
        $user = \User_use::find(Auth::user()->id);

        $project_das_status = $user->project_actived('das');

        $register_print_query = DB::table('register_print')->where('user_id', $user->id)->where('project', 4);

        if ($project_das_status['registered'] && !$project_das_status['actived'])
        {
            if (!$register_print_query->exists())
            {
                $token = str_shuffle(sha1($user->email . spl_object_hash($user) . microtime(true)));

                DB::table('register_print')->insert(['token' => $token, 'user_id' => $user->id, 'project' => 4, 'created_at' => new Carbon]);
            } else {
                $token = DB::table('register_print')->where('user_id', $user->id)->where('project', 4)->orderBy('created_at', 'desc')->first()->token;
            }

            $project_das_status['token'] = $token;
        }

        return ['das_status' => $project_das_status];
    }

    public function register_das()
    {
        \User_use::find(Auth::user()->id)->contactdas()->create(['project' => 'das', 'created_ip' => Request::getClientIp()]);

        return $this->get_register_das();
    }

    public function getGroups()
    {
        $project_id = $this->configs['project_id'];

        return ['groups' => $this->user->groups, 'positions' => Project::find($project_id)->positions];
    }

    public function getUsers()
    {
        $project_id = $this->configs['project_id'];

        $members_query = Member::where('project_id', $project_id)->orderBy('user_id')->with(['user.positions', 'user.inGroups', 'contact', 'organizations.now']);

        Input::has('search.position') && $members_query->whereHas('user.positions', function($query) {
            $query->where('project_positions.id', Input::get('search.position'));
        });

        Input::has('search.organization') && $members_query->whereHas('organizations', function($query) {
            $query->where('works.organization_id', Input::get('search.organization.id'));
        });

        Input::has('search.username') && $members_query->whereHas('user', function($query) {
            $query->where('users.username', Input::get('search.username'));
        });

        Input::has('search.email') && $members_query->whereHas('user', function($query) {
            $query->where('users.email', Input::get('search.email'));
        });

        $members = $members_query->paginate(10);

        $profiles = $members->getCollection()->map(function($member) {
            return $this->setProfile($member);
        });

        return array('users' => $profiles, 'currentPage' => $members->getCurrentPage(), 'lastPage' => $members->getLastPage(), 'log' => DB::getQueryLog());
    }

    public function activeUser()
    {
        $member = Member::find(Input::get('member_id'));

        $member->user->actived = Input::get('actived') ? true : $member->user->actived;

        $member->actived = Input::get('actived');

        $member->push();

        return ['profile' => $this->setProfile($member)];
    }

    public function disableUser()
    {
        $member = Member::find(Input::get('member_id'));

        $disabled = isset($member) ? $member->delete() : false;

        return ['disabled' => $disabled];
    }

    public function addGroup()
    {
        $member = Member::find(Input::get('member_id'));

        if (!$member->user->inGroups->contains(Input::get('group_id'))) {
            $member->user->inGroups()->attach(Input::get('group_id'));
        }

        return ['inGroups' => $member->user->inGroups()->getResults()];
    }

    public function deleteGroup()
    {
        $member = Member::find(Input::get('member_id'));

        $member->user->inGroups()->detach(Input::get('group_id'));

        return ['inGroups' => $member->user->inGroups];
    }

    public function setUsername()
    {
        $updateKeys = ["title", "tel", "fax"];

        $userData = Input::get('userdata');

        $member = Member::find($userData['member_id']);

        $update_data = array_filter($userData, function($var) use ($updateKeys) {
            return in_array($var, $updateKeys);
        },ARRAY_FILTER_USE_KEY);

        $member->user->username = $userData['name'];

        $member->user->save();

        Contact::where('member_id', $userData['member_id'])->update($update_data);

        $organizations = array_fetch($userData['organizations'], 'id');

        $member->organizations()->sync($organizations);

        $member->load('organizations.now');

        return ['user' => $this->setProfile($member)];
    }

    public function queryOrganizations()
    {
        $organizationDetails = \Plat\Project\OrganizationDetail::where(function($query) {
            $query->where('name', 'like', '%' . Input::get('query') . '%')->orWhere('id', Input::get('query'));
        })->limit(2000)->lists('organization_id');

        $organizations = \Plat\Project\Organization::find($organizationDetails)->load('now');

        return ['organizations' => $organizations];
    }

    public function queryUsernames()
    {
        $project_id = $this->configs['project_id'];

        $usernames = Member::with('user')->where('project_id', $project_id)->whereHas('user', function($query) {
            $query->where('users.username', 'like', '%' . Input::get('query') . '%')->groupBy('users.username');
        })->limit(1000)->get()->fetch('user.username')->all();

        return ['usernames' => array_unique($usernames)];
    }

    public function queryEmails()
    {
        $project_id = $this->configs['project_id'];

        $emails = Member::with('user')->where('project_id', $project_id)->whereHas('user', function($query) {
            $query->where('users.email', 'like', '%' . Input::get('query') . '%');
        })->limit(1000)->get()->fetch('user.email');

        return ['emails' => $emails];
    }

    public function setProfile($member)
    {
        return [
            'id'        => (int)$member->user_id,
            'member_id' => (int)$member->id,
            'actived'   => $member->user->actived && $member->actived,
            'password'  => $member->user->password=='',
            'email'     => $member->user->email,
            'name'      => $member->user->username,
            'title'  => $member->contact->title,
            'tel'    => $member->contact->tel,
            'fax'    => $member->contact->fax,
            'email2' => $member->contact->email2,
            'inGroups'  => $member->user->inGroups,
            'organizations' => $member->organizations,
        ];
    }

}
