<?php

namespace Wumpa\Component\Box;

class Box {

    private $storage = array();

    private function __construct() {
    }
    
    public function getStorage() {
    	return $this->storage;
    }
    public function setStorage($storage) {
    	$this->storage = $storage;
    	return $this;
    }

    public static function init() {
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
        	echo "have to expire";
            unset($storage[$nameTag]);
        }
        
        $box->setStorage($storage);
        $box = serialize($box);
        $_SESSION['~Box'] = $box;
        
        return $item['item'];
    }

}
