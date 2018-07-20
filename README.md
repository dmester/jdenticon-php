# [Jdenticon-php](https://jdenticon.com)
PHP library for generating highly recognizable identicons.

![Sample identicons](https://jdenticon.com/hosted/github-samples.png)

[![Build Status](https://travis-ci.org/dmester/jdenticon-php.svg?branch=master)](https://travis-ci.org/dmester/jdenticon-php)
[![Total Downloads](https://poser.pugx.org/jdenticon/jdenticon/downloads)](https://packagist.org/packages/jdenticon/jdenticon)

## Features
Jdenticon-php is a PHP port of the JavaScript library [Jdenticon](https://github.com/dmester/jdenticon).

* Renders identicons as PNG or SVG with no extension requirements.
* Runs on PHP 5.3 and later.

## Live demo
https://jdenticon.com

## Getting started
Using Jdenticon is simple. Follow the steps below to integrate Jdenticon into your website.

### 1. Install the Jdenticon Composer package
The easiest way to get started using Jdenticon for PHP is to install the Jdenticon Composer package.

```
composer require jdenticon/jdenticon
```

### 2. Create a php file that will serve an icon
Now create a file that you call icon.php and place it in the root of your application. Add the following content to the file.

```PHP
<?php
include_once("vendor/autoload.php");

// Set max-age to a week to benefit from client caching (this is optional)
header('Cache-Control: max-age=604800');

// Parse query string parameters
$value = $_GET['value'];
$size = min(max(intval($_GET['size']), 20), 500);

// Render icon
$icon = new \Jdenticon\Identicon();
$icon->setValue($value);
$icon->setSize($size);
$icon->displayImage('png');
```

### 3. Use icon.php
Open up your favourite browser and navigate to http://localhost:PORT/icon.php?size=100&value=anything. 
An identicon should be displayed. Try to change the url parameters to see the difference in the generated icon.

## Other resources
### API documentation
For more usage examples and API documentation, please see:

https://jdenticon.com/php-api.html

## License
Jdenticon-php is released under the [MIT license](https://github.com/dmester/jdenticon-php/blob/master/LICENSE).
