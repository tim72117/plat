<?php
namespace app\library\files\v0;
use Exception;

class UploadFailedException extends Exception {
	public $uploadFailedMessage;
	public function __construct($uploadFailedMessage = Null) {
		$this->uploadFailedMessage = $uploadFailedMessage;
	}	
}