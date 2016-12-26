<?php

use Yaf\Controller_Abstract;
use App\Models\User;

class IndexController extends Controller_Abstract
{
    public function init()
    {
    }

    public function indexAction()
    {
        $user = User::all();

        print_r($user->toArray());
    }

}
