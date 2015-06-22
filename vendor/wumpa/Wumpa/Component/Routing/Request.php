<?php

namespace Wumpa\Component\Routing;

/**
 * Define a request.
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class Request {

	private $method;
	private $host;
	private $uri;
	private $nodes = array();

	public function __construct() {
		$this->setMethod($_SERVER['REQUEST_METHOD']);
		$this->setHost($_SERVER['HTTP_HOST']);

		$requestURI = $_SERVER['REQUEST_URI'];
		$this->setUri($requestURI);

		if(!!$posGet = strpos($requestURI, '?'))
			$requestURI = substr_replace($requestURI, '', $posGet);
		$requestURI = rtrim($requestURI, '/');

		$requestURI = explode('/', $requestURI);
		$scriptName = explode('/', $_SERVER['SCRIPT_NAME']);

		for($i=0; $i<sizeof($scriptName) && isset($requestURI[$i])  && $requestURI[$i] == $scriptName[$i] ; $i++);

		$nodes = (empty(array_values(array_slice($requestURI,$i))) ? array('') : array_slice($requestURI,$i));
		$this->setNodes($nodes);
	}

	public function getFullUrl() {
		return 'http://'.$this->getHost().$this->getUri();
	}


	public function getMethod() {
		return $this->method;
	}

	public function setMethod($method) {
		$this->method = $method;
		return $this;
	}

	public function getHost() {
		return $this->host;
	}

	public function setHost($host) {
		$this->host = $host;
		return $this;
	}

	public function getUri() {
		return $this->uri;
	}

	public function setUri($uri) {
		$this->uri = $uri;
		return $this;
	}

	public function getNodes() {
		return $this->nodes;
	}

	public function setNodes($nodes) {
		$this->nodes = $nodes;
		return $this;
	}

}
