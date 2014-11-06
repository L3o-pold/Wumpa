<?php

namespace Wumpa\Component\Database\Analyzer;

use Wumpa\Component\Database\Database;

class PgAnalyzer implements AnalyzerInterface {

	public function getTables() {
		$dbh = Database::connect();

		$sql = "
			select table_name
			from information_schema.tables
			where table_type like 'BASE TABLE'
			and table_schema not in ('pg_catalog', 'information_schema')
		";

		$tables = array();
		$sth = $dbh->query($sql);
		while($res = $sth->fetch(PDO::FETCH_ASSOC)) {
			var_dump($res);
		}
		/*
		$dbh = null;
		return $tables;
		*/
	}

	public function getColumns($table) {
		$dbh = Database::connect();

		$sql = "
			select column_name, data_type, is_nullable, column_default, character_maximum_length, numeric_precision
			from information_schema.columns
			where table_name like '".$table."'
		";

		$cols = array();

		foreach ($dbh->query($sql) as $row) {
			$cols[] = array(
				"column_name" => $row['column_name'],
				"data_type" => $row['data_type'],
				"is_nullable" => $row['is_nullable'],
				"column_default" => $row['column_default'],
				"character_maximum_length" => $row['character_maximum_length'],
				"numeric_precision" => $row['numeric_precision'],
			);
		}

		$dbh = null;
		return $cols;
	}

	public function getPrimaries($table) {
		$dbh = Database::connect();

		$sql = "
			SELECT
			pg_attribute.attname,
			format_type(pg_attribute.atttypid, pg_attribute.atttypmod)
			FROM pg_index, pg_class, pg_attribute
			WHERE
			pg_class.oid = '".$table."'::regclass AND
			indrelid = pg_class.oid AND
			pg_attribute.attrelid = pg_class.oid AND
			pg_attribute.attnum = any(pg_index.indkey)
			AND indisprimary
		";

		$cols = array();

		foreach ($dbh->query($sql) as $row) {
			$cols[] = $row["attname"];
		}

		$dbh = null;
		return $cols;
	}

}
