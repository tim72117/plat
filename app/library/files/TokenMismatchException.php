<?php

namespace Plat\Files;

use Exception;

class TokenMismatchException extends Exception {
	public $validator;
	public function __construct($validator = Null) {
		$this->validator = $validator;
	}	
}