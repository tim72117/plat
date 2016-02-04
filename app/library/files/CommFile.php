<?php
namespace Plat\Files;

use User;
use Files;
use Input, DB, Response, Validator, Session;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CommFile {

    protected $doc;

    public $storage_path;

    public function __construct(Files $file, User $user)
    {
        if (is_null($file))
            throw new FileFailedException;

        $this->file = $file;

        $this->user = $user;

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
            $this->file->title = Input::get('title');

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
            $this->doc->visible = Input::get('visible');
            $this->doc->save();
        }

        return ['doc' => $this->doc];
    }

    public function shareTo()
    {
        foreach (Input::get('groups') as $group) {
            if (count($group['users']) == 0 && $this->user->groups->contains($group['id'])) {
                \ShareFile::updateOrCreate([
                    'file_id' => $this->doc->file_id,
                    'target' => 'group',
                    'target_id' => $group['id'],
                    'created_by' => $this->user->id
                ]);
            }
            if (count($group['users']) != 0){
                foreach ($group['users'] as $user) {
                    \ShareFile::updateOrCreate([
                        'file_id' => $this->doc->file_id,
                        'target' => 'user',
                        'target_id' => $user['id'],
                        'created_by' => $this->user->id
                    ]);
                }
            }
        }

        return ['doc' => \Struct_file::open($this->doc)];
    }

    public function requestTo()
    {
        foreach (Input::get('groups') as $group) {
            if (count($group['users']) == 0 && $this->user->groups->contains($group['id'])) {
                \RequestFile::updateOrCreate([
                    'doc_id' => $this->doc->id,
                    'target' => 'group',
                    'target_id' => $group['id'],
                    'created_by' => $this->user->id,
                ], [
                    'disabled' => false,
                    'description' => Input::get('description'),
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
                        'description' => Input::get('description'),
                    ]);
                }
            }
        }

        return ['doc' => \Struct_file::open($this->doc)];
    }

}
