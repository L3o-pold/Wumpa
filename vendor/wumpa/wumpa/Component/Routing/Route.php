<?php

namespace Wumpa\Component\Routing;

class Route {
	
	protected $name;
	protected $path;
	protected $controller;
	protected $requirements;
	
	public function __construct($name, $path, $controller, $requirements = null) {
		$this->setName($name);
		$this->setPath($path);
		$this->setController($controller);
		$this->setRequirements($requirements);
	}
	
	public function getName() {
		return $this->name;
	}
	public function setName($name) {
		$this->name = $name;
		return $this;
	}
	public function getPath() {
		return $this->path;
	}
	public function setPath($path) {
		$this->path = $path;
		return $this;
	}
	public function getController() {
		return $this->controller;
	}
	public function setController($controller) {
		$this->controller = $controller;
		return $this;
	}
	public function getRequirements() {
		return $this->requirements;
	}
	public function setRequirements($requirements) {
		$this->requirements = $requirements;
		return $this;
	}

}