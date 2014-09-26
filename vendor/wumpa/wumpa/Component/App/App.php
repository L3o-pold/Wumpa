<?php

namespace Wumpa\Component\App;

use Wumpa\Component\Exception\Exception\DirectoryNotFoundException;
use Wumpa\Component\Exception\Exception\InvalidArgumentException;
use Wumpa\Component\Exception\Exception\IllegalMethodCall;
use Wumpa\Component\Exception\Exception\FileNotFoundException;
use Wumpa\Component\Database\Database;
use Wumpa\Component\Autoloader\Loader;
use Wumpa\Component\Routing\Router;
use Wumpa\Component\Exception\Exception\ConfigErrorException;
use Wumpa\Component\Exception\ExceptionHandler;
use Wumpa\Component\Autoloader\Autoloader;

/**
 * This define the application by describing it's execution environment.
 *
 * @author Bastien de Luca
 */
class App {

	const URL = "url";
	const INDEX = "index";
	const CONFIG = "config";
	const ROUTES = "routes";
	const DB = "db";
	const CONTROLLER = "controller";
	const MODEL = "model";
	const VIEW = "view";
	const TEMPLATES = "templates";

	private $url;
	private $indexDir;
	private $configDir;
	private $dbFile;
	private $routesFile;
	private $controllerDir;
	private $modelDir;
	private $viewDir;
	private $templatesDir;
	private $displayTrace;
	private $wumpaExcpHandle;

	private static $instance;

	private function __construct($indexDir) {
		if(file_exists($indexDir)) {
			$this->setIndexDir($indexDir);
		} else {
			throw new DirectoryNotFoundException($indexDir);
		}

		$configDir = $indexDir."config/";

		if(file_exists($configDir)) {
			$this->setConfigDir($configDir);
		} else {
			throw new DirectoryNotFoundException($configDir);
		}

		if(file_exists($configDir."system.php")) {
			$sysConfig = require $configDir."system.php";

			if(!isset($sysConfig['database_file']) || is_null($sysConfig['database_file'])) {
				$this->setDbFile(null);
			} else {
				if(file_exists($configDir.$sysConfig['database_file'])) {
					$this->setDbFile($configDir.$sysConfig['database_file']);
				} else {
					throw new FileNotFoundException($configDir.$sysConfig['database_file']);
				}
			}

			if((!isset($sysConfig['routes_file']) || is_null($sysConfig['routes_file'])) || !file_exists($configDir.$sysConfig['routes_file'])) {
				throw new FileNotFoundException($configDir.$sysConfig['routes_file']);
			} else {
				$this->setRoutesFile($configDir.$sysConfig['routes_file']);
			}

			if(!isset($sysConfig['controller_dir']) || is_null($sysConfig['controller_dir'])) {
				$this->setControllerDir(null);
			} else {
				if(file_exists($indexDir.$sysConfig['controller_dir'])) {
					$this->setControllerDir($indexDir.$sysConfig['controller_dir']);
				} else {
					throw new DirectoryNotFoundException($indexDir.$sysConfig['controller_dir']);
				}
			}

			if(!isset($sysConfig['model_dir']) || is_null($sysConfig['model_dir'])) {
				$this->setModelDir(null);
			} else {
                if(file_exists($indexDir.$sysConfig['model_dir'])) {
					$this->setModelDir($indexDir.$sysConfig['model_dir']);
				} else {
					throw new DirectoryNotFoundException($indexDir.$sysConfig['model_dir']);
				}
			}

			if(!isset($sysConfig['view_dir']) || is_null($sysConfig['view_dir'])) {
				$this->setViewDir(null);
			} else {
				if(file_exists($indexDir.$sysConfig['view_dir'])) {
					$this->setViewDir($indexDir.$sysConfig['view_dir']);
				} else {
					throw new DirectoryNotFoundException($indexDir.$sysConfig['view_dir']);
				}
			}

			if(!isset($sysConfig['templates_dir']) || is_null($sysConfig['templates_dir'])) {
				$this->setTemplatesDir(null);
			} else {
				if(file_exists($indexDir.$sysConfig['templates_dir'])) {
					$this->setTemplatesDir($indexDir.$sysConfig['templates_dir']);
				} else {
					throw new DirectoryNotFoundException($indexDir.$sysConfig['templates_dir']);
				}
			}
		} else {
			throw new FileNotFoundException($configDir."system.php");
		}

		$this->setUrl(self::retrieveURL());

        if(!isset($sysConfig['wumpa_exception_handler'])) {
            $this->setWumpaExcpHandle(false);
            $this->setDisplayTrace(false);
        } else {
            if(!is_bool($sysConfig['wumpa_exception_handler'])) {
                throw new ConfigErrorException("Wrong type used in configuration, wumpa_exception_handler must be a boolean.");
            } else {
                $this->setWumpaExcpHandle($sysConfig['wumpa_exception_handler']);
            }

            if(!isset($sysConfig['debug_trace'])) {
                $this->setDisplayTrace(false);
            } else {
                if(!is_bool($sysConfig['debug_trace'])) {
                    throw new ConfigErrorException("Wrong type used in configuration, debug_trace must be a boolean.");
                } else {
                    $this->setDisplayTrace($sysConfig['debug_trace']);
                }
            }
        }
	}

