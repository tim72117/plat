<?php
namespace Plat\Files;

use User;
use Files;

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

    public function create()
    {
        parent::create();
    }

}