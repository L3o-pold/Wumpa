<?php

namespace Wumpa\Component\App;

use Wumpa\Component\Routing\Router;
use Wumpa\Component\Autoloader\Autoloader;
use Wumpa\Component\Database\Database;
use Wumpa\Component\Exception\ExceptionHandler;

/**
 * Handle the creation of applications depending on the type.
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class AppFactory {

    const APP_INDEX = "index";
    const APP_AJAX  = "ajax";
    const APP_TERM  = "term";

    private $appType;

    public function create($indexDir) {
        switch($this->getAppType()) {
            case self::APP_INDEX:
                $app = new AppIndex($indexDir);

                $router = new Router($app);
                $app->setRouter($router);

                $autoloader = new Autoloader($app);
                $autoloader->register();
                $app->setAutoloader($autoloader);

                if(!is_null($app->getDbFile()) && !is_null(require $app->getDbFile())) {
                    $database = new Database($app);
                    $app->setDatabase($database);
                }

                if($app->isHandlingExcp()) {
                    $sysConfig = require $app->getConfigDir()."system.php";
                    $excpHandler = new ExceptionHandler($sysConfig["debug_trace"]);
                    $excpHandler->register();
                    $app->setExcpHandler($excpHandler);
                }
                break;
            case self::APP_AJAX:
                $app = new AppAjax($indexDir);

                $router = new Router($app);
                $app->setRouter($router);

                $autoloader = new Autoloader($app);
                $autoloader->register();
                $app->setAutoloader($autoloader);

                if(!is_null($app->getDbFile()) && !is_null(require $app->getDbFile())) {
                    $database = new Database($app);
                    $app->setDatabase($database);
                }
                break;
            case self::APP_TERM:
                $app = new AppConsole($indexDir);

                if(!is_null($app->getDbFile()) && !is_null(require $app->getDbFile())) {
                    $database = new Database($app);
                    $app->setDatabase($database);
                }
                break;
        }

        return $app;
    }

    public function __construct($appType = self::APP_INDEX) {
        $this->setAppType($appType);
    }

    // Getter and Setter
    public function getAppType() {
        return $this->appType;
    }

    public function setAppType($appType) {
        $this->appType = $appType;
        return $this;
    }

}
