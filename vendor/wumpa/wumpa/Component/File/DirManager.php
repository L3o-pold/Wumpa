<?php

namespace Wumpa\Component\File;

class DirManager {

	/**
	 * Create a new directory in the specified one
	 */
	public static function create($dir, $dirName) {
		// Test if last char of $dir is "/";
		// IF FALSE ==> $dir .= "/";
		mkdir($dir . "");
	}

	/**
	 * Remove a directory, use recursivity to remove sub directory/file
	 */
	public static function delete($dir, $recursiv = false) {

	}

	/**
	 * Rename or move directory
	 */
	public static function rename($dir, $newDirName) {

	}

	/**
	 * Return the OS in order to set the correct file system interraction
	 */
	private static function getOS() {

	}

}
