<?php

namespace Plat\Files;

use User;
use Files;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Config\FileLoader;
use Illuminate\Config\Repository;

class CustomFile extends CommFile {

    function __construct(Files $file, User $user)
    {
        parent::__construct($file, $user);

        $fileLoader = new FileLoader(new Filesystem, app_path() . '/views');

        $this->module = new Repository($fileLoader, '');
    }

    public function get_views()
    {
        return ['open'];
    }

    public function open()
    {
        $func = $this->module->get($this->file->file . '.open');

        return call_user_func($func);
    }

    public function is_full()
    {
        return false;
    }

    public function __call($method, $args)
    {
        $func = $this->module->get($this->file->file . '.' . $method);

        if (is_callable($func)) {
            return call_user_func($func);
        }
    }
}
