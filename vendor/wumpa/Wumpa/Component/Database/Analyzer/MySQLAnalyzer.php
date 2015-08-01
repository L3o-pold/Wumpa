<?php

namespace Wumpa\Component\Database\Analyzer;

use PDO;
use Wumpa\Component\App\App;

/**
 * MySQL analyser
 *
 * @author Léopold Jacquot <leopold.jacquot@gmail.com>
 */
class MySQLAnalyzer implements AnalyzerInterface {

    /**
     * Return an array containing the database tables names.
     *
     * @return array MySQL table names
     */
    public function getTables() {
        $dbh = App::getDatabase()->connect();

        $sql = '
			SELECT TABLE_NAME as table_name
			FROM information_schema.tables
			WHERE TABLE_SCHEMA="' . App::getDatabase()->getDbName() . '"
		';

        $tables = [];
        $sth    = $dbh->query($sql);

        while ($res = $sth->fetch(PDO::FETCH_ASSOC)) {
            $tables[] = $res['table_name'];
        }

        $dbh = null;

        return $tables;
    }

    /**
     * Return an array containing the columns names from the specified table.
     *
     * @param $table
     *
     * @return array Columns names from a MySQL table
     */
    public function getCols($table) {
        $dbh = App::getDatabase()->connect();

        $sql = '
			SELECT COLUMN_NAME as column_name
			FROM INFORMATION_SCHEMA.COLUMNS
			WHERE TABLE_SCHEMA="' . App::getDatabase()->getDbName() . '"
			AND TABLE_NAME="' . $table . '"';

        $cols = [];
        $sth  = $dbh->query($sql);

        while ($res = $sth->fetch(PDO::FETCH_ASSOC)) {
            $cols[] = $res["column_name"];
        }

        $dbh = null;

        return $cols;
    }

    /**
     * @param $table
     *
     * @return array
     */
    public function getPK($table) {
        $dbh = App::getDatabase()->connect();

        $sql = '
			SELECT COLUMN_NAME as column_name
			FROM information_schema.key_column_usage
			WHERE CONSTRAINT_NAME="PRIMARY"
			AND TABLE_NAME="' . $table . '"
			AND CONSTRAINT_SCHEMA="' . App::getDatabase()->getDbName() . '"
		';

        $cols = [];
        $sth  = $dbh->query($sql);

        while ($res = $sth->fetch(PDO::FETCH_ASSOC)) {
            $cols[] = $res['column_name'];
        }

        $dbh = null;

        return $cols;
    }

    /**
     * @param $table
     *
     * @return array
     */
    public function getFK($table) {
        $dbh = App::getDatabase()->connect();

        $sql = '
			SELECT COLUMN_NAME as column_name, TABLE_NAME as table_name
			FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
			WHERE REFERENCED_TABLE_NAME="' . $table . '"
			AND CONSTRAINT_SCHEMA="' . App::getDatabase()->getDbName() . '"
		';

        $cols = [];
        $sth  = $dbh->query($sql);

        while ($res = $sth->fetch(PDO::FETCH_ASSOC)) {
            $cols[$res['column_name']] = $res['table_name'];
        }

        $dbh = null;

        return $cols;
    }

}
