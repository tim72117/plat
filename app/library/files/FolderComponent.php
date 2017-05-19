<?php
namespace Plat\Files;

use User;
use Files;
use Input;
use ShareFile;
use Struct_file;
use Plat\Files\FolderComponent;

class FolderComponent extends CommFile {

    /**
     * Create a new FolderComponent.
     *
     * @param  Files  $file
     * @param  User  $user
     * @return void
     */
    function __construct(Files $file, User $user)
    {
        parent::__construct($file, $user);
    }

    public function is_full()
    {
        return false;
    }

    public function get_views()
    {
        return ['open'];
    }

    public function create()
    {
        parent::create();
    }

    public function open()
    {
        return 'files.folder.files';
    }

    public function docs()
    {
        $docs = ShareFile::with(['isFile', 'isFile.isType', 'shareds', 'requesteds'])->where(function($query) {

            $query->where(function($query) {

                $query->where('target', 'user')->where('target_id', $this->user->id);

            })->orWhere(function($query) {

                $inGroups = $this->user->inGroups->lists('id');

                count($inGroups)>0 && $query->where('target', 'group')->whereIn('target_id', $inGroups)->where('created_by', '!=', $this->user->id);

            });

        })->where('folder_id', $this->doc->id)->get()->map(function($doc) {

            return Struct_file::open($doc);

        })->toArray();

        $paths = $this->getPaths()->load('isFile');

        return ['docs' => $docs, 'paths' => $paths];
    }

    public function folders()
    {
        $folders = ShareFile::whereHas('isFile', function($query) {

            $query->where('type', 20);

        })->where(function($query) {

            $query->where(function($query) {

                $query->where('target', 'user')->where('target_id', $this->user->id);

            })->orWhere(function($query) {

                $inGroups = $this->user->inGroups->lists('id');

                count($inGroups)>0 && $query->where('target', 'group')->whereIn('target_id', $inGroups)->where('created_by', '!=', $this->user->id);

            });

        })->get()->load('isFile');

        return ['folders' => $folders];
    }

    public function createComponent()
    {
        if (!Input::has('fileInfo'))
            throw new ValidateException(new MessageBag(array('no_file_info' => '沒有輸入檔案資訊')));

        $file = $this->createFile(Input::get('fileInfo')['type'], Input::get('fileInfo')['title']);

        $class = $file->isType->class;

        $app = new $class($file, $this->user);

        $app->create();

        $doc = $this->createDoc($file);

        return ['doc' => Struct_file::open($doc)];
    }

    private function createFile($type_id, $title)
    {
        return new Files(['type' => $type_id, 'title' => $title]);
    }

    private function createDoc($file)
    {
        return ShareFile::updateOrCreate([
            'file_id'    => $file->id,
            'target'     => 'user',
            'target_id'  => $this->user->id,
            'created_by' => $this->user->id,
        ], [
            'folder_id'  => $this->doc->id,
            'visible' => false,
        ]);
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
                $group->users->load('members.organizations.now')->each(function ($user) use($shareds) {
                    $user->selected = in_array($user->id, $shareds['user']);
                });
            });
        }

        return ['groups' => $groups->toArray()];
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

    public function uploadFile()
    {
        if (!Input::hasFile('file_upload'))
            throw new ValidateException(new MessageBag(array('no_file_upload' => '檔案錯誤')));

        $file = $this->createFile(3, Input::file('file_upload')->getClientOriginalName());

        $class = $file->isType->class;

        $app = new $class($file, $this->user);

        $app->upload(Input::file('file_upload'));

        $doc = $this->createDoc($file);

        return ['doc' => Struct_file::open($doc)];
    }

}