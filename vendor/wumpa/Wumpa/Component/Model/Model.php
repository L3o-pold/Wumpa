<?php

namespace Wumpa\Component\Model;

use Wumpa\Component\App\App;

/**
 * Provide query methodes for model classes.
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
abstract class Model implements ModelInterface {

	const ASC = "ASC";
	const DESC = "DESC";

	const LIKE = "LIKE";
	const NOTLIKE = "NOT LIKE";

	const IS = "IS";
	const ISN = "IS NOT";

	const IN = "IN";
	const NIN = "NOT IN";

	const EQ = "=";
	const NEQ = "<>";
	const GT = ">";
	const LT = "<";
	const GOET = ">=";
	const LOET = "<=";

	const DEP = 1;
	const FULL = 0;
	const COMP = -1;


	/**
	 * Constructor can be used to retrieve an object by its id
	 */
	public function __construct(array $id = null, $tableName = false) {
		if(!is_null($id))
			$this->getById($id, $tableName);
	}

	/**
	 * Get all rows of the table and return them in an array of objects
	 * Restrictions are not allowed in this function (use getByCols instead)
	 * ORDER BY is possible and facultative, just specify a array like:
	 * array(
	 *     "columnToOrder" => "way (ASC or DESC)",
	 *     "columnToOrder" => "way (ASC or DESC)"
	 * );
	 * It is also possible to define a limit and offset.
	 */
	public function getAll($tableName = false, array $orders = null, $limit = null, $offset = null) {
		$dbh = App::getDatabase()->connect();

		if(!$tableName)
			$query = "SELECT *
					  FROM ".$this::getTableName();
		else
			$query = "SELECT t.*, p.relname as tablename
					  FROM ".$this::getTableName()." t, pg_class p
					  WHERE t.tableoid = p.oid";

		if($fl = !is_null($orders)) {
			$query .= "\nORDER BY ";
			foreach($orders as $col => $way) {
				$query .= (($fl) ? "" : ", ").$col ." ". $way;
				$fl = false;
			}
		}

		$query .= (!is_null($limit)) ? "\nLIMIT ".$limit : "";
		$query .= (!is_null($offset)) ? "\nOFFSET ".$offset : "";

		$sth = $dbh->query($query);

		$data = array();
		while($res = $sth->fetch(\PDO::FETCH_ASSOC)) {
			$item = new $this();
			foreach($res as $key => $val) {
				$item->$key = $val;
			}
			$data[] = $item;
		}

		$dbh = null;
		return $data;
	}

	/**
	 * Attempt to count elements in a table
	 * Restrictions are not mandatory.
	 */
	public function count(array $ands = null, array $ors = null) {
		$dbh = App::getDatabase()->connect();

		$query = "SELECT COUNT(*)
				  FROM ".$this::getTableName();

		$fl = !(is_null($ands)&&is_null($ors));

		if(!is_null($ands)) {
			foreach($ands as $col => $and) {
				$query .= "\n".(($fl) ? "WHERE" : "AND")." ".$col." ".$and['operator']." ".(($and['value'] === "null") ? $and['value'] : "'".$and['value']."'");
				$fl = false;
			}
		}

		if(!is_null($ors)) {
			foreach($ors as $col => $or) {
				$query .= "\n".(($fl) ? "WHERE" : "OR")." ".$col." ".$or['operator']." ".(($or['value'] === "null") ? $or['value'] : "'".$or['value']."'");
				$fl = false;
			}
		}

		$sth = $dbh->query($query);
		$data = $sth->fetch(\PDO::FETCH_ASSOC)['count'];

		$dbh = null;
		return (int) $data;
	}

	/**
	 * Attempt to get a single element in the database and put its data in the current object
	 * Also return the current object.
	 * Can retrieve the tableName of the object;
	 * Null data are returned if nothing is found
	 */
	public function getById(array $id, $tableName = false) {
		$dbh = App::getDatabase()->connect();

		if($fl = !$tableName)
			$query = "SELECT *
	    			  FROM ".$this::getTableName();
		else
			$query = "SELECT t.*, p.relname as tablename
					  FROM ".$this::getTableName()." t, pg_class p
					  WHERE t.tableoid = p.oid";

		foreach($this::getPrimaries() as $pk) {
			$query .= "\n".(($fl) ? "WHERE" : "AND")." ".$pk." = '".$id[$pk]."'";
			$fl = false;
		}

		$sth = $dbh->query($query);
		$res = $sth->fetch(\PDO::FETCH_ASSOC);

		foreach($res as $key=>$val) {
			$this->$key = $val;
		}

		$dbh = null;
		return $this;
	}

	/**
	 * Attempt to retrieve array of item corresponding to the given conditions.
	 * It's possible to return the tableName of each item.
	 */
	public function getByCols($tableName = false, array $ands = null, array $ors = null, array $orders = null, $limit = null, $offset = null) {
		$dbh = App::getDatabase()->connect();

		if($fl = !$tableName)
			$query = "SELECT * FROM ".$this::getTableName();
		else
			$query = "SELECT t.*, p.relname as tablename
					  FROM ".$this::getTableName()." t, pg_class p
					  WHERE t.tableoid = p.oid";

		if(!is_null($ands)) {
			foreach($ands as $col => $and) {
				$query .= "\n".(($fl) ? "WHERE" : "AND")." ".$col." ".$and['operator']." ".(($and['value'] === "null") ? $and['value'] : "'".$and['value']."'");
				$fl = false;
			}
		}

		if(!is_null($ors)) {
			foreach($ors as $col => $or) {
				$query .= "\n".(($fl) ? "WHERE" : "OR")." ".$col." ".$or['operator']." ".(($or['value'] === "null") ? $or['value'] : "'".$or['value']."'");
				$fl = false;
			}
		}

		if($fl = !is_null($orders)) {
			$query .= "\nORDER BY ";
			foreach($orders as $col => $way) {
				$query .= (($fl) ? "" : ", ").$col ." ". $way;
				$fl = false;
			}
		}

		$query .= (!is_null($limit)) ? "\nLIMIT ".$limit : "";
		$query .= (!is_null($offset)) ? "\nOFFSET ".$offset : "";

		$sth = $dbh->query($query);

		$data = array();
		while($res = $sth->fetch(\PDO::FETCH_ASSOC)) {
			$item = new $this();
			foreach($res as $key => $val) {
				$item->$key = $val;
			}
			$data[] = $item;
		}

		$dbh = null;
		return $data;
	}

	/**
	 * Attempt to add the current object to the database.
	 * No integrity or constraint checks are done when inserting the row.
	 * @return number
	 */
	public function store() {
		$dbh = App::getDatabase()->connect();

		$query = "INSERT INTO ".$this->getTableName()."
				  VALUES (";

		$fl = true;
		foreach ($this as $val) {
			$query .= (($fl) ? "" : ", ").(($val == "" || is_null($val)) ? "default" : "'".$val."'");
			$fl = false;
		}

		$query .= ") \nRETURNING ";
		$fl = true;
		foreach($this->getPrimaries() as $pk) {
			$query .= ($fl) ? $pk : ", ".$pk;
			$fl = false;
		}

		$sth = $dbh->query($query);
		$data = $sth->fetch(\PDO::FETCH_ASSOC);

		$dbh = null;
		return $data;
	}

	/**
	 * Update the row corresponding to the current object in the database.
	 * No integrity or constraint checks are done when updating the row.
	 */
	public function update() {
		$dbh = App::getDatabase()->connect();

		$query = "UPDATE ".$this->getTableName()."
				  SET ";

		$fl = true;
		foreach($this as $column => $val) {
			if(!in_array($column, $this->getPrimaries())) {
				$query .= (($fl) ? "" : ", ").$column." = ".((is_bool($val)) ? (($val) ? "TRUE" : "FALSE") : ((is_null($val) || $val == "") ? "null" : "'".$val."'"));
				$fl = false;
			}
		}

		$fl = true;
		foreach($this as $column => $val) {
			if(in_array($column, $this->getPrimaries())) {
				$query .= "\n".(($fl) ? "WHERE" : "AND")." ".$column." = '".$val."'";
				$fl = false;
			}
		}

		$res = $dbh->exec($query);
		$dbh = null;
		return $res;
	}

	/**
	 * Delete the current element in the database represented by the object
	 */
	public function delete() {
		$dbh = App::getDatabase()->connect();

		$query = "DELETE FROM ".$this->getTableName();

		$fl = true;
		foreach($this as $column => $val) {
			if(in_array($column, $this->getPrimaries())) {
				$query .= "\n".(($fl) ? "WHERE" : "AND")." ".$column." = '".$val."'";
				$fl = false;
			}
		}

		$res = $dbh->exec($query);
		$dbh = null;
		return $res;
	}

	/**
	 * Delete the element represented by the id in the database
	 */
	public function deleteById(array $id) {
		$dbh = App::getDatabase()->connect();

		$query = "DELETE FROM ".$this->getTableName();

		$fl = true;
		foreach($this::getPrimaries() as $pk) {
			$query .= "\n".(($fl) ? "WHERE" : "AND")." ".$pk." = '".$id[$pk]."'";
			$fl = false;
		}

		$res = $dbh->exec($query);
		$dbh = null;
		return $res;
	}

	/**
	 * Get the specified Item from the database and resolve all its dependencies
	 * and compositions.
	 * Will return a "tree" of objects based on the database structure
	 *
	 * The dependencies are injected in the foreign key attributes
	 * The Compositions are injected as array of object
	 */
	public function getDataTree(array $id, $way = 0) {
		$this->getById($id);

		if($way === 0 || $way === 1) {
			foreach($this as $attrib => $value) {
				foreach($this::getDependencies() as $key => $class) {
					if($attrib === $key && !is_null($this->$attrib)) {
						$id = array();
						foreach($class::getPrimaries() as $pk) {
							$id[$pk] = $this->$attrib;
						}
						$this->$attrib = new $class($id);
					}
				}
			}
		}

		if($way === 0 || $way === -1) {
			foreach($this::getCompositions() as $class => $id) {
				$attrName = (substr($class, -1) === 's') ? lcfirst($class) : lcfirst($class."s");
				$where = array(
					$id => array()
				);
				foreach($this::getPrimaries() as $pk) {
					$where[$id]["operator"] = $this::EQ;
					$where[$id]["value"] = $this->$pk;
				}
				$c = new $class();
				$this->$attrName = $c->getByCols(false, $where);
			}
		}

		return($this);
	}

}
