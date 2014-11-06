<?php

namespace Wumpa\Component\Routing;

/**
 * Handle the URI request and return the corresponding request in an array.
 *
 * @author Bastien de Luca <dev@b-deluca.com>
 */
class UrlHandler {
	
	public static function getRequest() {
		return $_SERVER['REQUEST_URI'];
	}
	
	public static function getRequestNodes() {
		$requestURI = explode('/', $_SERVER['REQUEST_URI']);
		$scriptName = explode('/', $_SERVER['SCRIPT_NAME']);
		
		for($i=0; $i<sizeof($scriptName) && $requestURI[$i] == $scriptName[$i]; $i++);
		
		$requestArray = array_values(array_slice($requestURI,$i));
		
		return $requestArray;
	}
	
}