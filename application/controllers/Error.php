<?php

/**
 * 错误控制器
 * Class ErrorController
 */
class ErrorController extends Yaf_Controller_Abstract
{
    public function errorAction($exception)
    {
        //$this->display('../public/404');
        return false;
    }
}