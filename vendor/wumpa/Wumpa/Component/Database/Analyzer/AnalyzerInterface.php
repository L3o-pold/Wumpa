<?php

namespace Wumpa\Component\Database\Analyzer;

/**
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
interface AnalyzerInterface {

	public function getTables();
	public function getColumns($table);
	public function getPrimaries($table);

}
