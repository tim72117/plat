<?php

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

    app_path().'/commands',
    app_path().'/controllers',
    app_path().'/models',
    app_path().'/database/seeds',

));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

Log::useFiles(storage_path().'/logs/laravel.log');

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(function(Exception $exception, $code)
{
    Log::error($exception);
});

App::error(function(Plat\Files\UploadFailedException $exception) {
    return Response::json($exception->uploadFailedMessage);
});

App::error(function(Plat\Files\FileFailedException $exception) {
    //return Response::view('noFile', array(), 404)->header('Refresh','5;url='.URL::to('page/project'));
});

App::error(function(Plat\Files\ValidateException $exception) {
    if (Request::ajax()) {
        return Response::json(['errors' => $exception->validator->errors()->all()]);
    } else {
        return Redirect::back()->withErrors($exception->validator)->withInput();
    }
});

App::error(function(Plat\Files\TokenMismatchException $exception) {
    return Redirect::back()->withErrors($exception->validator)->withInput(Input::except('_token','_token2'));
});

use Illuminate\Database\Eloquent\ModelNotFoundException;
App::error(function(ModelNotFoundException $e)
{
    return Response::make('Not Found', 404);
});

App::error(function(PDOException $exception) {
    //return Response::view('nopage', array(), 404);
});

App::error(function(Exception $exception)
{
    //return Response::view('nopage', array(), 404);
});

App::missing(function($exception) {
    //return Response::view('nopage', array(), 404);
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(function()
{
    return Response::make(View::make('maintenance'), 503);//View::make("maintenance", 503);
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require app_path().'/filters.php';

Event::listen('ques.open', function()
{
    $log = QuestionXML\Log::updateOrCreate(['session' => Session::getId()], ['host' => gethostname()]);

    $log->touch();
});

//DB::connection()->disableQueryLog();