<?php

/**
*  登陆
*/
class LoginController extends Yaf_Controller_Abstract
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

    public function loginAction()
    {
        $username = $this->getParam('username');
        $password = $this->getParam('password');

        $field = ['id'];
        $where = ['username' => $username, 'password' => $password];
        $data = $this->user->Field($field)->where($where)->SelectOne();
        $userID = $data['id'];
        if ($userID) {
            //set to session
            $this->setSession('userID', $userID);
            $this->setSession('username', $username);
            echo 1;
        } else {
            echo 0;
        }
        die;
    }

}