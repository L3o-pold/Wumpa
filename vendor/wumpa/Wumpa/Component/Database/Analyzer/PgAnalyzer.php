<?php

namespace Wumpa\Component\Database\Analyzer;

use Wumpa\Component\App\App;

/**
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class PgAnalyzer implements AnalyzerInterface {

	public function getTables() {
		$dbh = App::getDatabase()->connect();

		$sql = "
			select table_name
			from information_schema.tables
			where table_type like 'BASE TABLE'
			and table_schema not in ('pg_catalog', 'information_schema')
		";

		$tables = array();
		$sth = $dbh->query($sql);
		while($res = $sth->fetch(\PDO::FETCH_ASSOC)) {
			$tables[] = $res["table_name"];
		}

		$dbh = null;
		return $tables;
	}

	public function getColumns($table) {
		$dbh = App::getDatabase()->connect();

		$sql = "
			select column_name
			from information_schema.columns
			where table_name like '".$table."'
		";

		$cols = array();
		$sth = $dbh->query($sql);
		while ($res = $sth->fetch(\PDO::FETCH_ASSOC)) {
			$cols[] = $res["column_name"];
		}

		$dbh = null;
		return $cols;
	}

	public function getPrimaries($table) {
		$dbh = App::getDatabase()->connect();

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
		$sth = $dbh->query($sql);
		while ($res = $sth->fetch(\PDO::FETCH_ASSOC)) {
			$cols[] = $res["attname"];
		}

		$dbh = null;
		return $cols;
	}

}
