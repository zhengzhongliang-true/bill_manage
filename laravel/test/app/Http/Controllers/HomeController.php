<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Predis\Client;

class HomeController extends BaseController
{
    public static function home()
    {
        $redis = new Client(array('host' => '127.0.0.1','port' => 6379));
        $redis->set('name', 'xiaoming');
        echo $redis->get('name');
    }
}
