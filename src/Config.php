<?php

/*
 * Copyright © 2013 Kosh
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Koshatul\Config;

use Yosymfony\Toml\Toml;

class Config {
	protected $mConfigFile = null;
	protected $mData       = null;

	private static $sInstance = null;

	public function __construct($override_path = null)
	{
		$this->mConfigFile = new ConfigFile($override_path);
		if ($this->mConfigFile->isValid()) {
			$this->mData = Toml::Parse($this->mConfigFile->getFilename());
		}
	}

	public function getValue($name) {
		if (!is_null($this->mData)) {
			$name = explode('/', $name);
			$section = array_shift($name);
			$confitem = array_shift($name);
			if (array_key_exists($section, $this->mData)) {
				if (!is_null($confitem) and is_array($this->mData[$section]) and array_key_exists($confitem, $this->mData[$section])) {
					return $this->mData[$section][$confitem];
				} elseif (is_null($confitem)) {
					return $this->mData[$section];
				}
			}
		}
		return null;
	}

	public static function Instance($override_path = null)
	{
		if (is_null(self::$sInstance)) {
			self::$sInstance = new self($override_path);
		}
		return self::$sInstance;
	}

	public static function Get($name) {
		return self::Instance()->getValue($name);
	}

	public static function GetURI($name, $part) {
		switch($part) {
			case PHP_URL_SCHEME:
			case PHP_URL_HOST:
			case PHP_URL_PORT:
			case PHP_URL_USER:
			case PHP_URL_PASS:
			case PHP_URL_PATH:
			case PHP_URL_QUERY:
			case PHP_URL_FRAGMENT:
				return parse_url(self::Get($name), $part);
		}
		return parse_url(self::Get($name));
	}

	public static function GetMySQLURI($name, $part) {
		if (is_null($part)) {
			$part = -1;
		}
		$output = self::GetURI($name, $part);
		if ($output == self::Get($name)) {
			return null;
		}
		switch ($part) {
			case PHP_URL_SCHEME:
			case PHP_URL_HOST:
			case PHP_URL_PORT:
			case PHP_URL_USER:
			case PHP_URL_PASS:
			case PHP_URL_FRAGMENT:
				break;
			case PHP_URL_QUERY:
				parse_str($output, $output);
				break;
			case PHP_URL_PATH:
				$output = substr($output, 1);
				break;
		}
		return $output;
	}

}