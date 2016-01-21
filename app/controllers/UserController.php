<?php

class UserController extends BaseController {

    protected $layout = 'demo.layout-main';

    protected $auth_rull = array(
        'email'                 => 'required|email',
        'username'              => 'required|regex:/^[0-9a-zA-Z!@_]+$/|between:3,20',
        'password'              => 'required|regex:/^[0-9a-zA-Z!@#$%^&*]+$/|between:6,20',
        'password_confirmation' => 'required|regex:/^[0-9a-zA-Z!@#$%^&*]+$/|between:6,20|confirmed',
    );

    protected $rulls_message = array(
        'email.required'                 => '電子郵件必填',
        'email.email'                    => '電子郵件格式錯誤',
        'password.required'              => '密碼必填',
        'password.regex'                 => '密碼格式錯誤',
        'password.between'               => '密碼格式必須介於 6 - 20 個字元',
        'password_confirmation.required' => '確認密碼必填',
        'password_confirmation.regex'    => '確認密碼格式錯誤',
        'password_confirmation.between'  => '確認密碼格式必須介於 6 - 20 個字元',
        'password.confirmed'             => '確認密碼必須相同',
    );

    public function __construct() {}

    public function logout()
    {
        $member = Auth::user()->members()->logined()->orderBy('logined_at', 'desc')->first();

        Auth::logout();

        if ($member) {
            return Redirect::to('project/' . $member->project->code);
        }
    }

    public function loginPage($project = null)
    {
        if (!$project) {
            return Redirect::to('project/' . Config::get('project.default'));
        }

        return $this->createHomeView($project, 'demo.' . $project . '.auth.login');
    }

    public function login($project)
    {
        $input = Input::only('email', 'password');

        $rulls = array(
            'email'    => $this->auth_rull['email'],
            'password' => $this->auth_rull['password'],
        );

        $validator = Validator::make($input, $rulls, $this->rulls_message);

        if ($validator->fails()) {
            throw new Plat\Files\ValidateException($validator);
        }

        if (Auth::once($input)) {

            $user = Auth::user();

            $members = $user->members->load('project')->filter(function($member) use($project) {
                return $member->project->code == $project && $member->actived;
            });

            if (!$user->actived || $members->isEmpty()) {
                $validator->getMessageBag()->add('login_error', '帳號尚未開通');
                throw new Plat\Files\ValidateException($validator);
            }

            $member = $members->first();

            $member->logined_at = Carbon\Carbon::now()->toDateTimeString();

            $member->save();

            Auth::login($user, true);

            return Redirect::intended('page/project');

        } else {
            $validator->getMessageBag()->add('login_error', '帳號密碼錯誤');
            throw new Plat\Files\ValidateException($validator);
        }
    }

    public function remindPage($project)
    {
        return $this->createHomeView($project, 'demo.' . $project . '.auth.remind');
    }

    public function remind($project)
    {
        $credentials = array('email' => Input::get('email'));
        Config::set('auth.reminder.email', 'emails.auth.reminder_'.$project);
        $response = Password::remind($credentials, function($message) {
            $message->subject('重設您的查詢平台帳戶密碼');
        });
        switch($response) {
            case Password::INVALID_USER:
                return Redirect::back()->withErrors(['error' => Lang::get($response)]);

            case Password::REMINDER_SENT:
                return View::make('demo.' . $project . '.home', array('contextFile'=>'remind', 'title'=>'重設密碼信件已寄出'))
                    ->with('context', '<div style="margin:30px auto;width:300px;color:#f00">重設密碼信件已寄出，請到您的電子郵件信箱收取信件</div>')
                    ->nest('child_footer','demo.'.$project.'.footer');
        }
    }

    public function resetPage($project, $token)
    {
        return $this->createHomeView($project, 'demo.' . $project . '.auth.reset', ['token' => $token]);
    }

    public function reset($project, $token)
    {
        $input = Input::only('email', 'password', 'password_confirmation');

        $rulls = array(
            'email'                 => $this->auth_rull['email'],
            'password'              => $this->auth_rull['password_confirmation'],
            'password_confirmation' => $this->auth_rull['password'],
        );

        $validator = Validator::make($input, $rulls, $this->rulls_message);

        if ($validator->fails()) {
            throw new Plat\Files\ValidateException($validator);
        }

        $response = Password::reset(array_merge($input, ['token' => $token]), function($user, $password) {
            $user->password = Hash::make($password);

            $user->save();
        });

        switch($response) {
            case Password::INVALID_PASSWORD:
            case Password::INVALID_TOKEN:
            case Password::INVALID_USER:
                return Redirect::back()->withErrors(['error' => Lang::get($response)]);

            case Password::PASSWORD_RESET:
                return Redirect::to('project' . $project);
        }
    }

