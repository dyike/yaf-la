<?php

use Yaf\Loader;
use Yaf\Registry;
use Yaf\Dispatcher;
use Yaf\Application;
use Yaf\Route\Rewrite;
use Yaf\Bootstrap_Abstract;

use Illuminate\Events\Dispatcher as LaDispatcher;
use Illuminate\Container\Container as LaContainer;
use Illuminate\Database\Capsule\Manager as Capsule;


class Bootstrap extends Bootstrap_Abstract
{
    protected $config;

    // 初始化配置
    public function _initConfig(Dispatcher $dispatcher)
    {
        $this->config = Application::app()->getConfig();
        Registry::set('config', $this->config);
    }

    public function _initLoader()
    {
        Loader::import(APP_PATH . "/vendor/autoload.php");
    }

    // 初始化 Eloquent ORM
    public function _initDatabaseEloquent(Dispatcher $dispatcher)
    {
        $capsule = new Capsule();
        // 创建默认链接
        $capsule->addConnection($this->config->database->toArray());
        $capsule->setEventDispatcher(new LaDispatcher(new LaContainer));
        // 设置全局静态可访问
        $capsule->setAsGlobal();
        // 启动Eloquent
        $capsule->bootEloquent();

    }

}