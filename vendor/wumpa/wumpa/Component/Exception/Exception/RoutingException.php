<?php

namespace Wumpa\Component\Exception\Exception;

class RoutingException extends \Exception {

	private $request;

	public function __construct($request, $message, $code = null, $previous = null) {
		$this->setRequest($request);
		parent::__construct($message, $code, $previous);
	}

	public function getRequest() {
		return $this->request;
	}
	public function setRequest($request) {
		$this->request = $request;
		return $this;
	}

}
