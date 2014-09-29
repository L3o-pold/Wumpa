<?php

namespace Wumpa\Component\Database\Analyzer;

use Wumpa\Component\Database\Database;

class PgAnalyzer implements AnalyzerInterface {
	
	public function getTables() {
		Database::connect();
		
		$sql = "
			select table_name
			from information_schema.tables
			where table_type like 'BASE TABLE'
			and table_schema not in ('pg_catalog', 'information_schema')
		";
		
		$tables = array();
		
		foreach (Database::get()->getConnec()->query($sql) as $row) {
			$tables[] = $row['table_name'];
		}
		
		return $tables;
	}
	
	public function getColumns($table) {
		Database::connect();
		
		$sql = "
			select column_name, data_type, is_nullable, column_default, character_maximum_length, numeric_precision
			from information_schema.columns
			where table_name like '".$table."'
		";
		
		$cols = array();
		
		foreach (Database::get()->getConnec()->query($sql) as $row) {
			$cols[] = array(
				"column_name" => $row['column_name'],
			);
		}
		
		return $cols;
	}
	
}