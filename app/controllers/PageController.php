<?php

class PageController extends BaseController {

    protected $layout = 'demo.layout-main';

    protected $project;

    public function __construct()
    {
        $this->beforeFilter(function($route){
            $this->project = Auth::user()->getProject();
        });
    }

    public function project($context = 'intro', $parameter = null)
    {
        $project = DB::table('projects')->where('code', $this->project)->first();
        $contents = View::make('demo.main', ['project' => $project])->nest('context','demo.' . $this->project . '.page.' . $context);

        $this->layout->content = $contents;

        View::share('parameter', $parameter);

        $response = Response::make($this->layout, 200);
        $response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Last-Modified', gmdate( 'D, d M Y H:i:s' ).' GMT');

        return $response;
    }

    public function page($context)
    {
        $project = DB::table('projects')->where('code', $this->project)->first();
        $contents = View::make('demo.main', ['project' => $project])->nest('context','demo.page.' . $context);

        $this->layout->content = $contents;

        $response = Response::make($this->layout, 200);
        $response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Last-Modified', gmdate( 'D, d M Y H:i:s' ).' GMT');

        return $response;
    }

}
