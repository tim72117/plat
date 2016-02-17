<?php

use Illuminate\Filesystem\Filesystem;
use Illuminate\Config\FileLoader;
use Illuminate\Config\Repository;

class UserController extends BaseController {

    protected $layout = 'project.layout-main';

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

    public function loginPage($project)
    {
        $context = View::exists('project.' . $project->code . '.auth.login') ? 'project.' . $project->code . '.auth.login' : 'project.auth-login';

        return $this->createHomeView($project, 'home', $context);
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

            $members = $user->members->load('project')->filter(function($member) use ($project) {

                return ($member->project->code == $project->code || $project->code == Config::get('project.default')) && $member->actived;

            })->sortBy(function($member) use ($project) {

                return $member->project->code == $project->code ? 0 : 1;

            });

            if (!$user->actived || $members->isEmpty()) {
                throw new Plat\Files\ValidateException($validator->getMessageBag()->add('error', '帳號尚未開通'));
            }

            $member = $members->first();

            $member->logined_at = Carbon\Carbon::now()->toDateTimeString();

            $member->save();

            Auth::login($user, true);

            return Redirect::intended('project/intro');

        } else {
            throw new Plat\Files\ValidateException($validator->getMessageBag()->add('error', '帳號密碼錯誤'));
        }
    }

    public function remindPage($project)
    {
        return $this->createHomeView($project, 'home', 'project.auth-remind');
    }

    public function remind($project)
    {
        Config::set('auth.reminder.email', 'emails.auth.reminder_' . $project->code);

        $response = Password::remind(['email' => Input::get('email')], function($message) {
            $message->subject('重設您的查詢平台帳戶密碼');
        });

        switch($response) {
            case Password::INVALID_USER:
                return Redirect::back()->withErrors(['error' => Lang::get($response)]);

            case Password::REMINDER_SENT:
                return $this->createHomeView($project, 'home', 'project.auth-reminded');
        }
    }

    public function resetPage($project, $token)
    {
        return $this->createHomeView($project, 'home', 'project.auth-reset', ['token' => $token]);
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
                return Redirect::to('project/' . $project->code);
        }
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

        $input = $validator->getData()['user'];

        $user = new User;
        $user->username = $input['username'];
        $user->email    = $input['email'];
        $user->valid();

        try {
            DB::beginTransaction();

            $user->save();

            $member = Plat\Member::firstOrNew(['user_id' => $user->id, 'project_id' => $project->id]);
            $member->actived = false;
            $user->members()->save($member);

            $contact = Plat\Contact::firstOrNew(['member_id' => $member->id]);
            $contact->title      = $input['contact']['title'];
            $contact->tel        = $input['contact']['tel'];
            $contact->department = isset($input['contact']['department']) ? $input['contact']['department'] : '';
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
            return call_user_func($func);
        }
    }

}
