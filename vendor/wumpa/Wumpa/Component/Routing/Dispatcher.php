<?php

namespace Wumpa\Component\Routing;

use Wumpa\Component\App\App;
use Wumpa\Component\Exception\Exception\FileNotFoundException;
use Wumpa\Component\Exception\Exception\MethodNotFoundException;

/**
 * Include the requested controller by the URL.
 *
 * @author Bastien de Luca <dev@b-deluca.com>
 */
class Dispatcher {
	
	private static function isController($controllerName) {
		if(file_exists(App::get(App::CONTROLLER).$controllerName.".php")) {
			return true;
		} else {
			return false;
		}
	}
	
	private static function isCallable($controller, $method) {
		if(is_callable(array($controller, $method))) {
			return true;
		} else {
			return false;
		}
	}

	public static function dispatch() {
		$regex = "/{(.*?)}/";
		$parameters = array();
		$i = 0;
		
		$request = Router::get()->getRequest()->getNodes();
		$route = Router::get()->getRoute();
		
		foreach(explode("/", $route->getPath()) as $node) {
			if(preg_match($regex, $node)) {
				$nodeName = str_replace(array("{", "}"), "", $node);
				$parameters[$nodeName] = $request[$i];
			}
			$i++;
		}
		
		$call = explode(":", $route->getController());
		$controllerName = $call[0] . "Controller";
		$method = $call[1] . "Action";
		
		if(self::isController($controllerName)) {
			include_once App::get(App::CONTROLLER).$controllerName.".php";
			$controller = new $controllerName;
		} else {
			throw new FileNotFoundException(App::get(App::CONTROLLER).$controllerName.".php");
		}
		
		if(self::isCallable($controller, $method)) {
			call_user_func_array(array($controller, $method), $parameters);
		} else {
			throw new MethodNotFoundException($method, get_class($controller), "Method is not callable");
		}
	}
	
}