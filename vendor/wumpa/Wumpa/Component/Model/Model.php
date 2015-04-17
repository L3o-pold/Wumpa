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

	const IN = "IN";
	const NIN = "NOT IN";

	const EQ = "=";
	const NEQ = "<>";
	const GT = ">";
	const LT = "<";
	const GOET = ">=";
	const LOET = "<=";


	public function __construct(array $id = null) {
		if(!is_null($id))
			$this->getById($id);
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
	 *
	 * @return multitype:unknown
	 */
	public function getAll($tableName = false, array $orders = null, $limit = null, $offset = null) {
		$dbh = App::getDatabase()->connect();

		if(!$tableName) {
			$query = "
				SELECT *
				FROM ".$this::getTableName();
		} else {
			$query = "
				SELECT t.*, p.relname as tablename
				FROM ".$this::getTableName()." t, pg_class p
				WHERE t.tableoid = p.oid";
		}

		if(!is_null($orders)) {
			$query .= "\nORDER BY ";
			$first = true;
			foreach($orders as $col => $way) {
				if($first) {
					$query .= $col ." ". $way;
					$first = false;
				} else {
					$query .= ", ".$col." ".$way;
				}
			}
		}

		if(!is_null($limit))
			$query .= "\nLIMIT ".$limit;

		if(!is_null($offset))
			$query .= "\nOFFSET ".$offset;


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
	 * Allow to count elements in a table
	 * Restrictions are facultative.
	 */
	public function count(array $ands = null, array $ors = null) {
		$dbh = App::getDatabase()->connect();

		$query = "
			SELECT COUNT(*)
			FROM ".$this::getTableName();

		$firstLine = !is_null($ands) || !is_null($ors);

		if(!is_null($ands)) {
			foreach($ands as $col => $and) {
				if($firstLine) {
					$query .= "\nWHERE ".$col." ".$and['operator']." '".$and['value']."'";
					$firstLine = false;
				} else {
					$query .= "\AND ".$col." ".$and['operator']." '".$and['value']."'";
				}
			}
		}

		if(!is_null($ors)) {
			foreach($ors as $col => $or) {
				if($firstLine) {
					$query .= "\nWHERE ".$col." ".$or['operator']." '".$or['value']."'";
					$firstLine = false;
				} else {
					$query .= "\nOR ".$col." ".$or['operator']." '".$or['value']."'";
				}
			}
		}

		$sth = $dbh->query($query);
		$data = $sth->fetch(\PDO::FETCH_ASSOC)['count'];

		$dbh = null;

		return (int) $data;
	}

	/**
	 * Attempt to get a single element in the database and put its data in the current object
	 * Null data are returned if nothing is found
	 *
	 * @param unknown $id
	 * @return \Wumpa\Component\Model\Model
	 */
	public function getById(array $id) {
		$dbh = App::getDatabase()->connect();

		$query = "
    		SELECT *
    		FROM ".$this::getTableName()
    	;

		$firstLine = true;
		foreach($this::getPrimaries() as $pk) {
			if($firstLine) {
				$query .= "\nWHERE ".$pk." = '".$id[$pk]."'";
				$firstLine = false;
			} else {
				$query .= "\nAND ".$pk." = '".$id[$pk]."'";
			}
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
	 *
	 *
	 */
	public function getByCols($tableName = false, array $ands = null, array $ors = null, array $orders = null, $limit = null, $offset = null) {

		$dbh = App::getDatabase()->connect();

		if(!$tableName) {
			$query = "
				SELECT *
				FROM ".$this::getTableName();
		} else {
			$query = "
				SELECT t.*, p.relname as tablename
				FROM ".$this::getTableName()." t, pg_class p
			";
		}

		$firstLine = (!is_null($ands))||(!is_null($ors));

		if($tableName) {
			$query .= "\nWHERE t.tableoid = p.oid";
			$firstLine = false;
		}

		if(!is_null($ands)) {
			foreach($ands as $col => $and) {
				if($firstLine) {
					$query .= "\nWHERE ".$col." ".$and['operator']." '".$and['value']."'";
					$firstLine = false;
				} else {
					$query .= "\nAND ".$col." ".$and['operator']." '".$and['value']."'";
				}
			}
		}

		if(!is_null($ors)) {
			foreach($ors as $col => $or) {
				if($firstLine) {
					$query .= "\nWHERE ".$col." ".$or['operator']." '".$or['value']."'";
					$firstLine = false;
				} else {
					$query .= "\nOR ".$col." ".$or['operator']." '".$or['value']."'";
				}
			}
		}

		if(!is_null($orders)) {
			$query .= "\nORDER BY ";
			$first = true;
			foreach($orders as $col => $way) {
				if($first) {
					$query .= $col ." ". $way;
					$first = false;
				} else {
					$query .= ", ".$col." ".$way;
				}
			}
		}

		if(!is_null($limit))
		$query .= "\nLIMIT ".$limit;

		if(!is_null($offset))
		$query .= "\nOFFSET ".$offset;


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

		$query = "
			INSERT INTO ".$this->getTableName()."
			VALUES (";
		$first = true;
		foreach ($this as $val) {
			if($first) {
				if(is_null($val))
					$query .= "default";
				else
					$query .= "'".$val."'";

				$first = false;
			} else {
				if(is_null($val))
					$query .= ", default";
				else
					$query .= ", '".$val."'";
			}
		}
		$query .= ")";
		$query .= "\nRETURNING ";
		$first = true;
		foreach($this->getPrimaries() as $pk) {
			if($first) {
				$query .= $pk;
				$first = false;
			} else
				$query .= ", ".$pk;
		}

		$sth = $dbh->query($query);

		$data = $sth->fetch(\PDO::FETCH_ASSOC);

		$dbh = null;
		return $data;
	}

	/**
	 * Update the row corresponding to the current object in the database.
	 * No integrity or constraint checks are done when updating the row.
	 * @return number
	 */
	public function update() {
		$dbh = App::getDatabase()->connect();

		$query = "UPDATE ".$this->getTableName()."
				SET ";
		$first = true;
		foreach($this as $column => $val) {
			if(!in_array($column, $this->getPrimaries())) {
				if($first) {
					$query .= (is_null($val)) ? $column." = null" : $column." = '".$val."'";
					$first = false;
				} else {
					$query .= (is_null($val)) ? ", ".$column." = null" : ", ".$column." = '".$val."'";
				}
			}
		}
		$where = true;
		foreach($this as $column => $val) {
			if(in_array($column, $this->getPrimaries())) {
				if($where) {
					$query .= "\nWHERE ".$column." = '".$val."'";
					$where = false;
				} else {
					$query .= "\nAND ".$column." = '".$val."'";
				}
			}
		}

		$res = $dbh->exec($query);
		$dbh = null;
		return $res;
	}

	/**
	 * Delete the current element in the database represented by the object
	 * @return number
	 */
	public function delete() {
		$dbh = App::getDatabase()->connect();

		$query = "
			DELETE FROM ".$this->getTableName();
		$where = true;
		foreach($this as $column => $val) {
			if(in_array($column, $this->getPrimaries())) {
				if($where) {
					$query .= "\nWHERE ".$column." = '".$val."'";
					$where = false;
				} else {
					$query .= "\nAND ".$column." = '".$val."'";
				}
			}
		}

		$res = $dbh->exec($query);
		$dbh = null;
		return $res;
	}

	/**
	 * Delete the element represented by the id in the database
	 *
	 * @param unknown $id
	 * @return number
	 */
	public function deleteById(array $id) {
		$dbh = App::getDatabase()->connect();

		$query = "
			DELETE FROM ".$this->getTableName();

		$firstLine = true;
		foreach($this::getPrimaries() as $pk) {
			if($firstLine) {
				$query .= "\nWHERE ".$pk." = '".$id[$pk]."'";
				$firstLine = false;
			} else {
				$query .= "\nAND ".$pk." = '".$id[$pk]."'";
			}
		}

		$res = $dbh->exec($query);
		$dbh = null;
		return $res;
	}


	/**
	 * Get the spcified Item from the database and resolve all its dependencies
	 * and compositions.
	 * Will return a "tree" of objects based on the database structure
	 *
	 * The dependencies are injected in the foreign key attributes
	 * The Compositions are injected as array of object
	 */
	public function getDataTree(array $id) {
		$this->getById($id);

		foreach($this as $attrib => $value) {
			foreach($this::getDependencies() as $key => $class) {
				if($attrib === $key) {
					$id = array();
					foreach($class::getPrimaries() as $pk) {
						$id[$pk] = $this->$attrib;
					}
					$this->$attrib = new $class($id);
				}
			}
		}

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

		return($this);
	}

	/**
	 * Attempt to update the DataTree by updating all already existing objects
	 * and storing all not already in the database.
	 *
	 * This methodes checks if primary keys exists to determine if object must be
	 * inserted or updated
	 */
	public function saveDataTree() {

	}

	/**
	 * Attempt to remove the whole DataTree from the database.
	 * Use with care!!
	 */
	public function deleteDataTree() {

	}

}
