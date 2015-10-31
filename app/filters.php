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

Route::filter('auth', function()
{
    if (Auth::guest())
        return Redirect::guest('project');
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

Route::filter('guest', function()
{
    if (Auth::check()) return Redirect::to('page/project');
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
        throw new app\library\files\v0\TokenMismatchException($messageBag);
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


Route::filter('limit', function() {
    $user = Auth::user();
    $limit = DB::table('limit')->where('user_id', $user->id)->select('ip')->first();
    if (!is_null($limit)){
        $ipAllows = explode(",",$limit->ip);
        $ipPass = false;
        $myIp = Request::getClientIp();
        foreach ($ipAllows as $ipAllow ) {
            $ipRange = explode("-", $ipAllow);
            if (count($ipRange) > 1) {
                $ipLongs = array_map(function($ip){
                    return ip2long($ip);
                }, $ipRange);
                $ipLongs[0]<=ip2long($myIp) && $ipLongs[1]>=ip2long($myIp) && $ipPass = true;
            } else {
                $myIp == $ipRange[0] && $ipPass = true;
            }
        }

        if (!$ipPass) {
            $project = $user->getProject();
            Auth::logout();
            return Redirect::to('project/'.$project)->withErrors(array('limit'=>'您無法存取這個網站'));
        }
    }
});
