<?php

namespace Wumpa\Component\Controller;

use Wumpa\Component\App\App;

/**
 * Provide feature for user controllers. It use Twig to render view.
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class Controller {

	private $templateDir;
	private $url;

	public function render($template, $data = array()) {
		$data['~url'] = $this->getUrl();
		$loader = new \Twig_Loader_Filesystem($this->getTemplateDir());
		$twig = new \Twig_Environment($loader);
		echo $twig->render($template, $data);
	}

	public function __construct() {
		$this->setTemplateDir(App::get()->getTemplatesDir());
		$this->setUrl(App::get()->getUrl());
	}

	public function getTemplateDir() {
		return $this->templateDir;
	}

	public function setTemplateDir($templateDir) {
		$this->templateDir = $templateDir;
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
