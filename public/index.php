<?php
/**
 * 入口文件
 */

// 程序启动时间
define('APP_START_TIME', microtime(true));

// 程序跟目录
define("APP_PATH",  realpath(dirname(__FILE__) . '/../')); /* 指向public的上一级 */

// 加载 Composer
require APP_PATH.'/vendor/autoload.php';

// 启动程序
$app  = new Yaf\Application(APP_PATH . "/conf/application.ini");
$app->bootstrap()->run();