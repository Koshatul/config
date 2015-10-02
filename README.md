Configuration Class for PHP
===========================

A Configuration class for PHP for keeping configuration items separate from repositories or incorporating config into repositories.

[![Build Status](https://travis-ci.org/Koshatul/config.png?branch=master)](https://travis-ci.org/Koshatul/config)
[![Latest Stable Version](https://poser.pugx.org/koshatul/config/v/stable.png)](https://packagist.org/packages/koshatul/config)
[![Total Downloads](https://poser.pugx.org/koshatul/config/downloads.png)](https://packagist.org/packages/koshatul/config)

Installation
------------

Use [Composer](http://getcomposer.org/) to install the package:

Add the following to your `composer.json` and run `composer update`.

```json
"require": {
    "koshatul/config": "~1.0"
}
```

Example
-------

Example configuration file (could be in project root (in the repository), above that or in the users home directory)

```TOML
[testsection]
test="testdatavalue"

[anothersection]
test="differentvalue"

[uritest]
mysqlurl="mysql://username:password@hostname:1234/schema"
```

Usage
-----
You can use this package to get configuration from a global or specific configuration store.

```php
use Koshatul\Config\Config;

$value = Config::Get('project/apikey');

print_r($value);

$array = array(
	'driver'   => 'pdo_mysql',
	'host'     => Config::GetMySQLURI('project/db', PHP_URL_HOST),
	'dbname'   => Config::GetMySQLURI('project/db', PHP_URL_PATH),
	'user'     => Config::GetMySQLURI('project/db', PHP_URL_USER),
	'password' => Config::GetMySQLURI('project/db', PHP_URL_PASS),
	'port'     => $port,
);

print_r($array);
```
