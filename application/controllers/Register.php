<?php

class RegisterController extends Yaf_Controller_Abstract
{
    private $user;

    private function init()
    {
        $this->user = $this->load('user');
        $userID = $this->getSession('userID');

        if ($userID) {
            jsRedirect('/user/profile');
        }
    }

    public function indexAction()
    {

    }

    public function registerAction()
    {
        $user['username'] = $this->getParam('username');
        $user['password'] = $this->getParam('password');

        if (!$user['username'] || !$user['password']) {
            $error = "Username and Password are required!";
            $this->showError($error, 'index');
        }

        //检查用户名是否存在
        $where = ['username' => $user['username']];
        $num = $this->user>where($where)->Total();
        if ($num) {
            $msg = '用户名已经存在，请使用其他！';
            $this->showError($msg, 'index');
        }

        $userID = $this->user->Insert($user);
        if (!$userID) {
            $error = '注册失败，请重试!';
            $this->showError($error, 'index');
        } else {
            $msg = '注册成功，请登陆';
            $url = '/login';
        }

        jsAlert($msg);
        jsRedirect($url);

    }
}