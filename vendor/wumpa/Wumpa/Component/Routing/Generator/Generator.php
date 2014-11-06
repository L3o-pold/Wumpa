<?php

namespace Wumpa\Component\Routing\Generator;

use Wumpa\Component\Routing\Router;
use Wumpa\Component\App\App;
use Wumpa\Component\Exception\Exception\InvalidArgumentException;

class Generator {

	public static function generate($routeName, $parameters = null) {

		$regex = "/{(.*?)}/";

		$routingTable = Router::get()->getRoutingTable();

		foreach($routingTable as $route) {
			if($route->getName() == $routeName) {
				if($route->getPath() == "/") {
					return $route->getPath();
				}
				
				$parts = explode("/", $route->getPath());
				$first = 0;
				$genUrl = $_SERVER['HTTP_HOST'].App::get(App::URL);;
				
				foreach($parts as $node) {
					
					if(preg_match($regex, $node)) {
						$nodeName = str_replace(array("{", "}"), "", $node);
						if(isset($parameters[$nodeName])) {
							if(isset($route->getRequirements()[$nodeName])) {
								if(is_array($route->getRequirements()[$nodeName])) {
									if(in_array($parameters[$nodeName], $route->getRequirements()[$nodeName], true)) {
										if($first == 0) {
											$genUrl .= $parameters[$nodeName];
										} else {
											$genUrl .= "/" .$parameters[$nodeName];
										}
									} else {
										throw new InvalidArgumentException(func_get_args(), __METHOD__, __CLASS__);
									}
								} elseif($route->getRequirements()[$nodeName] == "num" || $route->getRequirements()[$nodeName] == "char") {
									switch ($route->getRequirements()[$nodeName]) {
										case "num":
											if(is_numeric($parameters[$nodeName])) {
												if($first == 0) {
													$genUrl .= $parameters[$nodeName];
												} else {
													$genUrl .= "/" .$parameters[$nodeName];
												}
											} else {
												throw new InvalidArgumentException(func_get_args(), __METHOD__, __CLASS__);
											}
											break;
										case "char":
											if(ctype_alpha($parameters[$nodeName])) {
												if($first == 0) {
													$genUrl .= $parameters[$nodeName];
												} else {
													$genUrl .= "/" .$parameters[$nodeName];
												}
											} else {
												throw new InvalidArgumentException(func_get_args(), __METHOD__, __CLASS__);
											}
											break;
									}
								} else {
									if(preg_match($route->getRequirements()[$nodeName], $parameters[$nodeName])) {
										if($first == 0) {
											$genUrl .= $parameters[$nodeName];
										} else {
											$genUrl .= "/" .$parameters[$nodeName];
										}
									} else {
										throw new InvalidArgumentException(func_get_args(), __METHOD__, __CLASS__);
									}
								}
							}	
						} else {
							throw new InvalidArgumentException(func_get_args(), __METHOD__, __CLASS__);
						}
					} else {
						if($first == 0) {
							$genUrl .= $node;
						} else {
							$genUrl .= "/" .$node;
						}
					}
					
					$first++;
				}
				
				return $genUrl;
			}
		}

		return false;
	}

}
