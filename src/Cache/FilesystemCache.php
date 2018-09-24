<?php

namespace Tsukasa\Orm\Cache;

use Tsukasa\Helpers\Paths;
use Doctrine\Common\Cache\FilesystemCache as DBALFilesystemCache;

class FilesystemCache extends DBALFilesystemCache
{
    public function __construct($directory, $extension = self::EXTENSION, $umask = 0002)
    {
        parent::__construct(Paths::get($directory), $extension, $umask);
    }
}