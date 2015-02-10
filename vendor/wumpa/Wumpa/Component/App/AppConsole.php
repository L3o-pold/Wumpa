<?php

namespace Wumpa\Component\App;

use Wumpa\Component\Exception\Exception\DirectoryNotFoundException;

/**
 * Define an application callable from the Wumpa console component to use its
 * database management system.
 *
 * This app should not be used for anything else than console.
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class AppConsole extends AppMom {

    public function run() {
        // TODO: do something ? maybe
    }

    public function __construct($indexDir) {
        if(!file_exists($indexDir))
            throw new DirectoryNotFoundException($indexDir);

        parent::__construct($indexDir);
    }

}
