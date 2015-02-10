<?php

namespace Wumpa\Component\App;

use Wumpa\Component\Box\Box;

/**
 * Store the application singleton and provide methods to access it.
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class App {

    private static $app;

    public static function get() {
        return self::$app;
    }

    public static function getDatabase() {
        return self::$app->getDatabase();
    }

    public static function getRouter() {
        if(!(self::$app instanceof AppConsole))
            return self::$app->getRouter();
    }

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
