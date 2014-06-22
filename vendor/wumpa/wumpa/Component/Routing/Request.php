<?php

namespace Wumpa\Component\Routing;

/**
 * Define a request.
 *
 * @author Bastien de Luca <dev@b-deluca.com>
 */

class Request {
	
	protected $url;
	protected $nodes = array();
	
	public function __construct($url, $nodes) {
		$this->setUrl($url);
		$this->setNodes($nodes);
	}
	
	public function getNodes() {
		return $this->nodes;
	}
	public function setNodes($nodes) {
		$this->nodes = $nodes;
		return $this;
	}
	
	public function getUrl() {
		return $this->url;
	}
	public function setUrl($url) {
		$this->url = $url;
		return $this;
	}

}