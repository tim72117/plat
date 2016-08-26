<?php

class ApiController extends BaseController {

    public $storage_path;

    public function __construct()
    {
        $this->beforeFilter(function($route){

        });
        $this->storage_path = storage_path() . '\file_upload';
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

        $posts = Plat\Project::find($project_id)->posts()->whereBetween('created_at', [$toDate, $fromDate])->select(['id','title', 'context', 'publish_at','display_at'])->get()->load('files');

        return Response::json($posts);
    }

    public function postFileDownload($id)
    {
        $postFile = Plat\Project\PostFile::find($id)->file;
        return Response::download($this->storage_path.'/'.$postFile->file,$postFile->title);
    }

}