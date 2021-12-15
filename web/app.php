<?php

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../vendor/autoload.php';
if (PHP_VERSION_ID < 70000) {
    include_once __DIR__.'/../var/bootstrap.php.cache';
}

if (class_exists(Dotenv::class) && is_file(dirname(__DIR__) . '/.env'))
{
  // load all the .env files
  (new Dotenv())->load(dirname(__DIR__) . '/.env');
}

$env = isset($_SERVER['APP_ENV']) ? $_SERVER['APP_ENV'] : 'prod';
$debug = $env === 'dev';


$kernel = new AppKernel($env, $debug);
if (PHP_VERSION_ID < 70000) {
    $kernel->loadClassCache();
}
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
