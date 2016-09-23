<?php

/*
 * Copyright © 2013 Kosh
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Koshatul\Config;

use PHPUnit_Framework_TestCase;

class ConfigFileTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->configFile = new ConfigFile(__DIR__);
    }

    public function testFindConfigFile()
    {
        $filename = new ConfigFile(__DIR__);
        $expectedLocation = str_replace('suite/Koshatul/Config', '', __DIR__);
        $this->assertEquals($expectedLocation . '.kosh.config.toml', $filename->getFilename(), 'Check Test File Location');
        $configSpecifyFile = new ConfigFile($expectedLocation . '.kosh.config.toml');
        $this->assertEquals($expectedLocation . '.kosh.config.toml', $configSpecifyFile->getFilename(), 'Check Test File Location');
    }

    public function testFindGlobalFile()
    {
        if (file_exists($_SERVER['HOME'] . DIRECTORY_SEPARATOR . '.kosh.config.toml')) {
            $filename = new ConfigFile('/');
            $this->assertEquals(true, $filename->isValid(), '[001] Test that home directory config file is valid (Home Config Exists)');
            $filename = new ConfigFile();
            $this->assertEquals(true, $filename->isValid(), '[002] Test that config file is valid (Home Config Exists)');
            $filename = new ConfigFile('');
            $this->assertEquals(true, $filename->isValid(), '[003] Test that home directory config file is valid (Home Config Exists)');
        } else {
            $filename = new ConfigFile('/');
            $this->assertEquals(false, $filename->isValid(), '[004] Test that home directory config file is valid (Home Config Does Not Exist)');
            $filename = new ConfigFile('');
            $this->assertEquals(false, $filename->isValid(), '[006] Test that home directory config file is valid (Home Config Does Not Exist)');
            $filename = new ConfigFile();
            $this->assertEquals(false, $filename->isValid(), '[005] Test that config file is valid (Home Config Does Not Exist)');
        }
        $home = $_SERVER['HOME'];
        unset($_SERVER['HOME']);
        $filename = new ConfigFile('/');
        $this->assertEquals(false, $filename->isValid(), '[010] Test that config file is not found (Bypassing Home Check)');
        $_SERVER['HOME'] = $home;
    }

    public function testConfigFileValid()
    {
        $this->assertEquals(true, $this->configFile->isValid(), 'Test that config file is valid');
        $badConfigFile = new ConfigFile('/', 'null_should_not_exist');
        $this->assertEquals(false, $badConfigFile->isValid(), 'Test that config file is invalid when not found');
    }
}
