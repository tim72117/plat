<?php
namespace app\library\files\v0;

use User;
use Files;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Config\FileLoader;
use Illuminate\Config\Repository;

class CustomFile extends CommFile {

    function __construct(Files $file, User $user)
    {
        parent::__construct($file, $user);
    }

    public function get_views()
    {
        return ['open'];
    }

    public function open()
    {
        return $this->file->file;
    }

    public function is_full()
    {
        $information = json_decode($this->file->information);

        return isset($information->full) && $information->full;
    }

    public function __call($method, $args)
    {
        $fileLoader = new FileLoader(new Filesystem, app_path() . '/views/demo');

        $ajax = new Repository($fileLoader, '');

        $func = $ajax->get($this->file->controller . '.' . $method);

        if (is_callable($func)) {
            return call_user_func($func);
        }
    }
}
