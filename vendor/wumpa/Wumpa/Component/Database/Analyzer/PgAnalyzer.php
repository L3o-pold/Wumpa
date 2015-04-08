<?php

namespace Wumpa\Component\Database\Analyzer;

use Wumpa\Component\App\App;

/**
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class PgAnalyzer implements AnalyzerInterface {


	/**
	 * Return an array containing the database tables names.
	 */
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

	/**
	 * Return an array containing the columns names from the specified table.
	 */
	public function getCols($table) {
		$dbh = App::getDatabase()->connect();

		$sql = "
			select column_name
			from information_schema.columns
			where table_name = '$table'
		";

		$cols = array();
		$sth = $dbh->query($sql);
		while ($res = $sth->fetch(\PDO::FETCH_ASSOC)) {
			$cols[] = $res["column_name"];
		}

		$dbh = null;
		return $cols;
	}


	public function getPK($table) {
		$dbh = App::getDatabase()->connect();

		$sql = "
			SELECT kcu.column_name
			FROM information_schema.table_constraints AS tc
			JOIN information_schema.key_column_usage AS kcu ON tc.constraint_name = kcu.constraint_name
			WHERE tc.constraint_type = 'PRIMARY KEY'
			AND tc.table_name = '$table'
		";

		$cols = array();
		$sth = $dbh->query($sql);
		while ($res = $sth->fetch(\PDO::FETCH_ASSOC)) {
			$cols[] = $res["column_name"];
		}

		$dbh = null;
		return $cols;
	}


	public function getFK($table) {
		$dbh = App::getDatabase()->connect();

		$sql = "
			SELECT kcu.column_name, ccu.table_name
			FROM information_schema.table_constraints AS tc
			JOIN information_schema.key_column_usage AS kcu ON tc.constraint_name = kcu.constraint_name
			JOIN information_schema.constraint_column_usage AS ccu ON ccu.constraint_name = tc.constraint_name
			WHERE tc.constraint_type = 'FOREIGN KEY'
			AND tc.table_name = '$table'
		";

		$cols = array();
		$sth = $dbh->query($sql);
		while($res = $sth->fetch(\PDO::FETCH_ASSOC)) {
			$cols[$res["column_name"]] = $res["table_name"];
		}

		$dbh = null;
		return $cols;
	}

}
