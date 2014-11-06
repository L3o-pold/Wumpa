<?php

namespace Pulsar\Component\Database;

use Pulsar\Component\File\File;
use Pulsar\Component\Path\Path;
use Pulsar\Component\Database\Analyzer\PgAnalyzer;

class ModelGenerator {
	
	private function dirToWrite() {
		include_once '';
	}
	
	public static function write($className, $table) {
		if(Database::get()->getDriver() == "pgsql") {
			$analyzer = new PgAnalyzer();
		} elseif(Database::get()->getDriver() == "mysql") {
			echo "mysql pas encore fait";
			exit;
		}
		
		$file = new File(Path::get(Path::INDEX).$className.".php");
		$file->open();
		$ressource = $file->getResource();
		
		fwrite($ressource, "<?php\n");
		fwrite($ressource, "\n");
		fwrite($ressource, "class ".$className." extends cake {\n");
		fwrite($ressource, "\n");
		
		foreach ($analyzer->getColumns("rank") as $column) {
			fwrite($ressource, "\tprotected \$".$column['column_name'].";\n");
		}
		
		fwrite($ressource, "\n");
		fwrite($ressource, "\tpublic function tableName() {\n");
		fwrite($ressource, "\t\treturn \"".$table."\";\n");
		fwrite($ressource, "\t}\n");
		fwrite($ressource, "\n");
		
		foreach ($analyzer->getColumns("rank") as $column) {
			$attr = strtolower($column['column_name']);
			$uc_attr = ucfirst($attr);
			fwrite($ressource, "\tpublic function get".$uc_attr."() {\n");
			fwrite($ressource, "\t\treturn \$this->".$attr.";\n");
			fwrite($ressource, "\t}\n");
			fwrite($ressource, "\tpublic function set".$uc_attr."($".$attr.") {\n");
			fwrite($ressource, "\t\t\$this->".$attr." = $".$attr.";\n");
			fwrite($ressource, "\t\treturn \$this;\n");
			fwrite($ressource, "\t}\n");
			fwrite($ressource, "\n");
		}
		
		fwrite($ressource, "}\n");
		
		$file->close();
	}
	
}