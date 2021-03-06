<?php
@include 'config.php';
require_once 'vendor/autoload.php';

date_default_timezone_set('America/Sao_Paulo');

// REDBEAN CONFIGURATION
use RedBeanPHP\Facade as RedBean;

if(defined('MYSQL_HOST') && defined('MYSQL_DATABASE') && defined('MYSQL_USER') && defined('MYSQL_PASSWORD')) {
    if(!defined('MYSQL_PORT')) define('MYSQL_PORT', '3306');
    RedBean::setup('mysql:host='. MYSQL_HOST . ';port=' . MYSQL_PORT . ';dbname=' . MYSQL_DATABASE , MYSQL_USER, MYSQL_PASSWORD);
    RedBean::setAutoResolve(true);
    // TODO: Implement freeze
    // RedBean::freeze();
}

// SLIM FRAMEWORK
\Slim\Slim::registerAutoLoader();
$app = new \Slim\Slim();

// Teste
function exception_handler($exception) {
    echo $exception;
}
set_exception_handler('exception_handler');
// LOAD CONTROLLERS
foreach (glob('controllers/*.php') as $controller) {
    include_once $controller;
}
error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) {
        echo $severity . ' ' . $message . " at " . $file . ':' . $line;
});
// Teste Fim

Controller::init();
$app->run();
