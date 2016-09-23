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

class ConfigTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        Config::Instance(__DIR__);
    }

    public function testInMemorySetMethods()
    {
        $this->assertEquals(null,  Config::Get('foonew'),    'Test non-existant Key');
        Config::Set('foonew', 'testvalue1');
        $this->assertEquals(array('' => 'testvalue1'),  Config::Get('foonew'),    'Test get new title Key');

        $this->assertEquals(null,  Config::Get('footoo/item'),    'Test non-existant Key');
        Config::Set('footoo/item', 'testvalue1');
        $this->assertEquals('testvalue1',  Config::Get('footoo/item'),    'Test get new title Key');

        Config::Set('footoo/item');
        $this->assertEquals(null,  Config::Get('footoo/item'),    'Test non-existant Key');

        Config::Set('footoo/item', 'testvalue2');
        $this->assertEquals('testvalue2',  Config::Get('footoo/item'),    'Test get new title Key');

        Config::Set('footoo/item', 'testvalue3');
        $this->assertEquals('testvalue3',  Config::Get('footoo/item'),    'Test get new title Key');
    }

    public function testEnvOverrideFile()
    {
        $url = 'http://code.nervhq.com/toml/.empty.config.toml';
        putenv("KOSH_CONFIG=$url");
        $configObject = new Config(__DIR__);
        $this->assertEquals($url,  $configObject->getConfigFile()->getFilename(),    'Test Environment Override Config Location');
        putenv('KOSH_CONFIG');
    }

    public function testEnvironmentVariables()
    {
        $this->assertEquals('testdatavalue',  Config::Get('testsection/overridetest'),         'Return Known Config Value');
        putenv('TESTSECTION_OVERRIDETEST=override_value');
        $this->assertEquals('override_value',  Config::Get('testsection/overridetest'),         'Return Environemnt Value (overiding existing value)');

        $this->assertEquals(null,        Config::Get('onlyin/env'),         'Return Invalid Environemnt Value');
        putenv('ONLYIN_ENV=newvalue');
        $this->assertEquals('newvalue',  Config::Get('onlyin/env'),         'Return Environemnt Value (new value)');
    }

    public function testDefaultValues()
    {
        $this->assertEquals(null,            Config::Get('doesnotexist'),                    'Test Non Existent Key');
        $this->assertEquals('defaultvalue',  Config::Get('doesnotexist', 'defaultvalue'),    'Test Default Value on non-existant Key');
    }

    public function testFindConfigFile()
    {
        $filename = new ConfigFile(__DIR__);
        $expectedLocation = str_replace('suite/Koshatul/Config', '', __DIR__);
        $this->assertEquals($expectedLocation . '.kosh.config.toml', $filename->getFilename(), 'Check Test File Location');
    }

    public function testURLConfigFile()
    {
        $url = 'http://code.nervhq.com/toml/.kosh.config.toml';
        // $testToml = '#Toml File'.PHP_EOL.PHP_EOL.'[webonly]'.PHP_EOL.'test="testvalue"'.PHP_EOL;

        $configFile = new ConfigFile($url);
        $this->assertEquals($url, $configFile->getFilename(), 'Check URL File Location');

        // echo "Mock Test".PHP_EOL;
        // $config = $this->getMockBuilder('Koshatul\Config\Config')
        // 	->disableOriginalConstructor()
        // 	->getMock();

        // $config->expects($this->once())
        // 	->method('_getContents')
        // 	->with($url)
        // 	->willReturn($testToml);
        // $config->__construct($url);

        $config = new Config($url);
        $this->assertEquals('testvalue', $config->getValue('webonly/test'), 'Check Value in Web Only TOML file');

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

    public function testURIParts()
    {
        $this->assertEquals('mysql',     Config::GetURI('uritest/mysqlurl', PHP_URL_SCHEME), 'URI Test [VALID]: Scheme');
        $this->assertEquals('username',  Config::GetURI('uritest/mysqlurl', PHP_URL_USER),   'URI Test [VALID]: Username');
        $this->assertEquals('password',  Config::GetURI('uritest/mysqlurl', PHP_URL_PASS),   'URI Test [VALID]: Password');
        $this->assertEquals('hostname',  Config::GetURI('uritest/mysqlurl', PHP_URL_HOST),   'URI Test [VALID]: Hostname');
        $this->assertEquals('1234',      Config::GetURI('uritest/mysqlurl', PHP_URL_PORT),   'URI Test [VALID]: Port');
        $this->assertEquals('/schema',   Config::GetURI('uritest/mysqlurl', PHP_URL_PATH),   'URI Test [VALID]: Path');
    }

    public function testMySQLURIParts()
    {
        $this->assertEquals('mysql',     Config::GetMySQLURI('uritest/mysqlurl', PHP_URL_SCHEME), 'MySQL URI Test [VALID]: Scheme');
        $this->assertEquals('username',  Config::GetMySQLURI('uritest/mysqlurl', PHP_URL_USER),   'MySQL URI Test [VALID]: Username');
        $this->assertEquals('password',  Config::GetMySQLURI('uritest/mysqlurl', PHP_URL_PASS),   'MySQL URI Test [VALID]: Password');
        $this->assertEquals('hostname',  Config::GetMySQLURI('uritest/mysqlurl', PHP_URL_HOST),   'MySQL URI Test [VALID]: Hostname');
        $this->assertEquals('1234',      Config::GetMySQLURI('uritest/mysqlurl', PHP_URL_PORT),   'MySQL URI Test [VALID]: Port');
        $this->assertEquals('schema',    Config::GetMySQLURI('uritest/mysqlurl', PHP_URL_PATH),   'MySQL URI Test [VALID]: Database');
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
        $this->assertEquals($query,      Config::GetMySQLURI('uritest/mysqlurl', null), 'MySQL URI Test [VALID]: Array');
    }

    public function testMySQLURIParts_socket()
    {
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

    public function testMySQLURIParts_baduri()
    {
        $this->assertEquals(null,  Config::GetMySQLURI('uritest/mysqlurl_badurl', PHP_URL_SCHEME), 'MySQL URI Test [BADURI]: Scheme');
        $this->assertEquals(null,  Config::GetMySQLURI('uritest/mysqlurl_badurl', PHP_URL_USER),   'MySQL URI Test [BADURI]: Username');
        $this->assertEquals(null,  Config::GetMySQLURI('uritest/mysqlurl_badurl', PHP_URL_PASS),   'MySQL URI Test [BADURI]: Password');
        $this->assertEquals(null,  Config::GetMySQLURI('uritest/mysqlurl_badurl', PHP_URL_HOST),   'MySQL URI Test [BADURI]: Hostname');
        $this->assertEquals(null,  Config::GetMySQLURI('uritest/mysqlurl_badurl', PHP_URL_PORT),   'MySQL URI Test [BADURI]: Port');
        $this->assertEquals(null,  Config::GetMySQLURI('uritest/mysqlurl_badurl', PHP_URL_PATH),   'MySQL URI Test [BADURI]: Database');
    }
}
