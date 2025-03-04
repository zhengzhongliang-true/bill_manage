<?php

use Predis\Client;

class Factory
{
    public static function redis()
    {
        return new Client(require __DIR__ . '/../config/redis.php');
    }
}
