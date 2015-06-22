<?php

namespace Wumpa\Component\App;

use Wumpa\Component\Exception\Exception\DirectoryNotFoundException;

/**
 * Define an application callable from an ajax controller in order to use the
 * framework features.
 *
 * This app do not handle or dispatch nor it handle Exceptions.
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class AppAjax extends AppMom {

    private $baseUrl;

    private $autoloader;
    private $router;


    public function run() {
        // TODO: do something ?
    }

    // TODO: Change to get common part in url
    private function retrieveBaseUrl() {
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

        $this->setBaseUrl($this->retrieveBaseUrl());
    }

    public function getBaseUrl() {
        return $this->baseUrl;
    }

    public function setBaseUrl($url) {
        $this->baseUrl = $url;
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

}
