<?php

namespace Wumpa\Component\App;

use Wumpa\Component\Exception\Exception\DirectoryNotFoundException;
use Wumpa\Component\Exception\Exception\FileNotFoundException;

/**
 * Define the common data required for an application to work.
 * It defines its execution environment and its database.
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
abstract class AppMom implements AppInterface {

    protected $indexDir;
    protected $configDir;
    protected $dbFile;
    protected $routesFile;
    protected $controllerDir;
    protected $modelDir;
    protected $viewDir;
    protected $templatesDir;

    protected $database;

    public function __construct($indexDir) {
        $this->setIndexDir($indexDir);

        $configDir = $indexDir."config/";

        if(!file_exists($configDir))
            throw new DirectoryNotFoundException($configDir);

        $this->setConfigDir($configDir);

        if(!file_exists($configDir."system.php"))
            throw new FileNotFoundException($configDir."system.php");

        $sysConfig = require $configDir."system.php";

        if(!isset($sysConfig['database_file']) || is_null($sysConfig['database_file'])) {
            $this->setDbFile(null);
        } else {
            if(!file_exists($configDir.$sysConfig['database_file']))
                throw new FileNotFoundException($configDir.$sysConfig['database_file']);

            $this->setDbFile($configDir.$sysConfig['database_file']);
        }

        if((!isset($sysConfig['routes_file']) || is_null($sysConfig['routes_file'])) || !file_exists($configDir.$sysConfig['routes_file']))
            throw new FileNotFoundException($configDir.$sysConfig['routes_file']);

        $this->setRoutesFile($configDir.$sysConfig['routes_file']);

        if(!isset($sysConfig['controller_dir']) || is_null($sysConfig['controller_dir'])) {
            $this->setControllerDir(null);
        } else {
            if(!file_exists($indexDir.$sysConfig['controller_dir']))
                throw new DirectoryNotFoundException($indexDir.$sysConfig['controller_dir']);

            $this->setControllerDir($indexDir.$sysConfig['controller_dir']);
        }

        if(!isset($sysConfig['model_dir']) || is_null($sysConfig['model_dir'])) {
            $this->setModelDir(null);
        } else {
            if(!file_exists($indexDir.$sysConfig['model_dir']))
                throw new DirectoryNotFoundException($indexDir.$sysConfig['model_dir']);

            $this->setModelDir($indexDir.$sysConfig['model_dir']);
        }

        if(!isset($sysConfig['view_dir']) || is_null($sysConfig['view_dir'])) {
            $this->setViewDir(null);
        } else {
            if(!file_exists($indexDir.$sysConfig['view_dir']))
                throw new DirectoryNotFoundException($indexDir.$sysConfig['view_dir']);

            $this->setViewDir($indexDir.$sysConfig['view_dir']);
        }

        if(!isset($sysConfig['templates_dir']) || is_null($sysConfig['templates_dir'])) {
            $this->setTemplatesDir(null);
        } else {
            if(!file_exists($indexDir.$sysConfig['templates_dir']))
                throw new DirectoryNotFoundException($indexDir.$sysConfig['templates_dir']);

            $this->setTemplatesDir($indexDir.$sysConfig['templates_dir']);
        }
    }

    public function getIndexDir() {
        return $this->indexDir;
    }

    public function setIndexDir($indexDir) {
        $this->indexDir = $indexDir;
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

    //I love the developper who made that !

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

    public function getDatabase() {
        return $this->database;
    }

    public function setDatabase($database) {
        $this->database = $database;
        return $this;
    }

}
