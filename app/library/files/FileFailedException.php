<?php

namespace Plat\Files;

use Exception;

class FileFailedException extends Exception {
	public $fileFailedMessage;
	public function __construct($fileFailedMessage = Null) {
		$this->fileFailedMessage = $fileFailedMessage;
	}	
}