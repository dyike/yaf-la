<?php
/**
 * 入口文件
 */

header('content-Type:text/html;charset=utf-8;');

define("APP_PATH",  realpath(dirname(__FILE__).'/../')); /* 指向public的上一级 */
//define("APP_PATH", dirname(__FILE__));
define("BASE_URL", "http://yaf.app/");


$app  = new Yaf_Application(APP_PATH . "/conf/application.ini");
$app->bootstrap()->run();