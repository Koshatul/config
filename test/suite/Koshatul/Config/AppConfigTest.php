<?php

/*
 * Copyright © 2013 Kosh
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Koshatul\Config;

use Yosymfony\Toml\Toml;

use PHPUnit_Framework_TestCase;

class AppConfigTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
    	$this->configObject = new Config(__DIR__);
    	$this->config = new AppConfig('uritest', $this->configObject);
    }

    public function testConfigContent()
    {
    	$tmpConfig = new AppConfig('testsection', $this->configObject);
        $this->assertEquals('testdatavalue',  $tmpConfig->getValue('test'),         'Return Known Config Value');
        $this->assertEquals(null,             $tmpConfig->getValue('doesnotexixt'), 'Return Known Config Value');
    }

    public function testURIParts()
    {
        $this->assertEquals('mysql',     $this->config->getURIValue('mysqlurl', PHP_URL_SCHEME), 'URI Test [VALID]: Scheme');
        $this->assertEquals('username',  $this->config->getURIValue('mysqlurl', PHP_URL_USER),   'URI Test [VALID]: Username');
        $this->assertEquals('password',  $this->config->getURIValue('mysqlurl', PHP_URL_PASS),   'URI Test [VALID]: Password');
        $this->assertEquals('hostname',  $this->config->getURIValue('mysqlurl', PHP_URL_HOST),   'URI Test [VALID]: Hostname');
        $this->assertEquals('1234',      $this->config->getURIValue('mysqlurl', PHP_URL_PORT),   'URI Test [VALID]: Port');
        $this->assertEquals('/schema',   $this->config->getURIValue('mysqlurl', PHP_URL_PATH),   'URI Test [VALID]: Path');
	}

    public function testMySQLURIParts()
    {
        $this->assertEquals('mysql',     $this->config->getMySQLURIValue('mysqlurl', PHP_URL_SCHEME), 'MySQL URI Test [VALID]: Scheme');
        $this->assertEquals('username',  $this->config->getMySQLURIValue('mysqlurl', PHP_URL_USER),   'MySQL URI Test [VALID]: Username');
        $this->assertEquals('password',  $this->config->getMySQLURIValue('mysqlurl', PHP_URL_PASS),   'MySQL URI Test [VALID]: Password');
        $this->assertEquals('hostname',  $this->config->getMySQLURIValue('mysqlurl', PHP_URL_HOST),   'MySQL URI Test [VALID]: Hostname');
        $this->assertEquals('1234',      $this->config->getMySQLURIValue('mysqlurl', PHP_URL_PORT),   'MySQL URI Test [VALID]: Port');
        $this->assertEquals('schema',    $this->config->getMySQLURIValue('mysqlurl', PHP_URL_PATH),   'MySQL URI Test [VALID]: Database');
	}

    public function testMySQLURIParts_array()
    {
        $query = array(
            'scheme' => 'mysql',
            'host' => 'hostname',
            'port' => 1234,
            'user' => 'username',
            'pass' => 'password',
            'path' => '/schema',
        );
        $this->assertEquals($query,      $this->config->getMySQLURIValue('mysqlurl', null), 'MySQL URI Test [VALID]: Array');
    }

    public function testMySQLURIParts_socket()
    {
        $this->assertEquals('mysql',     $this->config->getMySQLURIValue('mysqlurl_socket', PHP_URL_SCHEME), 'MySQL URI Test [VALID_WITHSOCKET]: Scheme');
        $this->assertEquals('username',  $this->config->getMySQLURIValue('mysqlurl_socket', PHP_URL_USER),   'MySQL URI Test [VALID_WITHSOCKET]: Username');
        $this->assertEquals('password',  $this->config->getMySQLURIValue('mysqlurl_socket', PHP_URL_PASS),   'MySQL URI Test [VALID_WITHSOCKET]: Password');
        $this->assertEquals('hostname',  $this->config->getMySQLURIValue('mysqlurl_socket', PHP_URL_HOST),   'MySQL URI Test [VALID_WITHSOCKET]: Hostname');
        $this->assertEquals('1234',      $this->config->getMySQLURIValue('mysqlurl_socket', PHP_URL_PORT),   'MySQL URI Test [VALID_WITHSOCKET]: Port');
        $this->assertEquals('schema',    $this->config->getMySQLURIValue('mysqlurl_socket', PHP_URL_PATH),   'MySQL URI Test [VALID_WITHSOCKET]: Database');

        $query = $this->config->getMySQLURIValue('mysqlurl_socket', PHP_URL_QUERY);
        $this->assertArrayHasKey('socket', $query, 'MySQL URI Test [VALID_WITHSOCKET]: Socket Value Exists');
        $this->assertEquals('/tmp/mysql.sock', $query['socket'], 'MySQL URI Test [VALID_WITHSOCKET]: Socket Value Correct');
    }

    public function testURIParts_baduri()
    {
        $this->assertEquals(null,  $this->config->getMySQLURIValue('mysqlurl_badurl', PHP_URL_SCHEME), 'MySQL URI Test [BADURI]: Scheme');
        $this->assertEquals(null,  $this->config->getMySQLURIValue('mysqlurl_badurl', PHP_URL_USER),   'MySQL URI Test [BADURI]: Username');
        $this->assertEquals(null,  $this->config->getMySQLURIValue('mysqlurl_badurl', PHP_URL_PASS),   'MySQL URI Test [BADURI]: Password');
        $this->assertEquals(null,  $this->config->getMySQLURIValue('mysqlurl_badurl', PHP_URL_HOST),   'MySQL URI Test [BADURI]: Hostname');
        $this->assertEquals(null,  $this->config->getMySQLURIValue('mysqlurl_badurl', PHP_URL_PORT),   'MySQL URI Test [BADURI]: Port');
        $this->assertEquals(null,  $this->config->getMySQLURIValue('mysqlurl_badurl', PHP_URL_PATH),   'MySQL URI Test [BADURI]: Database');
    }
}
