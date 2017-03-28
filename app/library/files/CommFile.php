<?php
namespace Plat\Files;

use Illuminate\Http\Request;
use App\User;
use Files;
use Input, DB, Response, Validator, Session;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CommFile {

    protected $doc;

    public $storage_path;

    public function __construct(Files $file, User $user, Request $request)
    {
        if (is_null($file))
            throw new FileFailedException;

        $this->file = $file;

        $this->user = $user;

        $this->request = $request;

        $this->storage_path = storage_path() . '/file_upload';
    }

    public function get_views()
    {
        return [];
    }

    public static function get_tools()
    {
        return [];
    }

    public function open()
    {
        return $this->download();
    }

    public function delete()
    {
        $deleted = $this->doc->target == 'user' ? $this->doc->delete() : false;

        return ['deleted' => $deleted];
    }

    public function rename()
    {
        if ($this->isCreater()) {
            $this->file->title = $this->request->get('title');

            $this->doc->touch();

            $this->doc->push();

            return ['doc' => \Struct_file::open($this->doc)];
        }
    }

    public function create()
    {
        $this->file->created_by = $this->user->id;

        $this->file->save();
    }

    public function upload(UploadedFile $file_upload, $visible = true)
    {
        $validator =  Validator::make(
            array('file_upload'     => $file_upload),
            array('file_upload'     => 'required|max:8000000'),
            array('file_upload.max' => '檔案太大')
        );

        if ($validator->fails())
            throw new ValidateException($validator);

        $hash_name = hash_file('md5', $file_upload->getRealPath()) . '-' . uniqid(rand(0, 99));

        $parts = array_slice(str_split($hash = md5($this->user->id), 2), 0, 2);

        $this->file->file = join('/', $parts) . '/' . $hash_name;

        $this->move($file_upload, $this->storage_path . '/' . $this->file->file);

        $this->create();
    }

    public function move(UploadedFile $file_upload, $path)
    {
        try
        {
            $filesystem = new Filesystem();

            $filesystem->makeDirectory(dirname($path), 0777, true, true);

            $file_upload->move(dirname($path), basename($path));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function download()
    {
        $file_path = $this->storage_path . '/' . $this->file->file;

        if (!file_exists($file_path))
            throw new FileFailedException;

        return Response::download($file_path, $this->file->title);
    }

    private function decodeInput($input)
    {
        return json_decode(urldecode(base64_decode($input)));
    }

    public function id()
    {
        return $this->file->id;
    }

    public function setDoc($doc)
    {
        $this->doc = $doc;
    }

    public function isCreater()
    {
        return isset($this->doc) && $this->doc->created_by == $this->user->id;
    }

    public function setVisible()
    {
        if ($this->doc->target == 'user') {
            $this->doc->visible = $this->request->get('visible');
            $this->doc->save();
        }

        return ['doc' => $this->doc];
    }

    public function shareTo()
    {
        foreach ($this->request->get('groups') as $group) {
            if (count($group['users']) == 0 && $this->user->groups->contains($group['id'])) {
                \ShareFile::updateOrCreate([
                    'file_id' => $this->doc->file_id,
                    'target' => 'group',
                    'target_id' => $group['id'],
                    'created_by' => $this->user->id
                ], [
                    'visible' => true,
                    'folder_id' => $this->getPaths()->first()->id,
                ]);
            }
            if (count($group['users']) != 0){
                foreach ($group['users'] as $user) {
                    \ShareFile::updateOrCreate([
                        'file_id' => $this->doc->file_id,
                        'target' => 'user',
                        'target_id' => $user['id'],
                        'created_by' => $this->user->id
                    ], [
                        'visible' => true,
                        'folder_id' => $this->getPaths()->first()->id,
                    ]);
                }
            }
        }

        return ['doc' => \Struct_file::open($this->doc)];
    }

    public function requestTo()
    {
        foreach ($this->request->get('groups') as $group) {
            if (count($group['users']) == 0 && $this->user->groups->contains($group['id'])) {
                \RequestFile::updateOrCreate([
                    'doc_id' => $this->doc->id,
                    'target' => 'group',
                    'target_id' => $group['id'],
                    'created_by' => $this->user->id,
                ], [
                    'disabled' => false,
                    'description' => $this->request->get('description'),
                ]);
            }
            if (count($group['users']) != 0){
                foreach ($group['users'] as $user) {
                    \RequestFile::updateOrCreate([
                        'doc_id' => $this->doc->id,
                        'target' => 'user',
                        'target_id' => $user['id'],
                        'created_by' => $this->user->id,
                    ], [
                        'disabled' => false,
                        'description' => $this->request->get('description'),
                    ]);
                }
            }
        }

        return ['doc' => \Struct_file::open($this->doc)];
    }

    public function saveAs()
    {
        $file = $this->file->replicate();
        $file->title = $file->title . '(副本)';
        $file->save();
        $doc = $this->doc->replicate();
        $doc->file_id = $file->id;
        $doc->save();
        return $doc;
    }

    public function moveToFolder()
    {
        $this->doc->folder_id = $this->request->get('folder_id');

        return ['moved' => $this->doc->save()];
    }

    public function getPaths()
    {
        if ($this->doc->folder) {

            $folder = new FolderComponent($this->doc->folder->isFile, $this->user, $this->request);

            $folder->setDoc($this->doc->folder);

            $paths = $folder->getPaths()->add($this->doc);

        } else {

            $paths = \Illuminate\Database\Eloquent\Collection::make([$this->doc]);

        }

        return $paths;
    }

}
