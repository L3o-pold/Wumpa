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
use Wumpa\Component\Crate\Crate;

/**
 * This define the application by describing it's execution environment.
 *
 * @author Bastien de Luca
 */
class ConsoleApp {

    const INDEX = "index";
    const CONFIG = "config";
    const DB = "db";
    const MODEL = "model";

    private $indexDir;
    private $configDir;
    private $dbFile;
    private $modelDir;

    private static $instance = null;

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

            if(!isset($sysConfig['model_dir']) || is_null($sysConfig['model_dir'])) {
                $this->setModelDir(null);
            } else {
                if(file_exists($indexDir.$sysConfig['model_dir'])) {
                    $this->setModelDir($indexDir.$sysConfig['model_dir']);
                } else {
                    throw new DirectoryNotFoundException($indexDir.$sysConfig['model_dir']);
                }
            }
        } else {
            throw new FileNotFoundException($configDir."system.php");
        }

    }

    public static function get($path = null) {
        switch ($path) {
            case null:
                return self::$instance;
            case self::INDEX:
                return self::$instance->getIndexDir();
            case self::CONFIG:
                return self::$instance->getConfigDir();
            case self::DB:
                return self::$instance->getDbFile();
            case self::MODEL:
                return self::$instance->getModelDir();
            default:
                throw new InvalidArgumentException(func_get_args(), __METHOD__, __CLASS__);
        }
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
    public function getComponentDir() {
        return $this->componentDir;
    }
    public function setComponentDir($componentDir) {
        $this->componentDir = $componentDir;
        return $this;
    }
    public function getModelDir() {
        return $this->modelDir;
    }
    public function setModelDir($modelDir) {
        $this->modelDir = $modelDir;
        return $this;
    }


    public static function init($indexDir) {
        if(!(self::$instance instanceof self)) {
            self::$instance = new self($indexDir);
        } else {
            throw new IllegalMethodCall(__METHOD__, __CLASS__, "Illegal call of the init() method");
        }
    }

    public static function run() {
        Database::init();
    }

}
