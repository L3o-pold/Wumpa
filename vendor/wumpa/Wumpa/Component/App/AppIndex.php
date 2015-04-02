<?php

namespace Wumpa\Component\App;

use Wumpa\Component\Exception\Exception\DirectoryNotFoundException;
use Wumpa\Component\Exception\Exception\ConfigErrorException;

/**
 * Define an application callable from an index.php file.
 * This class provide required data to run a project.
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class AppIndex extends AppMom {

    private $url;
    private $handleExcp;

    private $autoloader;
    private $router;
    private $excpHandler;

    public function run() {
        $this->getRouter()->handleRequest();
        $this->getRouter()->dispatch();
    }

    private function retrieveUrl() {
        $requestURI = explode('/', $_SERVER['REQUEST_URI']);
        $scriptName = explode('/', $_SERVER['SCRIPT_NAME']);

        for($i=0; $i<sizeof($scriptName) && $requestURI[$i] == $scriptName[$i]; $i++);

        $requestArray = array_values(array_slice($requestURI,0, $i));

        for($i=0; $i<sizeof($requestArray); $i++) {
            $requestArray[$i] = $requestArray[$i].'/';
        }

        $path = '';
        foreach ( $requestArray as $partURI ) {
            $path .= $partURI;
        }

        return $path;
    }

    public function __construct($indexDir) {
        if(!file_exists($indexDir))
            throw new DirectoryNotFoundException($indexDir);

        parent::__construct($indexDir);

        $configDir = $indexDir."config/";
        $sysConfig = require $configDir."system.php";

        $this->setUrl($this->retrieveUrl());

        if(!isset($sysConfig['wumpa_exception_handler'])) {
            $this->setHandleExcp(false);
        } else {
            if(!is_bool($sysConfig['wumpa_exception_handler']))
                throw new ConfigErrorException("Wrong type used in configuration, wumpa_exception_handler must be a boolean.");

            $this->setHandleExcp($sysConfig['wumpa_exception_handler']);
        }
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    public function isHandlingExcp() {
        return $this->handleExcp;
    }

    public function setHandleExcp($handleExcp) {
        $this->handleExcp = $handleExcp;
        return $this;
    }

    public function getAutoloader() {
        return $this->autoloader;
    }

    public function setAutoloader($autoloader) {
        $this->autoloader = $autoloader;
        return $this;
    }

    public function getRouter() {
        return $this->router;
    }

    public function setRouter($router) {
        $this->router = $router;
        return $this;
    }

    public function getExcpHandler() {
        return $this->excpHandler;
    }

    public function setExcpHandler($excpHandler) {
        $this->excpHandler = $excpHandler;
        return $this;
    }

}
