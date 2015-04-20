<?php

namespace Wumpa\Component\Exception\Exception;

/**
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class FileNotFoundException extends \Exception {

	private $fileNotFound;

	public function __construct($fileNotFound, $message = "File cannot be found", $code = null, $previous = null) {
		$this->setFile($fileNotFound);
		parent::__construct($message, $code, $previous);
	}

	public function getFileNotFound() {
		return $this->fileNotFound;
	}

	public function setFileNotFound($fileNotFound) {
		$this->fileNotFound = $file;
		return $this;
	}

}
