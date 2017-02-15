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

        $this->configs = $this->file->configs->lists('value', 'name');

        $class = $this->file->isType->modules()->find($this->configs['module'])->class;

        $this->module = new $class();
    }

    public function get_views()
    {
        return ['open'];
    }

    public function open()
    {
        return $this->module->open();
    }

    public function is_full()
    {
        return $this->module->full;
    }

    public function __call($method, $args)
    {
        return $this->module->$method();
    }
}
