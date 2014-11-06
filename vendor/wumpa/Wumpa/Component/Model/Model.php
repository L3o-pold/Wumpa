<?php

namespace Wumpa\Component\Model;

use Wumpa\Component\Database\Database;

abstract class Model implements ModelInterface {

	/**
	 * Get all rows of the table and return them in an array of objects
	 * @return multitype:unknown
	 */
	public function getAll() {
		$dbh = Database::connect();

		$sth = $dbh->query("
    		SELECT *
    		FROM ".$this::getTableName()
    	);

		$data = array();
		while($res = $sth->fetch(\PDO::FETCH_ASSOC)) {
			$item = new $this();
			foreach($res as $key=>$val) {
				$item->$key = $val;
			}
			$data[] = $item;
		}

		$dbh = null;
		return $data;
	}
	/**
	 * Attempt to get a single element in the database and put its data in the current object
	 * Null data are returned if nothing is found
	 * @param unknown $id
	 * @return \Wumpa\Component\Model\Model
	 */
	public function getById($id) {
		$dbh = Database::connect();

		$query = "
    		SELECT *
    		FROM ".$this::getTableName()
    	;
		$where = true;
		foreach($this::getPrimaries() as $pk) {
			if($where) {
				$query .= "\nWHERE ".$pk." = '".$id[$pk]."'";
				$where = false;
			} else {
				$query .= "\nAND ".$pk." = '".$id[$pk]."'";
			}
		}

		$sth = $dbh->query($query);
		while($res = $sth->fetch(\PDO::FETCH_ASSOC)) {
			foreach($res as $key=>$val) {
				$this->$key = $val;
			}
		}

		$dbh = null;
		return $this;
	}

	/**
	 * Attempt to add the current object to the database.
	 * No integrity or constraint checks are done when inserting the row.
	 * @return number
	 */
	public function store() {
		$dbh = Database::connect();

		$query = "
			INSERT INTO ".$this->getTableName()."
			VALUES (";
		$first = true;
		foreach ($this as $val) {
			if($first) {
				$query .= "'".$val."'";
				$first = false;
			} else {
				$query .= ", '".$val."'";
			}
		}
		$query .= ")";

		$res = $dbh->exec($query);
		$dbh = null;
		return $res;
	}

	/**
	 * Update the row corresponding to the current object in the database.
	 * No integrity or constraint checks are done when updating the row.
	 * @return number
	 */
	public function update() {
		$dbh = Database::connect();

		$query = "UPDATE ".$this->getTableName()."
				SET ";
		$first = true;
		foreach($this as $column => $val) {
			if(!in_array($column, $this->getPrimaries())) {
				if($first) {
					$query .= $column." = '".$val."'";
					$first = false;
				} else {
					$query .= ", ".$column." = '".$val."'";
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
		$dbh = Database::connect();

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
	public function deleteById($id) {
		$dbh = Database::connect();

		$query = "
			DELETE FROM ".$this->getTableName();
		$where = true;
		foreach($this::getPrimaries() as $pk) {
			if($where) {
				$query .= "\nWHERE ".$pk." = '".$id[$pk]."'";
				$where = false;
			} else {
				$query .= "\nAND ".$pk." = '".$id[$pk]."'";
			}
		}

		$res = $dbh->exec($query);
		$dbh = null;
		return $res;
	}

}
