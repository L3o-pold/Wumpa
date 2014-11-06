<?php

namespace Wumpa\Component\Exception\Exception;

class FileNotFoundException extends \Exception {

	private $file;

	public function __construct($file, $message = "File cannot be found", $code = null, $previous = null) {
		$this->setFile($file);
		parent::__construct($message, $code, $previous);
	}

	public function getFile() {
		return $this->file;
	}
	public function setFile($file) {
		$this->file = $file;
		return $this;
	}

}
