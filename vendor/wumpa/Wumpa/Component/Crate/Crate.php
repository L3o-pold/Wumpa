<?php

namespace Wumpa\Component\Crate;

class Crate {

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
        if(isset($_SESSION['~Crate'])) {
            return false;
        } else {
            $crate = new self();
            $crate = serialize($crate);
            $_SESSION['~Crate'] = $crate;
            return true;
        }
    }

    public static function store($nameTag, $item, $expire = false) {
        $crate = $_SESSION['~Crate'];
        $crate = unserialize($crate);
        $storage = $crate->getStorage();

        $storage[$nameTag] = array(
            'item' => $item,
            'expire' => $expire
        );
        
        $crate->setStorage($storage);
        $crate = serialize($crate);
        $_SESSION['~Crate'] = $crate;
    }

    public static function get($nameTag) {
        $crate = $_SESSION['~Crate'];
        $crate = unserialize($crate);
        $storage = $crate->getStorage();

        if(!isset($storage[$nameTag])) {
            return false;
        }

        $item = $storage[$nameTag];

        if($item['expire'] == true) {
        	echo "have to expire";
            unset($storage[$nameTag]);
        }
        
        $crate->setStorage($storage);
        $crate = serialize($crate);
        $_SESSION['~Crate'] = $crate;
        
        return $item['item'];
    }

}