    public function passwordChangePage()
    {
        $member = Auth::user()->members()->logined()->orderBy('logined_at', 'desc')->first();

        $contents = View::make('demo.main', ['project' => $member->project])->nest('context','demo.passwordChange');

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

    public function registerPage($project)
    {
        $register = Plat\Project::where('code', $project)->first()->register;

        $context = $register ? 'demo.' . $project . '.auth.register' : 'demo.' . $project . '.auth.register_stop';

        return $this->createHomeView($project, $context);
    }

    public function registerSave($project)
    {
        $validator = require app_path() . '\\views\\demo\\' . $project . '\\auth\\register_validator.php';

        if ($validator->fails()) {
            throw new Plat\Files\ValidateException($validator);
        }

        $input = $validator->getData();

        $member = require app_path() . '\\views\\demo\\' . $project . '\\auth\\register_member.php';

        if ($member) {

            $applying = new Plat\Applying(['id' => sha1(spl_object_hash($user) . microtime(true))]);

            $member->applying()->save($applying);

            try {
                Config::set('auth.reminder.email', 'emails.auth.register_' . $project);
                Password::remind(['email' => $member->user->getReminderEmail()], function($message) {
                    $message->subject('教育資料庫整合平臺-註冊通知');
                });
            } catch (Exception $e) {

            }

            return Redirect::to('project/' . $project . '/register/finish/' . $member->applying->id);
        } else {
            return Redirect::back();
        }
    }

    public function registerFinish($project, $token)
    {
        return $this->createHomeView($project, 'demo.auth.register_finish', ['register_print_url' => URL::to('project/' . $project . '/register/print/' . $token)]);
    }

    public function registerPrint($project, $token)
    {
        $applying = Plat\Applying::find($token);

        if ($applying->exists()) {
            return View::make('demo.' . $project . '.auth.register_print', ['member' => $applying->member]);
        }
    }

    public function terms($project)
    {
        return View::make('demo.' . $project . '.home')->nest('context', 'demo.' . $project . '.auth.register_terms')->nest('child_footer', 'demo.' . $project . '.footer');
    }

    public function help($project)
    {
        return View::make('demo.' . $project . '.home')->nest('context', 'demo.' . $project . '.auth.register_help')->nest('child_footer', 'demo.' . $project . '.footer');
    }

    public function createHomeView($project, $context, $args = [])
    {
        View::share('project', $project);

        return View::make('demo.' . $project . '.home')->nest('context', $context, $args)->nest('child_footer', 'demo.' . $project . '.footer');
    }

    public function profile($project_code, $parameter = null)
    {
        $project = Plat\Project::where('code', $project_code)->first();

        $member = Auth::user()->members()->where('project_id', $project->id)->first();

        View::share('parameter', $parameter);

        return View::make('demo.main', ['project' => $project])->nest('context', 'demo.' . $project->code . '.auth.profile', ['member' => $member]);
    }

    public function profileSave($project_code, $parameter = null)
    {
        switch ($parameter) {
            case 'power':
                $attributes = ['user_id' => Auth::user()->id, 'project_id' => Input::get('project_id')];
                $member = Plat\Member::where($attributes)->withTrashed()->first() ?: new Plat\Member($attributes);

                $member->actived = false;

                require app_path() . '\\views\\demo\\' . $project_code . '\\auth\\register_works.php';

                if ($member->trashed()) {
                    $member->restore();
                } else {
                    Auth::user()->members()->save($member);
                }

                $member->contact()->save(Plat\Contact::firstOrNew(['member_id' => $member->id]));

                $applying = new Plat\Applying(['member_id' => $member->id]);

                $applying->id = sha1(spl_object_hash(Auth::user()) . microtime(true));

                $member->applying()->save($applying);
                break;

            case 'contact':
                $project = Plat\Project::where('code', $project_code)->first();
                $member = Auth::user()->members()->where('project_id', $project->id)->first();
                $member->contact->title = Input::get('title');
                $member->contact->tel = Input::get('tel');
                $member->contact->fax = Input::get('fax');
                $member->contact->email2 = Input::get('email2');

                $member->push();
                break;

            case 'changeUser':
                $user = Auth::user();
                $user->username = Input::get('username');
                $user->email = Input::get('email');
                $user->valid();
                $user->members->each(function($member) {
                    $member->actived = false;
                });
                $user->push();
                break;

            default:
                # code...
                break;
        }
        return Redirect::to(Request::path());
    }

}
