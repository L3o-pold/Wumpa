<?php

namespace Wumpa\Component\App;

use Wumpa\Component\Box\Box;
use Wumpa\Component\Database\Database;
use Wumpa\Component\Routing\Router;

/**
 * Store the application singleton and provide methods to access it.
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class App {

    /**
     * @var AppIndex|AppAjax|AppConsole
     */
    private static $app;

    /**
     * @return AppAjax|AppConsole|AppIndex
     */
    public static function get() {
        return self::$app;
    }

    /**
     * @return null|Database
     */
    public static function getDatabase() {
        return self::$app->getDatabase();
    }

    /**
     * @return null|Router
     */
    public static function getRouter() {
        if(!(self::$app instanceof AppConsole)) {
            return self::$app->getRouter();
        }
        return null;
    }

    /**
     * @param $indexDir
     * @param $appType
     */
    public static function init($indexDir, $appType) {
        $factory = new AppFactory($appType);
        $app = $factory->create($indexDir);
        self::$app = $app;

        if(!($app instanceof AppConsole))
            Box::init();
    }

    public static function run() {
        self::$app->run();
    }

}
