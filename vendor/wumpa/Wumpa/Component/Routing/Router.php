<?php

namespace Wumpa\Component\Routing;

use Wumpa\Component\App\App;
use Wumpa\Component\App\AppIndex;
use Wumpa\Component\Routing\Loader\PhpLoader;
use Wumpa\Component\Exception\Exception\IllegalMethodCall;
use Wumpa\Component\Exception\Exception\ConfigErrorException;
use Wumpa\Component\Exception\Exception\RoutingException;
use Wumpa\Component\Exception\Exception\InvalidArgumentException;

/**
 * Provide methodes to handle routing system in project.
 * This class handle requests and match them with existing routes, then the
 * route can be dispatched to call the controller.
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class Router {

	private $routingTable;
	private $request;
	private $route;

	private $app;

	private function isController($controllerName) {
		return file_exists($this->getApp()->getControllerDir().$controllerName.".php");
	}

	public function handleRequest() {
		$this->setRequest(new Request());
		$this->setRoute($this->match());
	}

	private function match() {
		$regex = "/{(.*?)}/";
		$found = null;

		$routingTable = $this->getRoutingTable();
		$request = $this->getRequest()->getNodes();

		foreach($routingTable as $route) {

			if($route->getPath() == "/")
				$path = array("");
			else
				$path = explode('/', $route->getPath());

			if(sizeof($path) == sizeof($request)) {

				$nbSame = 0;
				$i = 0;

				foreach($path as $node) {
					if(preg_match($regex, $node)) {

						$nodeName = str_replace(array("{", "}"), "", $node);

						if(isset($route->getRequirements()[$nodeName])) {
							if(is_array($route->getRequirements()[$nodeName])) {
								if(in_array($request[$i], $route->getRequirements()[$nodeName], true))
									$nbSame ++;
							} elseif($route->getRequirements()[$nodeName] == "num"
								|| $route->getRequirements()[$nodeName] == "char") {
								switch ($route->getRequirements()[$nodeName]) {
									case "num":
										if(is_numeric($request[$i]))
											$nbSame++;
										break;
									case "char":
										if(ctype_alpha($request[$i]))
											$nbSame++;
										break;
								}
							} else {
								if(preg_match($route->getRequirements()[$nodeName], $request[$i]))
									$nbSame++;
							}
						} else {
							$nbSame++;
						}
					} else {
						if($node == $request[$i])
							$nbSame++;
						else
							break;
					}
					$i++;
				}

				if($nbSame == sizeof($path)) {
					$found = $route;
					break;
				}
			}
		}

		if(is_null($found))
			throw new RoutingException($this->getRequest(), "No matching route found", 404);

		return $found;
	}

	public function dispatch() {
		$regex = "/{(.*?)}/";
		$parameters = array();
		$i = 0;

		$request = $this->getRequest()->getNodes();
		$route = $this->getRoute();

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

		if(!$this->isController($controllerName))
			throw new FileNotFoundException($this->getApp()->getControllerDir().$controllerName.".php");

		include_once $this->getApp()->getControllerDir().$controllerName.".php";
		$controller = new $controllerName;

		if(!is_callable(array($controller, $method)))
			throw new MethodNotFoundException($method, get_class($controller), "Method is not callable");

		call_user_func_array(array($controller, $method), $parameters);
	}

	public function generate($routeName, $parameters = null) {
		$regex = "/{(.*?)}/";
		$routingTable = $this->getRoutingTable();

		foreach($routingTable as $route) {
			if($route->getName() == $routeName) {

				$parts = explode("/", $route->getPath());
				$first = 0;
				$genUrl = $_SERVER['HTTP_HOST'].$this->getApp()->getUrl();

				if($route->getPath() == "/") {
					return "http://".$genUrl;
				}

				foreach($parts as $node) {

					if(preg_match($regex, $node)) {
						$nodeName = str_replace(array("{", "}"), "", $node);
						if(isset($parameters[$nodeName])) {
							if(isset($route->getRequirements()[$nodeName])) {
								if(is_array($route->getRequirements()[$nodeName])) {
									if(in_array($parameters[$nodeName], $route->getRequirements()[$nodeName], true)) {
										if($first == 0)
											$genUrl .= $parameters[$nodeName];
										else
											$genUrl .= "/" .$parameters[$nodeName];
									} else {
										throw new InvalidArgumentException(func_get_args(), __METHOD__, __CLASS__);
									}
								} elseif($route->getRequirements()[$nodeName] == "num" || $route->getRequirements()[$nodeName] == "char") {
									switch ($route->getRequirements()[$nodeName]) {
										case "num":
											if(is_numeric($parameters[$nodeName])) {
												if($first == 0)
													$genUrl .= $parameters[$nodeName];
												else
													$genUrl .= "/" .$parameters[$nodeName];
											} else {
												throw new InvalidArgumentException(func_get_args(), __METHOD__, __CLASS__);
											}
											break;
										case "char":
											if(ctype_alpha($parameters[$nodeName])) {
												if($first == 0)
													$genUrl .= $parameters[$nodeName];
												else
													$genUrl .= "/" .$parameters[$nodeName];
											} else {
												throw new InvalidArgumentException(func_get_args(), __METHOD__, __CLASS__);
											}
											break;
									}
								} else {
									if(preg_match($route->getRequirements()[$nodeName], $parameters[$nodeName])) {
										if($first == 0)
											$genUrl .= $parameters[$nodeName];
										else
											$genUrl .= "/" .$parameters[$nodeName];
									} else {
										throw new InvalidArgumentException(func_get_args(), __METHOD__, __CLASS__);
									}
								}
							} else {
								if($first == 0)
									$genUrl .= $parameters[$nodeName];
								else
									$genUrl .= "/" .$parameters[$nodeName];
							}
						} else {
							throw new InvalidArgumentException(func_get_args(), __METHOD__, __CLASS__);
						}
					} else {
						if($first == 0)
							$genUrl .= $node;
						else
							$genUrl .= "/" .$node;
					}
					$first++;
				}
				return "http://".$genUrl;
			}
		}
		return false;
	}

	public function __construct($app) {
		$this->setApp($app);
		$loader = new PhpLoader($app->getRoutesFile());
		$this->setRoutingTable($loader->load());
	}

	public function getRoutingTable() {
		return $this->routingTable;
	}

	public function setRoutingTable($routingTable) {
		$this->routingTable = $routingTable;
		return $this;
	}

	public function getRequest() {
		return $this->request;
	}

	public function setRequest($request) {
		$this->request = $request;
		return $this;
	}

	public function getRoute() {
		return $this->route;
	}

	public function setRoute($route) {
		$this->route = $route;
		return $this;
	}

	public function getApp() {
		return $this->app;
	}

	public function setApp($app) {
		$this->app = $app;
		return $this;
	}

}
