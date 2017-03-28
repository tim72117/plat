<?php

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Config\FileLoader;
use Illuminate\Config\Repository;

class UserController extends Controller {

    protected $layout = 'project.layout-main';

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
