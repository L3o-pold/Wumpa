<?php

namespace Wumpa\Component\Exception\Exception;

class InvalidArgumentException extends \Exception {
	
	private $args;
	private $method;
	private $class;
	
	public function __construct($args, $method, $class, $message = "Provided Parameter is invalid", $code = null, $previous = null) {
		$this->setArgs($args);
		$this->setMethod($method);
		$this->setClass($class);
		parent::__construct($message, $code, $previous);
	}
	
	public function getArgs() {
		return $this->args;
	}
	public function setArgs($args) {
		$this->args = $args;
		return $this;
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