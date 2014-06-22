<?php

namespace Wumpa\Component\Routing;

use Wumpa\Component\Exception\Exception\RoutingException;

class Matcher {
	
	public static function match() {
		$regex = "/{(.*?)}/";
		$found = null;
		
		$routingTable = Router::get()->getRoutingTable();
		$request = Router::get()->getRequest()->getNodes();
		
		foreach($routingTable as $route) {
			if($route->getPath() == "/") {
				$path = array("");
			} else {
				$path = explode('/', $route->getPath());
			}
			if(sizeof($path) == sizeof($request)) {
				$nbSame = 0;
				$i = 0;
				foreach($path as $node) {
					if(preg_match($regex, $node)) {
						$nodeName = str_replace(array("{", "}"), "", $node);
						if(isset($route->getRequirements()[$nodeName])) {
							if(is_array($route->getRequirements()[$nodeName])) {
								if(in_array($request[$i], $route->getRequirements()[$nodeName], true)) {
									$nbSame ++;
								}
							} elseif($route->getRequirements()[$nodeName] == "num" || $route->getRequirements()[$nodeName] == "char") {
								switch ($route->getRequirements()[$nodeName]) {
									case "num":
										if(is_numeric($request[$i])) {
											$nbSame++;
										}
										break;
									case "char":
										if(ctype_alpha($request[$i])) {
											$nbSame++;
										}
										break;
								}
							} else {
								if(preg_match($route->getRequirements()[$nodeName], $request[$i])) {
									$nbSame++;
								}
							}
						} else {	
							$nbSame++;
						}
					} else {
						if($node == $request[$i]) {
							$nbSame++;
						} else {
							break;
						}
					}
					$i++;
				}
				
				if($nbSame == sizeof($path)) {
					$found = $route;
					break;
				}
			}
		}
		
		if(is_null($found)) {
			throw new RoutingException(Router::get()->getRequest(), "No matching route found", 404);
		} else {
			return $found;
		}
	}
	
}