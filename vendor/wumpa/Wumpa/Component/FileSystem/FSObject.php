<?php

namespace Wumpa\Component\FileSystem;

/**
 * Provide methodes to manipulate file and dirs.
 *
 * @author Bastien de Luca <dev@de-luca.io>
 */
class FSObject {

	private $name;

	/**
	*
	* @param boolean $overwrite
	* @return number|boolean
	*/
	public function create($overwrite = false) {
		if($this instanceof File) {
			if($overwrite || !file_exists($this->getName())) {
				return file_put_contents($this->getName(), '');
			}
		} elseif($this instanceof Dir) {
			if(!file_exists($this->getName())) {
				return mkdir($this->getName());
			}
		}
		return false;
	}

	/**
	*
	* @param boolean $recursive
	* @return boolean
	*/
	public function delete($recursive = false) {
		if(file_exists($this->getName())) {
			if($this instanceof File) {
				return unlink($this->getName());
			} elseif($this instanceof Dir) {
				if(!$recursive) {
					return rmdir($this->getName());
				} else {
					return self::recursDel($this->getName());
				}
			}
		}
		return false;
	}

	/**
	* Attempt to rename or move a file or directory
	* @param string $newName
	* @return boolean
	*/
	public function rename($newName, $overwrite = true) {
		if(file_exits($this->getName())) {
			if($overwrite || !file_exists($newName)) {
				if(rename($this->getName(), $newName)) {
					$this->setName($newName);
					return true;
				}
			}
		}
		return false;
	}

	/**
	*
	* @param string $dir
	*/
	private static function recursDel($dir) {
		foreach (scandir($file) as $file) {
			if(is_file($file)) {
				unlink($file);
			} else {
				self::recursDel($file);
				rmdir($file);
			}
		}
	}

	public function __construct($name) {
		$this->setName($name);
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
		return $this;
	}

}
