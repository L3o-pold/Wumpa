<?php

namespace Wumpa\Component\Renderer;

class Renderer {
	
	private $dir;
	private $twig;
	
	public function __construct($dir) {
		$this->setDir($dir);
		$loader = new \Twig_Loader_Filesystem($dir);
		$this->setTwig(new \Twig_Environment($loader));
	}
	
	public function getDir() {
		return $this->dir;
	}
	public function setDir($dir) {
		$this->dir = $dir;
		return $this;
	}
	/*
	public function getLoader() {
		return $this->loader;
	}
	public function setLoader($loader) {
		$this->loader = $loader;
		return $this;
	}
	*/
	public function getTwig() {
		return $this->twig;
	}
	public function setTwig($twig) {
		$this->twig = $twig;
		return $this;
	}
	
	public function render($template, $data = array()) {
		return $this->getTwig()->render($template, $data);
	}
	
}