<?php

/*
 * Copyright © 2013 Kosh
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Koshatul\Config;

class ConfigFile
{
	private $_defaultFilename = '.kosh.config.toml';
	private $_filename = null;

	public function __construct($override_path = null, $defaultFilename = '.kosh.config.toml')
	{
		$this->_defaultFilename = $defaultFilename;
		$this->_filename        = $this->_findConfigFile($override_path);
	}

	public function isValid()
	{
		return (!is_null($this->_filename));
	}

	public function getFilename()
	{
		return $this->_filename;
	}

	protected function _findConfigFile($override_path = null) {
		if (is_null($override_path)) {
			$folder = dirname(__FILE__);
		} else {
			$folder = $override_path;
		}
		$conffile = $this->_followPath($folder);
		if (!is_null($conffile)) {
			return $conffile;
		} else {
			if (is_array($_SERVER) and array_key_exists('HOME', $_SERVER)) {
				$checkfile = $_SERVER['HOME'].DIRECTORY_SEPARATOR.$this->_defaultFilename;
				if (file_exists($checkfile)) {
					return $checkfile;
				} else {
					return null;
				}
			} else {
				return null;
			}
		}
	}

	protected function _followPath($path) {
		if ('' === $path) {
			$path = DIRECTORY_SEPARATOR;
		}
		$checkfile = $path.DIRECTORY_SEPARATOR.$this->_defaultFilename;
		if (file_exists($checkfile)) {
			return $checkfile;
		} else {
			if ($path != DIRECTORY_SEPARATOR) {
				$path = explode(DIRECTORY_SEPARATOR, $path);
				array_pop($path);
				return $this->_followPath(implode(DIRECTORY_SEPARATOR, $path));
			} else {
				return null;
			}
		}
	}
}
