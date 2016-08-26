<?php

namespace Plat\Files;

use Exception;

class RowsImportException extends Exception {

    public $validator;

    public function __construct($messages = []) {

        $this->messages = $messages;

    }

}