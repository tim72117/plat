<?php

class OfflineController extends BaseController {

    protected $layout = 'project.layout-main';

    protected $project;

    public function __construct()
    {
        $this->beforeFilter(function($route){
            $this->project = Auth::user()->getProject();
        });
    }

    public function project($context = 'intro')
    {
        $contents = View::make('project.main')->nest('context','project.' . $this->project . '.page.' . $context);

        $this->layout->content = $contents;

        $response = Response::make($this->layout, 200);
        $response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Last-Modified', gmdate( 'D, d M Y H:i:s' ).' GMT');

        return $response;
    }

    public function page($context)
    {
        $contents = View::make('project.main')->nest('context','project.page.' . $context);

        $this->layout->content = $contents;

        $response = Response::make($this->layout, 200);
        $response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Last-Modified', gmdate( 'D, d M Y H:i:s' ).' GMT');

        return $response;
    }

    public function offline($context)
    {
        if( $context == 'template_demo' )
            return View::make('editor.question_demo');

        if( $context == 'interviewer' )
            return View::make('project.cdb.page.offline_interviewer');

        if( $context == 'cache_manifest' )
            return Response::view('nopage', array(), 404);

        return View::make('project.cdb.page.cache_manifest');
    }

}