<?php

namespace Wumpa\Component\Database;

use Wumpa\Component\App\App;

/**
 * Handle the database used by the framework
 * Supported databases: MySQl, PgSQL
 *
 * @author Bastien de Luca <dev@b-deluca.com>
 */

class Database {
	
	private $driver;
	private $dbName;
	private $host;
	private $port;
	private $user;
	private $password;
	private $connec = null;
	
	private static $instance = null;
	
	private function __construct($driver, $dbName, $host, $port, $user, $password) {
		$this->setDriver($driver);
		$this->setDbName($dbName);
		$this->setHost($host);
		$this->setUser($user);
		$this->setPort($port);
		$this->setPassword($password);
	}
	
	protected function getConnectionString() {
		$str = '';
		switch( $this->getDriver() ) {
			case 'mysql' : //mysql <==> pgsql
			case 'pgsql' :
				$str = $this->getDriver() . ':host=' . $this->getHost() . ( !is_null( $this->getPort() ) ? ';port=' . $this->getPort() : '' ) . ';dbname=' . $this->getDbName();
				break;
			case 'sqlsrv' :
				$str =  $this->getDriver() . ':Server=' . $this->getHost() . ( !is_null( $this->getPort() ) ',' . $this->getPort() : '' ) . ';Database=' . $this->getDbName();
				break;
			default :
				$str = 'Undefined driver : ' . $this->getDriver();
		}
		return $str;
	}
	
	public function getDriver() {
		return $this->driver;
	}
	public function setDriver($driver) {
		$this->driver = $driver;
		return $this;
	}
	public function getDbName() {
		return $this->dbName;
	}
	public function setDbName($dbName) {
		$this->dbName = $dbName;
		return $this;
	}
	public function getHost() {
		return $this->host;
	}
	public function setHost($host) {
		$this->host = $host;
		return $this;
	}
	public function getUser() {
		return $this->user;
	}
	public function setUser($user) {
		$this->user = $user;
		return $this;
	}
	public function getPort() {
		return $this->port;
	}
	public function setPort($port) {
		$this->port = $port;
		return $this;
	}
	public function getPassword() {
		return $this->password;
	}
	public function setPassword($password) {
		$this->password = $password;
		return $this;
	}
	public function getConnec() {
		return $this->connec;
	}
	public function setConnec($connec) {
		$this->connec = $connec;
		return $this;
	}
		
	public static function init() {
		if (!is_null(App::get(App::DB))) {
			$data = require_once App::get(App::DB);
			self::$instance = new self($data['driver'], $data['dbName'], $data['host'], $data['port'], $data['user'], $data['password']);
		}
	}
	
	public static function get() {
		if (!is_null(self::$instance)) {
			return self::$instance;
		} else {
			return false;
		}
	}
	
	public static function connect() {
		$db = self::get();
		if(is_null($db->getConnec())) {
			try {
				$dbh = new \PDO($db->getConnectionString(), $db->getUser(), $db->getPassword());
				$dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
				$dbh->exec('SET CHARACTER SET utf8');
			} catch (PDOException $e) {
				echo "cake";
			}
			self::get()->setConnec($dbh);
			return true;
		} else {
			return false;
		}
	}
	
	public static function disconnect() {
		if(!is_null(self::get()->getConnec())) {
			self::get()->setConnec(null);
			return true;
		} else {
			return false;
		}
	}
	
}