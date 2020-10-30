<?php

namespace Mvc\File;

class File {
	/**
	 * File constructor
	 * 
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Root path
	 * 
	 * @return string
	 */
	public static function root() {
		return ROOT;
	}

	/**
	 * Directory separator
	 * 
	 * @return string
	 */
	public static function ds() {
		return DS;
	}

	/**
	 * Get file full path
	 * 
	 * @param string $path
	 * @return string $path
	 */
	public static function path($path) {
		$path = static::root() . static::ds() . trim($path, '/');
		$path = str_replace(['/', '\\'], static::ds(), $path);
		
		return $path;
	}

	/**
	 * Check that file exists
	 * 
	 * @param string $path
	 * @return bool
	 */
	public static function exists($path) {
		return file_exists(static::path($path));
	}

	/**
	 * Require file
	 * 
	 * @param string $path
	 * @return mixed
	 */
	public static function require_file($path) {
		if(static::exists($path)) {
			return require_once static::path($path);
		}
	}

	/**
	 * Include file
	 * 
	 * @param string $path
	 * @return mixed
	 */
	public static function include_file($path) {
		if(static::exists($path)) {
			return include static::path($path);
		}
	}

	/**
	 * Require directory
	 * 
	 * @param string $path
	 * @return mixed
	 */
	public static function require_directory($path) {
		$files = array_diff(scandir(static::path($path)), ['.', '..']);
		foreach ($files as $file) {
			$file_path = $path . static::ds() . $file;
			if(static::exists($file_path)) {
				static::require_file($file_path);
			}
		}
	}
}

?>