<?php

namespace Wumpa\Component\File;

class File {
	
	const CREATE = "create";
	const ADD = "add";
	
	private $filename;
	private $resource = null;
	private $openMode = null;
	
	public function __construct($filename) {
		$this->setFilename($filename);
	}
	
	public function getFilename() {
		return $this->filename;
	}
	public function setFilename($filename) {
		$this->filename = $filename;
		return $this;
	}
	public function getResource() {
		return $this->resource;
	}
	public function setResource($resource) {
		$this->resource = $resource;
		return $this;
	}
	public function getOpenMode() {
		return $this->openMode;
	}
	public function setOpenMode($openMode) {
		$this->openMode = $openMode;
		return $this;
	}
		
	public function open($mode = "create") {
		if(is_null($this->getResource()) && is_null($this->getOpenMode())) {
			switch($mode) {
				case "create":
					$this->setResource(fopen($this->getFilename(), "w+"));
					$this->setOpenMode($mode);
					break;
				case "add":
					$this->setResource(fopen($this->getFilename(), "a+"));
					$this->setOpenMode($mode);
					break;
				default:
					echo "cake";
					break;
			}
			return true;
		} else {
			return false;
		}
	}
	
	public function close() {
		if(!is_null($this->getResource()) && !is_null($this->getOpenMode())) {
			fclose($this->getResource());
			$this->setResource(null);
			$this->setOpenMode(null);
			return true;
		} else {
			return false;
		}
	}
	
}