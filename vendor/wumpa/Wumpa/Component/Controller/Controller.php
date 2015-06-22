<?php

namespace Wumpa\Component\Controller;

use Wumpa\Component\App\App;
use Wumpa\Component\TwigExtension\RouterTwigExtension;
use Wumpa\Component\TwigExtension\RegexTwigExtension;

/**
 * Provide features for user controllers. It use Twig to render view.
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class Controller {

	private $twig;

	public function render($template, $data = array()) {
		echo $this->getTwig()->render($template, $data);
	}

	public function __construct() {
		$loader = new \Twig_Loader_Filesystem(App::get()->getTemplatesDir());
		$twig = new \Twig_Environment($loader);
		$twig->addExtension(new RouterTwigExtension());
		$twig->addExtension(new RegexTwigExtension());
		$this->setTwig($twig);
	}

	public function getTwig() {
		return $this->twig;
	}

	public function setTwig($twig) {
		$this->twig = $twig;
		return $this;
	}

}
