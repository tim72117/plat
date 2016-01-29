<?php

class FileController extends BaseController {

    protected $layout = 'project.layout-main';

    public function __construct()
    {
        $this->user = Auth::user();

        Event::fire('ques.open', array());
    }

    public function docs()
    {
        $apps = ShareFile::with(['isFile', 'isFile.isType'])->where(function($query) {

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
            ];

        })->toArray();

        $requests = RequestFile::where(function($query) {

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
            ];

        })->toArray();

        return ['apps' => $apps, 'requests' => $requests];
    }

    public function request($request_id, $method = null)
    {
        $doc_id = RequestFile::find($request_id)->doc_id;

        $doc = ShareFile::find($doc_id);
        if (!isset($doc)) {
            return $this->no();
        }

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
            return $this->deny();
        }

        return $this->active($doc, $method);
    }

    private function active($doc, $method)
    {
        $class = 'Plat\\Files\\' . $doc->isFile->isType->class;

        $file = new $class($doc->isFile, $this->user);

        $file->setDoc($doc);

        if (in_array($method, $file->get_views())) {
            if ($file->is_full()) {
                $view = View::make($file->$method());
            } else {
                $member = $this->user->members()->logined()->orderBy('logined_at', 'desc')->first();
                $context = View::make('project.main', ['project' => $member->project])->nest('context', $file->$method());
                $view = $this->createView($context);
            }
        } else {
            $view = $file->$method();
        }

        return $view;
    }

    public function upload()
    {
        if (!Input::hasFile('file_upload'))
            throw new ValidateException(new MessageBag(array('no_file_upload' => '檔案錯誤')));

        $file = $this->createFile(3, Input::file('file_upload')->getClientOriginalName());

        $file->upload(Input::file('file_upload'));

        $doc = $this->createDoc($file);

        return ['doc' => Struct_file::open($doc)];
    }

    public function create()
    {
        if (!Input::has('fileInfo'))
            throw new ValidateException(new MessageBag(array('no_file_info' => '沒有輸入檔案資訊')));

        $file = $this->createFile(Input::get('fileInfo')['type'], Input::get('fileInfo')['title']);

        $file->create();

        $doc = $this->createDoc($file);

        return ['doc' => Struct_file::open($doc)];
    }

    public function shared()
    {
        $groups = $this->user->groups()->with(['users' => function ($query) {
            $query->where('disabled', false)->where('actived', true);
        }])->get();

        if (count(Input::get('docs'))==1) {
            $shareds = ShareFile::find(Input::get('docs')[0]['id'])->shareds->reduce(function ($carry, $item) {
                array_push($carry[$item->target], $item->target_id);
                return $carry;
            }, ['group' => [], 'user' => []]);

            $groups->each(function ($group) use($shareds) {
                $group->selected = in_array($group->id, $shareds['group']);
                $group->users->each(function ($user) use($shareds) {
                    $user->selected = in_array($user->id, $shareds['user']);
                });
            });
        }

        return ['groups' => $groups->toArray()];
    }

    public function shareTo()
    {
        $docs = ShareFile::where('created_by', $this->user->id)->get();
        $shareds = [];

        foreach (Input::get('docs') as $doc) {
            if (!$docs->contains($doc['id'])) {
                continue;
            }

            $doc = $docs->find($doc['id']);

            foreach (Input::get('groups') as $group) {
                if (count($group['users']) == 0 && $this->user->groups->contains($group['id'])) {
                    ShareFile::updateOrCreate(['file_id' => $doc->file_id, 'target' => 'group', 'target_id' => $group['id'], 'created_by' => $this->user->id]);
                }
                if (count($group['users']) != 0){
                    foreach ($group['users'] as $user) {
                        ShareFile::updateOrCreate(['file_id' => $doc->file_id, 'target' => 'user', 'target_id' => $user['id'], 'created_by' => $this->user->id]);
                    }
                }
            }
            array_push($shareds, Struct_file::open($doc));
        }

        return ['docs' => $shareds];
    }

    public function requested()
    {
        $groups = $this->user->groups()->with(['users' => function ($query) {
            $query->where('disabled', false)->where('actived', true);
        }])->get();

        if (count(Input::get('docs'))==1) {
            $requesteds = ShareFile::find(Input::get('docs')[0]['id'])->requesteds->reduce(function ($carry, $item) {
                array_push($carry[$item->target], $item->target_id);
                return $carry;
            }, ['group' => [], 'user' => []]);

            $groups->each(function ($group) use($requesteds) {
                $group->selected = in_array($group->id, $requesteds['group']);
                $group->users->each(function ($user) use($requesteds) {
                    $user->selected = in_array($user->id, $requesteds['user']);
                });
            });
        }

        return ['groups' => $groups->toArray()];
    }

    public function requestTo()
    {
        $docs = ShareFile::where('created_by', $this->user->id)->get();
        $requesteds = [];

        foreach (Input::get('docs') as $doc) {
            if (!$docs->contains($doc['id'])) {
                continue;
            }

            $doc = $docs->find($doc['id']);

            foreach (Input::get('groups') as $group) {
                if (count($group['users']) == 0 && $this->user->groups->contains($group['id'])) {
                    RequestFile::updateOrCreate(['doc_id' => $doc->id, 'target' => 'group', 'target_id' => $group['id'], 'created_by' => $this->user->id])
                    ->update(['description' => Input::get('description')]);
                }
                if (count($group['users']) != 0){
                    foreach ($group['users'] as $user) {
                        RequestFile::updateOrCreate(['doc_id' => $doc->id, 'target' => 'user', 'target_id' => $user['id'], 'created_by' => $this->user->id])
                        ->update(['description' => Input::get('description')]);
                    }
                }
            }
            array_push($requesteds, Struct_file::open($doc));
        }

        return ['docs' => $requesteds];
    }

    private function createFile($type_id, $title)
    {
        $file = new Files(['type' => $type_id, 'title' => $title]);

        $type = DB::table('files_type')->where('id', $type_id)->first();

        $class = 'Plat\\Files\\' . $type->class;

        return new $class($file, $this->user);
    }

    private function createDoc($file)
    {
        return ShareFile::updateOrCreate([
            'file_id'    => $file->id(),
            'target'     => 'user',
            'target_id'  => $this->user->id,
            'created_by' => $this->user->id,
        ]);
    }

    private function createView($view)
    {
        $this->layout->content = $view;

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

    public function page($context = 'intro', $parameter = null)
    {
        $member = $this->user->members()->logined()->orderBy('logined_at', 'desc')->first();

        $contents = View::make('project.main', ['project' => $member->project])->nest('context','project.' . $member->project->code . '.' . $context);

        $this->layout->content = $contents;

        View::share('parameter', $parameter);
        View::share('project', $member->project);

        $response = Response::make($this->layout, 200);
        $response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Last-Modified', gmdate( 'D, d M Y H:i:s' ).' GMT');

        return $response;
    }

    public function apps()
    {
        $docs = ShareFile::with(['isFile', 'isFile.isType', 'shareds', 'requesteds'])->where(function($query) {

            $query->where('target', 'user')->where('target_id', $this->user->id);

        })->orWhere(function($query) {

            $inGroups = $this->user->inGroups->lists('id');

            count($inGroups)>0 && $query->where('target', 'group')->whereIn('target_id', $inGroups)->where('created_by', '!=', $this->user->id);

        })->get()->map(function($doc) {

            return Struct_file::open($doc);

        })->toArray();

        $tooltip = [
            'start'  => ['startup' => true,  'position' => 'bottom center', 'html' => '<h2 class="ui header">有新的功能</h2>更新時間2016-01-29'],
            'rename' => ['startup' => false, 'position' => 'right center', 'html' => '<h2 class="ui header">點擊檔案名稱右邊的空白處，即可修改檔案名稱，修改完後再點擊一次空白處儲存變更。</h2><i class="info icon"></i>只有檔案的擁有人可以變更名稱'],
            'menu'   => ['startup' => false, 'position' => 'left center', 'html' => '<h2 class="ui header">勾選將這個檔案加到左邊快捷選單中。</h2><i class="info icon"></i>只有檔案的擁有人可以變更名稱'],
        ];

        return ['docs' => $docs, 'tooltip' => $tooltip];
    }

}
