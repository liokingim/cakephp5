<?php

namespace App\Utility;

use Redis;

class RedisStatus
{
    public static function isRedisRunning($host = '127.0.0.1', $port = 6379)
    {
        try {
            $redis = new Redis();
            $redis->connect($host, $port);
            return $redis->ping();
        } catch (\Exception $e) {
            return false;
        }
    }
}