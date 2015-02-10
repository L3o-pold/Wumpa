<?php

namespace Wumpa\Component\Renderer;

/**
 * Provide a simple way to quickly render file from a Twig template.
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class Renderer {

	private $dir;
	private $twig;

	public function render($template, $data = array()) {
		return $this->getTwig()->render($template, $data);
	}

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

	public function getTwig() {
		return $this->twig;
	}

	public function setTwig($twig) {
		$this->twig = $twig;
		return $this;
	}

}
