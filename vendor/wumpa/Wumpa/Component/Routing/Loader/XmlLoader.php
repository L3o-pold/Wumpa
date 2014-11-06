<?php

namespace Wumpa\Component\Routing\Loader;

use Wumpa\Component\Routing\RoutingTable;
use Wumpa\Component\Routing\Route;
use Wumpa\Component\App\App;
use Wumpa\Component\Exception\Exception\FileNotFoundException;
use Wumpa\Component\Exception\Exception\ConfigErrorException;

class XmlLoader implements LoaderInterface {
	
	private $file;
	
	public function __construct($file) {
		$this->setFile($file);
	}
	
	public function getFile() {
		return $this->file;
	}
	public function setFile($file) {
		$this->file = $file;
		return $this;
	}
	
	private function startElement($parser, $name, $attrs) {
		$GLOBALS['currentNode'] = strtolower($name);
		if(strtolower($name) == "route") {
			if(isset($GLOBALS['route'])) {
				unset($GLOBALS['route']);
			}
			$GLOBALS['route'] = array();
		}
	}
	
	private function endElement($parser, $name) {
		$GLOBALS['currentNode'] = null;
		if(strtolower($name) == "route") {
			if(!isset($GLOBALS['route']['requirements'])) {
				$GLOBALS['route']['requirements'] = null;
			}
			$GLOBALS['routingTable']->add(new Route($GLOBALS['route']['name'], $GLOBALS['route']['path'], $GLOBALS['route']['controller'], $GLOBALS['route']['requirements']));
		}
	}
	
	private function characters($parser, $data) {
		if(isset($GLOBALS['route']['requirements']) && !is_null($GLOBALS['currentNode'])) {
			if(preg_match("/{(.*?)}/", $data)) {
				$GLOBALS['route']['requirements'][$GLOBALS['currentNode']] = explode(", ", str_replace(array("{", "}"), "", $data));
			} else {
				$GLOBALS['route']['requirements'][$GLOBALS['currentNode']] = $data;
			}
		}
	
		switch($GLOBALS['currentNode']) {
			case "name":
				$GLOBALS['route']['name'] = $data;
				break;
			case "path":
				$GLOBALS['route']['path'] = $data;
				break;
			case "controller":
				$GLOBALS['route']['controller'] = $data;
				break;
			case "requirements":
				$GLOBALS['route']['requirements'] = array();
				break;
		}
	}
	
	public function load() {
		if(file_exists(App::get(App::CONFIG).$this->getFile())) {
			$source = file_get_contents(App::get(Path::CONFIG).$this->getFile());
		} else {
			throw new FileNotFoundException(App::get(Path::CONFIG).$this->getFile());
		}
		
		if($source == "" || is_null($source)) {
			throw new ConfigErrorException("No routes defined in the routes config file");
		}
		
		$GLOBALS['routingTable'] = new RoutingTable();
		$GLOBALS['currentNode'] = "";
		
		$parser = xml_parser_create();
		xml_set_element_handler($parser, array($this, "startElement"), array($this, "endElement"));
		xml_set_character_data_handler($parser, array($this, "characters"));
		
		xml_parse($parser, $source, true);
		xml_parser_free($parser);
		
		$routingTable = $GLOBALS['routingTable'];
		
		unset($GLOBALS['currentNode']);
		unset($GLOBALS['route']);
		unset($GLOBALS['routingTable']);
		
		return $routingTable;
	}
	
}