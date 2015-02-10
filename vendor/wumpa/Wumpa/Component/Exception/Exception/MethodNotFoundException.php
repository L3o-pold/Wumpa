<?php

namespace Wumpa\Component\Exception\Exception;

/**
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class MethodNotFoundException extends \Exception {

	private $method;
	private $class;

	public function __construct($method, $class, $message, $code = null, $previous = null) {
		parent::__construct($message, $code, $previous);
	}

	public function getMethod() {
		return $this->method;
	}

	public function setMethod($method) {
		$this->method = $method;
		return $this;
	}

	public function getClass() {
		return $this->class;
	}
	
	public function setClass($class) {
		$this->class = $class;
		return $this;
	}

}
