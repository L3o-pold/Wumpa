<?php

namespace Wumpa\Component\Console;

use Symfony\Component\Console\Application;
use Wumpa\Component\Console\Command\dbSetup;
use Wumpa\Component\Console\Command\modelSetup;
use Wumpa\Component\Console\Command\projectSetup;


/**
 * Container for a collection of Wumpa commands
 *
 * @author Léopold Jacquot <leopold.jacquot@gmail.com>
 */
class Console extends Application {

    /**
     * Gets the default commands that should always be available.
     *
     * @return array An array of default Command instances
     */
    protected function getDefaultCommands() {
        $defaultCommands = parent::getDefaultCommands();

        $defaultCommands[] = new ProjectSetup();
        $defaultCommands[] = new DbSetup();
        $defaultCommands[] = new ModelSetup();

        return $defaultCommands;
    }
}
