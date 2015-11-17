<?php

class ApiController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter(function($route){

        });
    }

    public function projects()
    {
        $projects = DB::table('projects')->get();
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

        $news = DB::table('news')->where('project', $project_id)->whereBetween('created_at', [$toDate, $fromDate])->select(['title', 'context', 'updated_at', 'created_at', 'deleted_at'])->get();
        return Response::json($news);
    }

}