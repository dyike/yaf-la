<?php

use Yaf\Controller_Abstract;
use Yaf\Dispatcher;
use Yaf\Exception_LoadFailed;

class ErrorController extends Controller_Abstract
{
    public function init()
    {
        header("Content-Type:text/html;charset=utf-8");
        Dispatcher::getInstance()->disableView();
    }


    public function errorAction()
    {
        $exception = $this->getRequest()->getException();

        if (isset($_GET['x'])) {
            echo "<pre>";
            var_dump($exception);
            echo "</pre>";
        }

        // 加载失败
        if ($exception instanceof Exception_LoadFailed) {
            $this->redirect('/404');
            return;
        } else {

        }
    }
}