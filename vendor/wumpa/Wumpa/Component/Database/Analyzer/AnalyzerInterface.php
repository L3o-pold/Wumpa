<?php

namespace Wumpa\Component\Database\Analyzer;

interface AnalyzerInterface {
	
	public function getTables();
	
	public function getColumns($table);
	
}