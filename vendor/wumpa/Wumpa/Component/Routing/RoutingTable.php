<?php

namespace Wumpa\Component\Routing;

class RoutingTable implements \Iterator {
	
	private $routes = array();
	
	public function getRoutes() {
		return $this->routes;
	}
	public function setRoutes($routes) {
		$this->routes = $routes;
		return $this;
	}
	
	function rewind() {
		$this->position = 0;
	}
	
	function current() {
		return $this->routes[$this->position];
	}
	
	function key() {
		return $this->position;
	}
	
	function next() {
		++$this->position;
	}
	
	function valid() {
		return isset($this->routes[$this->position]);
	}
	
	public function add($route) {
		foreach ($this->getRoutes() as $r) {
			if($r->getName() == $route->getName()) {
				return false;
			}
		}
		$this->routes[] = $route;
	}
	
	public function remove($routeName) {
		foreach ($this->getRoutes() as $key => $r) {
			if($r->getName() == $routeName) {
				unset($this->routes[$key]);
				return true;
			}
		}
		return false;
	}
	
	public function merge($routingTable, $overwrite = true) {
		foreach ($this->getRoutes() as $key => $r) {
			foreach ($routingTable as $route) {
				
			}
		}
	}
	
}