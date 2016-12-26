<?php

$str =  <<< TEMPLATE
<?php

use Yaf\Controller_Abstract;

class %s extends Controller_Abstract
{
    public function init()
    {
    }


    public function indexAction()
    {

    }

}

TEMPLATE;
return $str;