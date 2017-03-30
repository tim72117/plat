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
        $this->middleware('auth');
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

        return $member && $member->actived ? View::make('project.main')->nest('context', 'project.intro') : redirect('/profile');
    }

    public function getProjects()
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

        return redirect('/project');
    }
}
