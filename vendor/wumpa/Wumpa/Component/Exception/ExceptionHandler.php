<?php

namespace Wumpa\Component\Exception;

use Wumpa\Component\Exception\Exception\ConfigErrorException;
use Wumpa\Component\Exception\Exception\DirectoryNotFoundException;
use Wumpa\Component\Exception\Exception\FileNotFoundException;
use Wumpa\Component\Exception\Exception\IllegalMethodCall;
use Wumpa\Component\Exception\Exception\InvalidArgumentException;
use Wumpa\Component\Exception\Exception\MethodNotFoundException;
use Wumpa\Component\Exception\Exception\RoutingException;

/**
 * Provide an Exception handler that can display them in a more user friendly way
 * with stack trace.
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class ExceptionHandler {

	private $trace;

	public function register() {
		set_exception_handler(array($this, "handle"));
	}

	public function unregister() {
		set_exception_handler(null);
	}

	public function handle($e) {
		\Twig_Autoloader::register();
		$loader = new \Twig_Loader_Filesystem(__DIR__."/templates/");
		$twig = new \Twig_Environment($loader);

		$data = array(
			"code" => $e->getCode(),
			"message" => $e->getMessage(),
		);

		if($this->isTracing())
			$data["e"] = $e;

		if($e instanceof DirectoryNotFoundException) {
			$data["description"] = "The following directory could not be found on server: <em><b>" .$e->getDirectory(). "</b></em>.";
		} else if($e instanceof FileNotFoundException) {
			$data["description"] = "The following file could not be found on server: <em><b>" .$e->getFileNotFound(). "</b></em>.";
		} else if($e instanceof IllegalMethodCall) {
			$data["description"] = "The following Methode: <em><b>" .$e->getMethod(). "</b></em> in class <em><b>" .$e->getClass(). "</b></em> cannot be called.";
		} else if($e instanceof InvalidArgumentException) {
			$data["description"] = "The following argument(s): <em><b>";
			if($e->getArgs() != null) {
				foreach($e->getArgs() as $arg) {
					if(is_array($arg)) {
						$i = 0;
						$data["description"] .= "(";
						foreach($arg as $index => $value) {
							if($i != 0) {
								$data["description"] .= "[". $index ."] => ". $value .", ";
							} else {
								$data["description"] .= "[". $index ."] => ". $value;
							}

							$i++;
						}
						$data["description"] .= "), ";
					} else {
						$data["description"] .= $arg. ", ";
					}
				}
			} else {
				$data["description"] .= "null, ";
			}
			$data["description"] .= "</b></em>are invalid for the method <em><b>" .$e->getMethod(). "</b></em> in class <em><b>" .$e->getClass(). "</b></em>.";
		} else if($e instanceof MethodNotFoundException) {
			$data["description"] = "The following Methode: <em><b>" .$e->getMethod(). "</b></em> could not be found in class <em><b>" .$e->getClass(). "</b></em>.";
		} else if($e instanceof RoutingException) {
			$data["description"] = "The following URL could not be found: <em><b>" .$_SERVER['HTTP_HOST'].$e->getRequest()->getURL(). "</b></em>.";
		}

		echo $twig->render("exceptionTemplate.html.twig", $data);
	}

	public function __construct($trace = true) {
		$this->setTrace($trace);
	}

	public function isTracing() {
		return $this->trace;
	}

	public function setTrace($trace) {
		$this->trace = $trace;
		return $this;
	}

}
