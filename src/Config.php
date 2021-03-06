<?php

/*
 * Copyright © 2013 Kosh
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Koshatul\Config;

use Yosymfony\Toml\Toml;

class Config
{
    protected $mConfigFile = null;
    protected $mData       = null;

    private static $sInstance = null;

    public function __construct($override_path = null)
    {
        $env_path = getenv('KOSH_CONFIG');
        if ($env_path !== false) {
            $override_path = $env_path;
        }
        $this->mConfigFile = new ConfigFile($override_path);
        if ($this->mConfigFile->isValid()) {
        	if ($this->mConfigFile->isURL()) {
        		$tmpConfig = file_get_contents($this->mConfigFile->getFilename());
        		$this->mData = Toml::Parse($tmpConfig);
        	} else {
	            $this->mData = Toml::Parse($this->mConfigFile->getFilename());
	        }
        }
    }

    public function getConfigFile()
    {
        return $this->mConfigFile;
    }

    public function getValue($name, $defaultValue = null)
    {
        $envname = strtoupper(str_replace('/', '_', $name));
        $envvalue = getenv($envname);
        if ($envvalue !== false) {
            return $envvalue;
        }
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

        return $defaultValue;
    }

    public function setValue($name, $value = null)
    {
        $name = explode('/', $name);
        $section = array_shift($name);
        $confitem = array_shift($name);

        if (array_key_exists($section, $this->mData) and is_array($this->mData[$section]) and array_key_exists($confitem, $this->mData[$section])) {
            // Section and Name exist, change value
            if (!is_null($value)) {
                $this->mData[$section][$confitem] = $value;
            } else {
                unset($this->mData[$section][$confitem]);
            }
        } elseif (array_key_exists($section, $this->mData) and is_array($this->mData[$section])) {
            if (!is_null($value)) {
                $this->mData[$section][$confitem] = $value;
            }
        } elseif (!array_key_exists($section, $this->mData)) {
            $this->mData[$section] = array(
                $confitem => $value,
            );
        }

        return null;
    }

    public function getURIValue($name, $part)
    {
        switch ($part) {
            case PHP_URL_SCHEME:
            case PHP_URL_HOST:
            case PHP_URL_PORT:
            case PHP_URL_USER:
            case PHP_URL_PASS:
            case PHP_URL_PATH:
            case PHP_URL_QUERY:
            case PHP_URL_FRAGMENT:
                return parse_url($this->getValue($name), $part);
        }

        return parse_url($this->getValue($name));
    }

    public function getMySQLURIValue($name, $part)
    {
        if (is_null($part)) {
            $part = -1;
        }
        $output = $this->getURIValue($name, $part);
        if ($output == $this->getValue($name)) {
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

    public static function Instance($override_path = null)
    {
        if (is_null(self::$sInstance)) {
            self::$sInstance = new self($override_path);
        }

        return self::$sInstance;
    }

    public static function Get($name, $defaultValue = null)
    {
        return self::Instance()->getValue($name, $defaultValue);
    }

    public static function Set($name, $value = null)
    {
        return self::Instance()->setValue($name, $value);
    }

    public static function GetURI($name, $part)
    {
        return self::Instance()->getURIValue($name, $part);
    }

    public static function GetMySQLURI($name, $part)
    {
        return self::Instance()->getMySQLURIValue($name, $part);
    }
}
