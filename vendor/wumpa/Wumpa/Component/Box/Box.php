<?php

namespace Wumpa\Component\Box;

/**
 * Provide a simple way to store, retrieve, manage data in session.
 * Also provide a data expiration system.
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class Box {

    private $storage = array();

    public static function init() {
        session_start();

        if(isset($_SESSION['~Box'])) {
            return false;
        } else {
            $box = new self();
            $box = serialize($box);
            $_SESSION['~Box'] = $box;
            return true;
        }
    }

    public static function store($nameTag, $item, $expire = false) {
        $box = $_SESSION['~Box'];
        $box = unserialize($box);
        $storage = $box->getStorage();

        $storage[$nameTag] = array(
            'item' => $item,
            'expire' => $expire
        );

        $box->setStorage($storage);
        $box = serialize($box);
        $_SESSION['~Box'] = $box;
    }

    public static function get($nameTag) {
        $box = $_SESSION['~Box'];
        $box = unserialize($box);
        $storage = $box->getStorage();

        if(!isset($storage[$nameTag])) {
            return false;
        }

        $item = $storage[$nameTag];

        if($item['expire'] == true) {
            unset($storage[$nameTag]);
        }

        $box->setStorage($storage);
        $box = serialize($box);
        $_SESSION['~Box'] = $box;

        return $item['item'];
    }

    public static function remove($nameTag) {
        $box = $_SESSION['~Box'];
        $box = unserialize($box);
        $storage = $box->getStorage();

        if(!isset($storage[$nameTag])) {
            return false;
        }

        unset($storage[$nameTag]);

        $box->setStorage($storage);
        $box = serialize($box);
        $_SESSION['~Box'] = $box;

        return true;
    }

    public function getStorage() {
        return $this->storage;
    }

    public function setStorage($storage) {
        $this->storage = $storage;
        return $this;
    }

}
