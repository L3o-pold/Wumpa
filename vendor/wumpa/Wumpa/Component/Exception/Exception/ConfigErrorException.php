<?php

namespace Wumpa\Component\Exception\Exception;

/**
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class ConfigErrorException extends \Exception {

	public function __construct($message, $code = null, $previous = null) {
		parent::__construct($message, $code, $previous);
	}

}
