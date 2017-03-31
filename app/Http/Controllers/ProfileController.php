<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use View;
use DB;
use Carbon;

class ProfileController extends Controller {

    protected $layout = 'project.layout-main';

    protected $auth_rull = array(
        'username'              => 'required|regex:/^[0-9a-zA-Z!@_]+$/|between:3,20',
        'password'              => 'required|regex:/^[0-9a-zA-Z!@#$%^&*]+$/|between:6,20',
        'password_confirmation' => 'required|regex:/^[0-9a-zA-Z!@#$%^&*]+$/|between:6,20|confirmed',
    );

    protected $rulls_message = array(
        'password.required'              => '密碼必填',
        'password.regex'                 => '密碼格式錯誤',
        'password.between'               => '密碼格式必須介於 6 - 20 個字元',
        'password_confirmation.required' => '確認密碼必填',
        'password_confirmation.regex'    => '確認密碼格式錯誤',
        'password_confirmation.between'  => '確認密碼格式必須介於 6 - 20 個字元',
        'password.confirmed'             => '確認密碼必須相同',
    );

    public function __construct()
    {
        // $this->beforeFilter(function($route){
        //     $project = $route->getParameter('project');
        //     if (isset($project) && !Auth::user()->members()->logined()->where('project_id', $project->id)->exists()) {
        //         return Response::view('noAuth', array(), 403);
        //     }
        // });
    }

    public function passwordChangePage()
    {
        $member = Auth::user()->members()->logined()->orderBy('logined_at', 'desc')->first();

        View::share('paths', []);

        $contents = View::make('project.main', ['project' => $member->project])->nest('context','project.passwordChange');

        $this->layout->content = $contents;
    }

    public function passwordChange()
    {
        $input = Input::only('passwordold', 'password', 'password_confirmation');

        $rulls = array(
            'passwordold'            => $this->auth_rull['password'],
            'password'               => $this->auth_rull['password_confirmation'],
            'password_confirmation'  => $this->auth_rull['password'],
        );

        $validator = Validator::make($input, $rulls, $this->rulls_message);

        if ($validator->fails()) {
            throw new Plat\Files\ValidateException($validator);
        }

        $user = Auth::User();

        if (Hash::check($input['passwordold'], $user->password)) {
            $user->password = Hash::make($input['password']);
            $user->save();
            $validator->getMessageBag()->add('passwordold', '密碼設定成功');
            return Redirect::back()->withErrors($validator);
        } else {
            $validator->getMessageBag()->add('passwordold', '舊密碼錯誤');
            return Redirect::back()->withErrors($validator);
        }
    }

    public function profile($parameter = null)
    {
        //$member = Auth::user()->members()->orderBy('logined_at', 'desc')->first();

        View::share('parameter', $parameter);
        View::share('paths', []);

        return View::make('project.main', [])->nest('context', 'profile.register', []);
    }

    public function profileSave($project, $parameter = null, Request $request)
    {
        switch ($parameter) {
            case 'power':
                $attributes = ['user_id' => Auth::user()->id, 'project_id' => $request->get('project_id')];
                $member = \Plat\Member::where($attributes)->withTrashed()->first() ?: Plat\Member::create($attributes);

                require base_path() . '\\resources\\views\\project\\' . $project->code . '\\auth\\register_power.php';

                if ($member->trashed()) {
                    $member->restore();
                } else {
                    Auth::user()->members()->save($member);
                }

                $member->contact()->save(\Plat\Contact::firstOrNew(['member_id' => $member->id]));

                $applying = new \Plat\Applying(['member_id' => $member->id]);

                $applying->id = sha1(spl_object_hash(Auth::user()) . microtime(true));

                $member->applying()->save($applying);
                break;

            case 'contact':
                $member = Auth::user()->members()->where('project_id', $project->id)->first();
                $member->contact->title = $request->input('contact.title');
                $member->contact->tel = $request->input('contact.tel');
                $member->contact->fax = $request->input('contact.fax');
                $member->contact->email2 = $request->input('contact.email2');
                $member->push();
                break;

            case 'changeUser':
                $member = Auth::user()->members()->where('project_id', $project->id)->first();
                $member->user->username = $request->input('user.username');
                $member->user->email = $request->input('user.email');
                $member->user->save();
                break;

            default:
                # code...
                break;
        }

        return ['member' => $member->load('applying')];
    }

