<?php

use app\library\files\v0\CommFile;

class FileController extends BaseController {

    protected $layout = 'demo.layout-main';
	
	public function __construct()
    {
		$this->beforeFilter(function($route) {
            $this->user = Auth::user();

            Event::fire('ques.open', array());
		});
	}

    public function request($request_id, $method = null)
    {
        $doc_id = RequestFile::find($request_id)->doc_id;

        return $this->open($doc_id, $method);
    }

    public function open($doc_id, $method = null)
    {
        $this->doc = ShareFile::find($doc_id);
        if (!isset($this->doc)) {
            return $this->no();
        }

        $inGroups = $this->user->inGroups->lists('id');
        if (
            ($this->doc->target=='user' && $this->doc->target_id!=$this->user->id) ||
            ($this->doc->target=='group' && !in_array($this->doc->target_id, $inGroups))
        ) {
            return $this->deny();
        }

        $class = 'app\\library\\files\\v0\\' . $this->doc->isFile->isType->class;

        $this->file = new $class($this->doc->isFile, $this->user);

        $this->file->setDoc($this->doc);

        if (in_array($method, $this->file->get_views())) {
            if ($this->file->is_full())
                return View::make($this->file->$method());

            $view = View::make('demo.use.main')->nest('context', $this->file->$method());
        
            return $this->createView($view);
        }
        
        return $this->file->$method();
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
            $query->where('disabled', false)->where('active', true);
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
            $query->where('disabled', false)->where('active', true);
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
        $file = new Files([
            'type'       => $type_id,
            'title'      => $title,
            'created_by' => $this->user->id,
        ]);

        $type = DB::table('files_type')->where('id', $type_id)->first();

        $class = 'app\\library\\files\\v0\\' . $type->class;

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

}