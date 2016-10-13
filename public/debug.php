<?php
/*
 * 统一入口文件
 * Created by Tsrign
 */
ini_set('display_errors', 'on');
error_reporting(E_ALL);

define("APP_IN", true);
define("APP_ENV", isset($_SERVER['APP_ENV']) ? $_SERVER['APP_ENV'] : 'product');
define("APP_TIME", time());
define("APP_BASE", realpath(dirname(dirname(__FILE__))));

define("APP_CONF", APP_BASE . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR.'application.ini');



//APP RUN
$app  = new Yaf_Application(APP_CONF, APP_ENV);
$router = Yaf_Dispatcher::getInstance()->getRouter();
$route = new Yaf_Route_Supervar("r");
$router->addRoute("name", $route);
$app->bootstrap()->run();