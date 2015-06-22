<?php

namespace Wumpa\Component\Database;

use Wumpa\Component\App\App;
use Wumpa\Component\App\AppIndex;

/**
 * Handle the database used by the framework
 * Supported databases: MySQl, PgSQL, SQLSrv
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class Database {

	private $driver;
	private $dbName;
	private $host;
	private $port;
	private $user;
	private $password;

	private $dbh;

	private $app;

	public function connect() {
		if(($dbh = $this->getDbh()) === null) {
			$dbh = new \PDO($this->getConnectionString(), $this->getUser(), $this->getPassword());
			$dbh->exec('SET CHARACTER SET utf8');

			if(($this->app instanceof AppIndex) && $this->getApp()->isHandlingExcp()) {
				$dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			}
			$this->setDbh($dbh);
		}
		return $dbh;
	}

	private function getConnectionString() {
		$str = '';
		switch($this->getDriver()) {
			case 'mysql' : //mysql <==> pgsql
			case 'pgsql' :
				$str = $this->getDriver() . ':host=' . $this->getHost() . ( !is_null( $this->getPort() ) ? ';port=' . $this->getPort() : '' ) . ';dbname=' . $this->getDbName();
				break;
			case 'sqlsrv' :
				$str =  $this->getDriver() . ':Server=' . $this->getHost() . ( !is_null( $this->getPort() ) ? ',' . $this->getPort() : '' ) . ';Database=' . $this->getDbName();
				break;
			default :
				return false;
		}
		return $str;
	}

	public function __construct($app) {
		$this->setApp($app);

		$data = require $app->getDbFile();

		$this->setDriver($data['driver']);
		$this->setDbName($data['dbName']);
		$this->setHost($data['host']);
		$this->setPort($data['port']);
		$this->setUser($data['user']);
		$this->setPassword($data['password']);
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

	public function getPort() {
		return $this->port;
	}

	public function setPort($port) {
		$this->port = $port;
		return $this;
	}

	public function getUser() {
		return $this->user;
	}

	public function setUser($user) {
		$this->user = $user;
		return $this;
	}

	public function getPassword() {
		return $this->password;
	}

	public function setPassword($password) {
		$this->password = $password;
		return $this;
	}

	private function getDbh() {
		return $this->dbh;
	}

	private function setDbh($dbh) {
		$this->dbh = $dbh;
	}

	public function getApp() {
		return $this->app;
	}

	public function setApp($app) {
		$this->app = $app;
		return $this;
	}

}
