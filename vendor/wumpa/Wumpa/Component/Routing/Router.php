<?php

namespace Wumpa\Component\Routing;

use Wumpa\Component\App\App;
use Wumpa\Component\Routing\Loader\PhpLoader;
use Wumpa\Component\Routing\Loader\XmlLoader;
use Wumpa\Component\Routing\Loader\JsonLoader;
use Wumpa\Component\Exception\Exception\IllegalMethodCall;
use Wumpa\Component\Exception\Exception\ConfigErrorException;

/**
 * 
 *
 * @author Bastien de Luca <dev@b-deluca.com>
 */
class Router {
	
	private static $instance;
	
	private $routingTable;
	private $request;
	private $route;
	
	private function __construct() {
		$loader = new PhpLoader(App::get(App::ROUTES));
		$this->setRoutingTable($loader->load());
		$this->setRequest(new Request(UrlHandler::getRequest(), UrlHandler::getRequestNodes()));
		$this->setRoute(null);
	}
	
	public function getLogs() {
		return $this->logs;
	}
	public function setLogs($logs) {
		$this->logs = $logs;
		return $this;
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
	
	public static function get() {
		return self::$instance;
	}
	
	public static function init() {
		if(!(self::$instance instanceof self)) {
			self::$instance = new self();
			self::$instance->setRoute(Matcher::match());
			Dispatcher::dispatch();
		} else {
			throw new IllegalMethodCall(__METHOD__, __CLASS__, "Illegal call of the init() method");
		}
	}

}