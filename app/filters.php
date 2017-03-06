<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
    //
});


App::after(function($request, $response)
{
    //
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function($route)
{
    if (Auth::guest()) {
        $project = $route->getParameter('project');
        $url = isset($project) ? 'project/' . $project->code : 'project/' . Config::get('project.default');
        return Redirect::guest($url);
    }
});


Route::filter('auth.basic', function()
{
    return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function($route)
{
    if (Auth::check()) {
        $project = $route->getParameter('project');
        return Redirect::to('project/' . $project->code . '/intro');
    }
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
    if (Session::token() != Input::get('_token'))
    {
        //throw new Illuminate\Session\TokenMismatchException;
        $messageBag = new Illuminate\Support\MessageBag();
        $messageBag->add('csrf', '畫面過期，請重新登入');
        throw new Plat\Files\TokenMismatchException($messageBag);
    }
});


Route::filter('post_delay', function()
{
    $ip = Request::server('REMOTE_ADDR');

    $ip_requested = sha1($ip.Request::url());

    $ip_requested_cache = Cache::get('post_delay' . $ip_requested, 0);

    sleep($ip_requested_cache);

    $ip_requested_cache++;

    Cache::put('post_delay' . $ip_requested, $ip_requested_cache, 10);
});


Route::filter('has_survey_login', function($route)
{
    $book_id = $route->getParameter('book_id');

    if(Plat\Surveys\SurveySession::check($book_id)){

        return Redirect::to('survey/'.$book_id.'/surveyLogin');
       
    }
});