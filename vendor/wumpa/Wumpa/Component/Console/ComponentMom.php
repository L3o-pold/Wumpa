<?php

namespace Wumpa\Component\Console;

class ComponentMom {

	public static $win_os = array("WIN32", "WINNT", "Windows");
	private $os;
	private $projectPath = null;

	public function __construct() {
		$this->setOs(PHP_OS);
	}

	public function getOs() {
		return $this->os;
	}

	public function setOs($os) {
		$this->os = $os;
		return $this;
	}

	public function getProjectPath() {
		return $this->projectPath;
	}

	public function setProjectPath($value) {
		$this->projectPath = $value;
		return $this;
	}

	public function clear() {
		if(in_array($this->getOs(), self::$win_os)) {
			system("cls");
		} else {
			system("clear");
		}
	}

}