    public function getMyMembers()
    {
        $member = Auth::user()->members()->logined()->orderBy('logined_at', 'desc')->first();

        $members = Auth::user()->members->load('user', 'contact', 'applying', 'project',  'organizations.now');

        return ['projects' => $members, 'projectNow' => $member->project];
    }

    public function template($key)
    {
        $templates = [
            'member' => 'profile.member',
        ];

        return View::make($templates[$key]);
    }

    public function projects(Request $request)
    {
        $projects = \Plat\Project::where('name', 'like', '%' . $request->get('name') . '%')->get();

        return ['projects' => $projects];
    }

    public function getMyProjects()
    {
        $projects = Auth::user()->members->load('project')->pluck('project');

        return ['projects' => $projects];
    }

    public function changeProject($project_id)
    {
        $project = \Plat\Project::find($project_id);

        $member = $project->members()->where('user_id', Auth::user()->id)->first();

        $member->logined_at = Carbon\Carbon::now()->toDateTimeString();

        $member->save();

        return redirect('/project?project_id=' . $project_id);
    }

    public function getCitys()
    {
        $citys = DB::table('plat_public.dbo.lists')->where('type', 'city')->select('name', 'code')->get();

        return ['citys' => $citys];
    }

    public function getOrganizations(Request $request)
    {
        $project = \Plat\Project::find($request->get('project_id'));

        $grades = [
            'tted' => ['0', '1', '3', '4', '5', '6', '7', '8', 'F', 'K', 'W', 'X', 'Y', 'Z', 'M', 'S'],
            'use' => ['0', '1', '2', '3', '4', 'B', 'C'],
            'yearbook' => ['0', '1'],
        ];

        $grade = $grades[$project->code];

        $organizations = DB::table('plat.dbo.organizations AS organizations')
            ->leftJoin('plat.dbo.organization_details AS details', 'organizations.id', '=', 'details.organization_id')
            ->where(function($query) use($grade) {
                $query->whereIn('details.grade', $grade)->orWhereNull('details.grade');
            })
            ->where('details.citycode', $request->get('city_code'))
            ->select('organizations.id', 'details.name', 'details.sysname')
            ->orderBy('details.year', 'desc')
            ->get();

        return ['organizations' => $organizations];
    }

    public function getPositions(Request $request)
    {
        $project = \Plat\Project::find($request->get('project_id'));

        return ['positions' => $project->positions];
    }

    public function getMember(Request $request)
    {
        if (!$request->has('project_id')) {
            return [];
        }

        $project = \Plat\Project::find($request->get('project_id'));

        $member = $project->members()->where('user_id', Auth::user()->id)->first();

        if ($member) {
            $member->load(['contact', 'applying', 'project']);
        }

        return ['member' => $member];
    }

    public function saveMember(Request $request)
    {
        $project = \Plat\Project::find($request->input('member.project.id'));

        $member = $project->members()->updateOrCreate(['user_id' => Auth::user()->id], ['actived' => false]);

        $contact = $member->contact()->updateOrCreate([], [
            'title' => $request->input('member.contact.title'),
            'tel' => $request->input('member.contact.tel'),
            'department' => $request->input('member.contact.department'),
        ]);

        $member->applying()->updateOrCreate([], ['id' => sha1(spl_object_hash(Auth::user()) . microtime(true))]);

        $member->organizations()->attach($request->input('member.organization.id'));

        $positions = array_keys(array_filter($request->input('member.user.positions' , [])));

        $member->user->positions()->attach($positions);

        return ['member' => $member->load('applying')];

        try {
            DB::beginTransaction();

            //require app_path() . '\\views\\project\\' . $project->code . '\\auth\\register_works.php';

            DB::commit();
        } catch (\PDOException $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function registerPrint($key)
    {
        $applying = \Plat\Project\Applying::find($key);

        if ($applying->exists()) {
            return View::make('project.' . $applying->member->project->code . '.auth.register_print', ['member' => $applying->member]);
        }
    }

    public function terms($project)
    {
        return $this->createHomeView($project, 'home', 'project.' . $project->code . '.auth.register_terms');
    }

    public function help($project)
    {
        return $this->createHomeView($project, 'home', 'project.' . $project->code . '.auth.register_help');
    }

    public function createHomeView(Plat\Project $project, $layout, $context, $args = [])
    {
        View::share('project', $project);

        return View::make('project.layout-' . $layout)->nest('context', $context, $args)->nest('child_footer', 'project.' . $project->code . '.footer');
    }
}
