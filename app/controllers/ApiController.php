<?php

class ApiController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter(function($route){

        });
    }

    public function projects()
    {
        $projects = Plat\Project::all();

        return Response::json($projects);
    }

    public function news($project_id, $to, $from = 'now')
    {
        try {
            $toDate = Carbon\Carbon::parse($to)->toDateTimeString();
            $fromDate = Carbon\Carbon::parse($from)->toDateTimeString();
        } catch (Exception $e) {
            return Response::json(['error' => $e->getMessage()]);
        }

        $posts = Plat\Project::find($project_id)->posts()->whereBetween('created_at', [$toDate, $fromDate])->select(['title', 'context', 'publish_at'])->get();

        return Response::json($posts);
    }

}