<?php
//save my dir
$dot = dirname(__FILE__);
$composer = dirname($dot) . '/vendor/autoload.php';

if (!file_exists($composer)) {
    throw new RuntimeException("Please run 'composer install' first to set up autoloading. $composer");
}
/** @var \Composer\Autoload\ClassLoader $autoloader */
$autoloader = require_once $composer;
// Already set in composer.json
//$autoloader->add('tests\\', $dot);
