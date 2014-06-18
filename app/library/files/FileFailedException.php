<?php
namespace app\library\files\v0;
use Exception;

class FileFailedException extends Exception {
	public $fileFailedMessage;
	public function __construct($fileFailedMessage = Null) {
		$this->fileFailedMessage = $fileFailedMessage;
	}	
}