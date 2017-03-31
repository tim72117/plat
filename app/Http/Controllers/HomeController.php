<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use View;
use ShareFile;
use Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'member']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function project()
    {
        $member = Auth::user()->members()->orderBy('logined_at', 'desc')->first();

        View::share('project', $member->project);
        View::share('paths', [ShareFile::whereNull('folder_id')->first()]);

        return View::make('project.main')->nest('context', 'project.intro');
    }
}
