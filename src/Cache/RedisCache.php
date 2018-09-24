<?php

namespace Tsukasa\Orm\Cache;

use Tsukasa\Helpers\Paths;
use Doctrine\Common\Cache\RedisCache as DBALRedisCache;

class RedisCache extends DBALRedisCache
{
    public function __construct($host = '127.0.0.1', $port = 6379, $timeout = 0.0, $reserved = null, $retry_interval = 0)
    {
        $r = new \Redis();

        if ($r->connect($host, $port, $timeout, $reserved, $retry_interval)) {
            $this->setRedis($r);
        }
    }
}