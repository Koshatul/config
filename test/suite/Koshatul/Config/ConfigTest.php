<?php

/*
 * Copyright © 2013 Kosh
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Koshatul\Config;

use PHPUnit_Framework_TestCase;

class ConfigTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		Config::Instance(__DIR__);
	}

	public function testFindConfigFile()
	{
		$filename = new ConfigFile(__DIR__);
		$expectedLocation = str_replace("suite/Koshatul/Config", "", __DIR__);
		$this->assertEquals($expectedLocation.'.kosh.config.toml', $filename->getFilename(), "Check Test File Location");
	}

	public function testTOMLSpecifics()
	{
		$this->assertEquals('TOML File Title',  Config::Get('title'),    'Return Bare Key');
	}

	public function testConfigSetup()
	{
		$this->assertEquals('testdatavalue',  Config::Get('testsection/test'),         'Return Known Config Value');
		$this->assertEquals(null,             Config::Get('testsection/doesnotexixt'), 'Return Known Config Value');
		$this->assertEquals('differentvalue', Config::Get('anothersection/test'),      'Return Known Config Value');
	}

	public function testURIParts_pass()
	{
		$this->assertEquals('mysql',     Config::GetMySQLURI('uritest/mysqlurl', PHP_URL_SCHEME), 'MySQL URI Test [VALID]: Scheme');
		$this->assertEquals('username',  Config::GetMySQLURI('uritest/mysqlurl', PHP_URL_USER),   'MySQL URI Test [VALID]: Username');
		$this->assertEquals('password',  Config::GetMySQLURI('uritest/mysqlurl', PHP_URL_PASS),   'MySQL URI Test [VALID]: Password');
		$this->assertEquals('hostname',  Config::GetMySQLURI('uritest/mysqlurl', PHP_URL_HOST),   'MySQL URI Test [VALID]: Hostname');
		$this->assertEquals('1234',      Config::GetMySQLURI('uritest/mysqlurl', PHP_URL_PORT),   'MySQL URI Test [VALID]: Port');
		$this->assertEquals('schema',    Config::GetMySQLURI('uritest/mysqlurl', PHP_URL_PATH),   'MySQL URI Test [VALID]: Database');

		$query = array (
			'scheme' => 'mysql',
			'host' => 'hostname',
			'port' => 1234,
			'user' => 'username',
			'pass' => 'password',
			'path' => '/schema',
		);
		$this->assertEquals($query,      Config::GetMySQLURI('uritest/mysqlurl', null), 'MySQL URI Test [VALID]: Array');


		$this->assertEquals('mysql',     Config::GetMySQLURI('uritest/mysqlurl_socket', PHP_URL_SCHEME), 'MySQL URI Test [VALID_WITHSOCKET]: Scheme');
		$this->assertEquals('username',  Config::GetMySQLURI('uritest/mysqlurl_socket', PHP_URL_USER),   'MySQL URI Test [VALID_WITHSOCKET]: Username');
		$this->assertEquals('password',  Config::GetMySQLURI('uritest/mysqlurl_socket', PHP_URL_PASS),   'MySQL URI Test [VALID_WITHSOCKET]: Password');
		$this->assertEquals('hostname',  Config::GetMySQLURI('uritest/mysqlurl_socket', PHP_URL_HOST),   'MySQL URI Test [VALID_WITHSOCKET]: Hostname');
		$this->assertEquals('1234',      Config::GetMySQLURI('uritest/mysqlurl_socket', PHP_URL_PORT),   'MySQL URI Test [VALID_WITHSOCKET]: Port');
		$this->assertEquals('schema',    Config::GetMySQLURI('uritest/mysqlurl_socket', PHP_URL_PATH),   'MySQL URI Test [VALID_WITHSOCKET]: Database');

		$query = Config::GetMySQLURI('uritest/mysqlurl_socket', PHP_URL_QUERY);
		$this->assertArrayHasKey('socket', $query, 'MySQL URI Test [VALID_WITHSOCKET]: Socket Value Exists');
		$this->assertEquals('/tmp/mysql.sock', $query['socket'], 'MySQL URI Test [VALID_WITHSOCKET]: Socket Value Correct');
	}

	public function testURIParts_baduri() 
	{
		$this->assertEquals(null,  Config::GetMySQLURI('uritest/mysqlurl_badurl', PHP_URL_SCHEME), 'MySQL URI Test [BADURI]: Scheme');
		$this->assertEquals(null,  Config::GetMySQLURI('uritest/mysqlurl_badurl', PHP_URL_USER),   'MySQL URI Test [BADURI]: Username');
		$this->assertEquals(null,  Config::GetMySQLURI('uritest/mysqlurl_badurl', PHP_URL_PASS),   'MySQL URI Test [BADURI]: Password');
		$this->assertEquals(null,  Config::GetMySQLURI('uritest/mysqlurl_badurl', PHP_URL_HOST),   'MySQL URI Test [BADURI]: Hostname');
		$this->assertEquals(null,  Config::GetMySQLURI('uritest/mysqlurl_badurl', PHP_URL_PORT),   'MySQL URI Test [BADURI]: Port');
		$this->assertEquals(null,  Config::GetMySQLURI('uritest/mysqlurl_badurl', PHP_URL_PATH),   'MySQL URI Test [BADURI]: Database');
	}

}