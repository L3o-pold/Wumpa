<?php

namespace Wumpa\Component\Exception;

use Wumpa\Component\Exception\Exception\ConfigErrorException;
use Wumpa\Component\Exception\Exception\DirectoryNotFoundException;
use Wumpa\Component\Exception\Exception\FileNotFoundException;
use Wumpa\Component\Exception\Exception\IllegalMethodCall;
use Wumpa\Component\Exception\Exception\InvalidArgumentException;
use Wumpa\Component\Exception\Exception\MethodNotFoundException;
use Wumpa\Component\Exception\Exception\RoutingException;
use Wumpa\Component\App\App;

class ExceptionHandler {

	/**
	 * Define Pulsar exception handling.
	 *
	 * Define a way to render each exceptions thrown by the framework.
	 *
	 * @param \Exception $e
	 */
	public function handle($e) {
		\Twig_Autoloader::register();
		$loader = new \Twig_Loader_Filesystem(__DIR__."/../../../../../app/resources/view/templates/");
		$twig = new \Twig_Environment($loader);

		$data = array(
			"code" => $e->getCode(),
			"message" => $e->getMessage(),
		);

		if(App::isDisplayingTrace()) {
			$tabTrace = "<table class=\"table table-hover table-bordered\">";
			$tabTrace .= "<tr class=\"danger\">";
			$tabTrace .= "<th>File</th>";
			$tabTrace .= "<th>Line</th>";
			$tabTrace .= "<th>Function</th>";
			if(isset($e->getTrace()[0]["class"])) {
				$tabTrace .= "<th>Class</th>";
			}
			$tabTrace .= "</tr>";

			foreach ($e->getTrace() as $step) {
				$tabTrace .= "<tr>";
				if(isset($step["file"])) {
					$tabTrace .= "<td>".$step["file"]."</td>";
				} else {
					$tabTrace .= "<td></td>";
				}
				if(isset($step["line"])) {
					$tabTrace .= "<td>".$step["line"]."</td>";
				} else {
					$tabTrace .= "<td></td>";
				}
				$tabTrace .= "<td>".$step["function"]."</td>";
				if(isset($step["class"])) {
					$tabTrace .= "<td>".$step["class"]."</td>";
				} else {
					$tabTrace .= "<td></td>";
				}
				$tabTrace .= "</tr>";
			}

			$tabTrace .= "</table>";

			$data["tabTrace"] = $tabTrace;
		}

		if($e instanceof DirectoryNotFoundException) {
			$data["description"] = "The following directory could not be found on server: <em><b>" .$e->getDirectory(). "</b></em>.";
		} else if($e instanceof FileNotFoundException) {
			$data["description"] = "The following file could not be found on server: <em><b>" .$e->getFile(). "</b></em>.";
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

	/**
	 * Register the handle() function as exception handler
	 */
	public static function register() {
		set_exception_handler(array(new self(), "handle"));
	}

	/**
	 * Reset the exception handler to its default state
	 */
	public static function unregister() {
		set_exception_handler(null);
	}
}
