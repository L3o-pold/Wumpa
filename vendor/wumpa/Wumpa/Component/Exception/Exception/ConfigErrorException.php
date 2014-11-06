<?php

namespace Wumpa\Component\Exception\Exception;

class ConfigErrorException extends \Exception {
	
	public function __construct($message, $code = null, $previous = null) {
		parent::__construct($message, $code, $previous);
	}
	
}