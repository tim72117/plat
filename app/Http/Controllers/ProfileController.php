<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use View;

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

    public function profile($project, $parameter = null)
    {
        $member = Auth::user()->members()->where('project_id', $project->id)->first();

        View::share('parameter', $parameter);
        View::share('paths', []);

        return View::make('project.main', ['project' => $project])->nest('context', 'project.profile', []);
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

    public function getMyMembers($project) {

        $projects = \Plat\Project::all()->keyBy('id');

        $members = Auth::user()->members->load('user', 'contact', 'applying', 'organizations.now')->each(function($member) use ($projects) {
            $projects[$member->project_id]->member = $member;
        });

        return ['projects' => $projects, 'projectNow' => $project];
    }

    public function registerPage($project)
    {
        $context = $project->register ? 'project.' . $project->code . '.auth.register' : 'project.register-stop';

        return $this->createHomeView($project, 'register', $context);
    }

    public function registerSave($project)
    {
        $validator = require app_path() . '\\views\\project\\' . $project->code . '\\auth\\register_validator.php';

        if ($validator->fails()) {
            throw new Plat\Files\ValidateException($validator);
        }

        $input = $validator->getData();

        $user = new User;
        $user->username = $input['username'];
        $user->email    = $input['email'];
        $user->actived = false;
        $user->disabled = false;
        $user->valid();

        try {
            DB::beginTransaction();

            $user->save();

            $member = Plat\Member::firstOrNew(['user_id' => $user->id, 'project_id' => $project->id]);
            $member->actived = false;
            $user->members()->save($member);

            $contact = Plat\Contact::firstOrNew(['member_id' => $member->id]);
            $contact->title      = $input['title'];
            $contact->tel        = $input['tel'];
            $contact->department = isset($input['department']) ? $input['department'] : '';

            $member->contact()->save($contact);

            require app_path() . '\\views\\project\\' . $project->code . '\\auth\\register_works.php';

            DB::commit();
        } catch (\PDOException $e) {
            DB::rollback();
            throw $e;
        }

        if ($member) {

            $applying = new Plat\Applying(['id' => sha1(spl_object_hash($user) . microtime(true))]);

            $member->applying()->save($applying);

            try {
                Config::set('auth.reminder.email', 'emails.auth.register_' . $project->code);
                View::share('applying_id', $member->applying->id);
                Password::remind(['email' => $member->user->getReminderEmail()], function($message) {
                    $message->subject('教育資料庫整合平臺-註冊通知');
                });
            } catch (Exception $e) {

            }

            if (Request::ajax()) {
                return ['applying_id' => $member->applying->id];
            } else {
                return Redirect::to('project/' . $project->code . '/register/finish/' . $member->applying->id);
            }

        } else {
            return Redirect::back();
        }
    }

    public function registerFinish($project, $token)
    {
        return $this->createHomeView($project, 'home', 'project.register-finish', ['register_print_url' => URL::to('project/' . $project->code . '/register/print/' . $token)]);
    }

    public function registerPrint($project, $token)
    {
        $applying = Plat\Applying::find($token);

        if ($applying->exists()) {
            return View::make('project.' . $project->code . '.auth.register_print', ['member' => $applying->member]);
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

    public function registerAjax($project, $method)
    {
        $fileLoader = new FileLoader(new Filesystem, app_path() . '\\views\\project\\' . $project->code . '\\auth');

        $repository = new Repository($fileLoader, '');

        $func = $repository->get('function-register.' . $method);

        if (is_callable($func)) {
            return call_user_func($func, $project);
        }
    }

}
