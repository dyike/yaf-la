<?php

/*
 * 默认启动脚本
 */

class Bootstrap extends Yaf_Bootstrap_Abstract
{
    private $arrConfig;

    public function _init()
    {

    }

    public function _initConfig() {
        $this->arrConfig = Yaf_Application::app()->getConfig();
        Yaf_Registry::set('config', $this->arrConfig);
    }

    //载入数据库
    public function _initDatabase()
    {
        $db_config['hostname'] = $this->arrConfig->db->hostname;
        $db_config['username'] = $this->arrConfig->db->username;
        $db_config['password'] = $this->arrConfig->db->password;
        $db_config['database'] = $this->arrConfig->db->database;
        $db_config['log'] = $this->arrConfig->db->log;
        Yaf_Registry::set('db', new Db($db_config));
    }


    //载入缓存
    public function _initCache()
    {
        $cache_config['port'] = $this->arrConfig->cache->port;
        $cache_config['host'] = $this->arrConfig->cache->host;
        Yaf_Registry::set('redis', new Rdb($cache_config));
    }

    //载入方法库
    public function _initLibrary()
    {
        Yaf_Loader::import('Core.php');
    }
    //注册路由
    public function _initRoute(Yaf_Dispatcher $dispatcher)
    {

    }
}