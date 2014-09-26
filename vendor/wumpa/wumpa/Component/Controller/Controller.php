<?php

namespace Wumpa\Component\Controller;

use Wumpa\Component\App\App;

class Controller {
	
	private $templateDir;
	private $asset;
	
	public function __construct() {
		$this->setTemplateDir(App::get(App::TEMPLATES));
		$this->setAsset(App::get(App::URL));
	}
	
	public function getTemplateDir() {
		return $this->templateDir;
	}
	public function setTemplateDir($templateDir) {
		$this->templateDir = $templateDir;
		return $this;
	}
	
	public function getAsset() {
		return $this->asset;
	}
	public function setAsset($asset) {
		$this->asset = $asset;
		return $this;
	}
	
	public function render($template, $data = array()) {
		$data['asset'] = $this->getAsset();
		\Twig_Autoloader::register();
		$loader = new \Twig_Loader_Filesystem($this->getTemplateDir());
		$twig = new \Twig_Environment($loader);
		echo $twig->render($template, $data);
	}
	
}