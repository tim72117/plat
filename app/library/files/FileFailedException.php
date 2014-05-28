<?php namespace app\library\files\v0;

class FileFailedException extends \Exception {
	public $fileFailedMessage;
	public function __construct($fileFailedMessage) {
		$this->fileFailedMessage = $fileFailedMessage;
	}	
}