<?php

namespace Wumpa\Component\Controller;

use Wumpa\Component\App\App;

class Controller {
	
	private $templateDir;
	
	public function __construct() {
		$this->setTemplateDir(App::get(App::TEMPLATES));
	}
	
	public function getTemplateDir() {
		return $this->templateDir;
	}
	public function setTemplateDir($templateDir) {
		$this->templateDir = $templateDir;
		return $this;
	}
	
	public function render($template, $data) {
		\Twig_Autoloader::register();
		$loader = new \Twig_Loader_Filesystem($this->getTemplateDir());
		$twig = new \Twig_Environment($loader);
		echo $twig->render($template, $data);
	}
	
}