	public static function get($path = null) {
		switch ($path) {
			case null:
				return self::$instance;
			case self::URL:
				return self::$instance->getUrl();
			case self::INDEX:
				return self::$instance->getIndexDir();
			case self::CONFIG:
				return self::$instance->getConfigDir();
			case self::ROUTES:
				return self::$instance->getRoutesFile();
			case self::DB:
				return self::$instance->getDbFile();
			case self::CONTROLLER:
				return self::$instance->getControllerDir();
			case self::MODEL:
				return self::$instance->getModelDir();
			case self::VIEW:
				return self::$instance->getViewDir();
			case self::TEMPLATES:
				return self::$instance->getTemplatesDir();
			default:
				throw new InvalidArgumentException(func_get_args(), __METHOD__, __CLASS__);
		}
	}

	public static function isDisplayingTrace() {
		return self::$instance->getDisplayTrace();
	}

	public static function enableTrace() {
		self::$instance->setDisplayTrace(true);
	}

	public static function disableTrace() {
		self::$instance->setDisplayTrace(false);
	}

	public static function isWumpaHandlingExcp() {
		return self::$instance->getWumpaExcpHandle();
	}

	public static function enableWumpaExcpHandler() {
		self::$instance->setWumpaExcpHandle(true);
		ExceptionHandler::register();
	}

	public static function disableWumpaExcpHandler() {
		self::$instance->setWumpaExcpHandle(false);
		ExceptionHandler::unregister();
	}

	private function retrieveURL() {
		$requestURI = explode('/', $_SERVER['REQUEST_URI']);
		$scriptName = explode('/', $_SERVER['SCRIPT_NAME']);

		for($i=0; $i<sizeof($scriptName) && $requestURI[$i] == $scriptName[$i]; $i++);

		$requestArray = array_values( array_slice($requestURI,0, $i) );

		for($i=0; $i<sizeof($requestArray); $i++) {
			$requestArray[$i] = $requestArray[$i].'/';
		}

		$path = '';
		foreach ( $requestArray as $partURI ) {
			$path .= $partURI;
		}

		return $path;
	}

	public function getUrl() {
		return $this->url;
	}
	public function setUrl($url) {
		$this->url = $url;
		return $this;
	}
	public function getIndexDir() {
		return $this->indexDir;
	}
	public function setIndexDir($indexDir) {
		$this->indexDir = $indexDir;
		return $this;
	}
	public function getWumpaDir() {
		return $this->wumpaDir;
	}
	public function setWumpaDir($wumpaDir) {
		$this->wumpaDir = $wumpaDir;
		return $this;
	}
	public function getConfigDir() {
		return $this->configDir;
	}
	public function setConfigDir($configDir) {
		$this->configDir = $configDir;
		return $this;
	}
	public function getDbFile() {
		return $this->dbFile;
	}
	public function setDbFile($dbFile) {
		$this->dbFile = $dbFile;
		return $this;
	}
	public function getRoutesFile() {
		return $this->routesFile;
	}
	public function setRoutesFile($routesFile) {
		$this->routesFile = $routesFile;
		return $this;
	}
	public function getComponentDir() {
		return $this->componentDir;
	}
	public function setComponentDir($componentDir) {
		$this->componentDir = $componentDir;
		return $this;
	}
	public function getControllerDir() {
		return $this->controllerDir;
	}
	public function setControllerDir($controllerDir) {
		$this->controllerDir = $controllerDir;
		return $this;
	}
	public function getModelDir() {
		return $this->modelDir;
	}
	public function setModelDir($modelDir) {
		$this->modelDir = $modelDir;
		return $this;
	}
	public function getViewDir() {
		return $this->viewDir;
	}
	public function setViewDir($viewDir) {
		$this->viewDir = $viewDir;
		return $this;
	}
	public function getTemplatesDir() {
		return $this->templatesDir;
	}
	public function setTemplatesDir($templatesDir) {
		$this->templatesDir = $templatesDir;
		return $this;
	}
	public function getWumpaExcpHandle() {
		return $this->wumpaExcpHandle;
	}
	public function setWumpaExcpHandle($wumpaExcpHandle) {
		$this->wumpaExcpHandle = $wumpaExcpHandle;
		return $this;
	}
	public function getDisplayTrace() {
		return $this->displayTrace;
	}
	public function setDisplayTrace($displayTrace) {
		$this->displayTrace = $displayTrace;
		return $this;
	}

	public static function init($indexDir) {
		if(!(self::$instance instanceof self)) {
			self::$instance = new self($indexDir);
            if(self::isWumpaHandlingExcp())
                ExceptionHandler::register();
		} else {
			throw new IllegalMethodCall(__METHOD__, __CLASS__, "Illegal call of the init() method");
		}
	}

	public static function run() {
		Database::init();
		Autoloader::register();
		Router::init();
	}

}
