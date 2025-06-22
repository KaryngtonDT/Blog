<?php

namespace App\Framework\Service;

use PhpDevCommunity\Flash\Flash;

class FlashService implements FlashServiceInterface
{

    private $flash;
    public function __construct()
    {
        $this->flash= new Flash($_SESSION);
    }


    public function add(string $type, string $message):void{

        unset($_SESSION['__flash']);
        $this->flash->set($type, $message);
    }

    public function get(string $type):?string{
        return $this->flash->get($type);
    }

}
