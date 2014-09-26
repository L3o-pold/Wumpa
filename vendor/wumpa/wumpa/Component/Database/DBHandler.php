<?php

namespace Wumpa\Component\Database;

use Wumpa\Component\App\App;

class DBHandler extends \PDO {
   
    public function __construct($db) {
    	parent::__construct($db->getConnectionString(), $db->getUser(), $db->getPassword());
    	$this->exec('SET CHARACTER SET utf8');
    	
    	if(App::isWumpaHandlingExcp()) {
    		$this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    	}			
    }

}
