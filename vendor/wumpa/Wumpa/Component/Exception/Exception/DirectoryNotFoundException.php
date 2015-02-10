<?php

namespace Wumpa\Component\Exception\Exception;

/**
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class DirectoryNotFoundException extends \Exception {

	private $directory;

	public function __construct($directory, $message = "Directory cannot be found", $code = null, $previous = null) {
		$this->setDirectory($directory);
		parent::__construct($message, $code, $previous);
	}

	public function getDirectory() {
		return $this->directory;
	}
	
	public function setDirectory($directory) {
		$this->directory = $directory;
		return $this;
	}

}
