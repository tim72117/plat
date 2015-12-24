<?php

namespace Plat\Files;

use Exception;

class UploadFailedException extends Exception {
	public $uploadFailedMessage;
	public function __construct($uploadFailedMessage = Null) {
		$this->uploadFailedMessage = $uploadFailedMessage;
	}	
}