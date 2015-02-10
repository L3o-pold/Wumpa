<?php

namespace Wumpa\Component\Model;

/**
 * Define the required methodes for model classes
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
interface ModelInterface {

    public static function getPrimaries();
    public static function getTableName();

}
