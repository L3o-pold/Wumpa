<?php

namespace Wumpa\Component\Controller;

use Wumpa\Component\App\App;

/**
 * Provide features for user controllers. It use Twig to render view.
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class Controller {

	private $templateDir;
	private $url;
	private $twig;

	public function render($template, $data = array()) {
		$data['_url'] = $this->getUrl();
		echo $this->getTwig()->render($template, $data);
	}

	public function __construct() {
		$this->setTemplateDir(App::get()->getTemplatesDir());
		$this->setUrl(App::get()->getUrl());
		$loader = new \Twig_Loader_Filesystem($this->getTemplateDir());
		$this->setTwig(new \Twig_Environment($loader));
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

	public function getTwig() {
		return $this->twig;
	}

	public function setTwig($twig) {
		$this->twig = $twig;
		return $this;
	}

}
