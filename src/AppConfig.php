<?php

/*
 * Copyright © 2013 Kosh
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Koshatul\Config;

class AppConfig
{
	protected $_appPrefix;
	protected $_config;

	public function __construct($application_prefix, Config $config)
	{
		$this->_appPrefix = $application_prefix;
		$this->_config = $config;
	}

	public function getValue($name, $defaultValue = null)
	{
		$valueName = $this->_appPrefix.'/'.$name;
		return $this->_config->getValue($valueName, $defaultValue);
	}

	public function getURIValue($name, $part)
	{
		$valueName = $this->_appPrefix.'/'.$name;
		return $this->_config->getURIValue($valueName, $part);
	}

	public function getMySQLURIValue($name, $part)
	{
		$valueName = $this->_appPrefix.'/'.$name;
		return $this->_config->getMySQLURIValue($valueName, $part);
	}

}
