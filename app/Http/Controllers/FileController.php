<?php

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileController extends Controller {

    public function __construct()
    {
        $this->user = Auth::user();

        $this->layout = View::make('project.layout-main');

        Event::fire('ques.open', array());
    }

    public function management()
    {
        return Redirect::to('doc/3171/open');
    }

    public function project()
    {
        View::share('paths', [ShareFile::whereNull('folder_id')->first()]);
        return $this->createView(View::make('project.main')->nest('context', 'project.intro1'));
    }

    public function apps()
    {
        $apps = ShareFile::with(['isFile', 'isFile.isType', 'isFile.tags'])->where(function($query) {

            $query->where(function($query) {
                $query->where('target', 'user')->where('target_id', $this->user->id);
            })->orWhere(function($query) {
                $inGroups = $this->user->inGroups->lists('id');
                $query->where('target', 'group')->whereIn('target_id', $inGroups);
            });

        })->where('visible', true)->get()->sortBy(function($app) {

            $rank_first = $app->file_id == 3 ? 0 : 1;
            $rank_last = $app->target == 'user' ? 0 : 1;
            return $rank_first . $app->isFile->title . $rank_last;

        })->groupBy('file_id')->map(function($app) {

            return [
                'title' => $app[0]->isFile->title,
                'link'  => 'doc/' . $app[0]->id . '/open',
                'tags'  => $app[0]->isFile->tags,
            ];

        })->toArray();

        $requests = RequestFile::has('isDoc')->where(function($query) {

            $query->where(function($query) {
                $query->where('target', 'user')->where('target_id', $this->user->id);
            })->orWhere(function($query) {
                $inGroups = $this->user->inGroups->lists('id');
                $query->where('target', 'group')->whereIn('target_id', $inGroups);
            });

        })->where('disabled', false)->get()->map(function($request) {

            return [
                'title' => $request->description,
                'link'  => 'request/' . $request->id . '/import',
                'created_at' => $request->created_at->toIso8601String(),
            ];

        })->toArray();

        return ['apps' => $apps, 'requests' => $requests];
    }

    public function request($request_id, $method = null)
    {
        $request = RequestFile::find($request_id);

        $doc = ShareFile::find($request->doc_id);
        if (!isset($doc)) {
            return $this->no();
        }

        View::share('request', $request);

        return $this->active($doc, $method);
    }

    public function open($doc_id, $method = null)
    {
        $doc = ShareFile::find($doc_id);
        if (!isset($doc)) {
            return $this->no();
        }

        $inGroups = $this->user->inGroups->lists('id');
        if (
            ($doc->target == 'user' && $doc->target_id != $this->user->id) ||
            ($doc->target == 'group' && !in_array($doc->target_id, $inGroups))
        ) {
            if (!($doc->target == 'user' && $doc->target_id == 0))
            return $this->deny();
        }

        $doc->opened_at = Carbon\Carbon::now()->toDateTimeString();
        $doc->save();

        return $this->active($doc, $method);
    }

    private function active($doc, $method)
    {
        $class = $doc->isFile->isType->class;

        $file = new $class($doc->isFile, $this->user);

        $file->setDoc($doc);

        if (in_array($method, $file->get_views())) {
            if ($file->is_full()) {
                $member = $this->user->members()->logined()->orderBy('logined_at', 'desc')->first();
                View::share('project', $member->project);
                $view = View::make($file->$method(), ['doc' => $doc]);
            } else {
                View::share('paths', $file->getPaths()->load('isFile'));
                $context = View::make('project.main', ['doc' => $doc])->nest('context', $file->$method());
                $view = $this->createView($context);
            }
        } else {
            $view = $file->$method();
        }

        return $view;
    }

    private function createView($view)
    {
        $this->layout->content = $view;

        $member = $this->user->members()->logined()->orderBy('logined_at', 'desc')->first();

        View::share('project', $member->project);

        $response = Response::make($this->layout, 200);
        $response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Last-Modified', gmdate( 'D, d M Y H:i:s' ).' GMT');

        return $response;
    }

    private function no()
    {
        return Response::view('noFile', array(), 404);
    }

    private function deny()
    {
        return Response::view('timeout', array(), 404);
    }

    private function showQuery()
    {
        $queries = DB::getQueryLog();
        foreach ($queries as $query) {
            var_dump($query);echo '<br /><br />';
        }
    }

}
