<?php

namespace Tsukasa\Orm\TableMetaData;

trait AutoMetaTrait
{
    /**
     * @return AutoMetaData|MetaData
     */
    public static function getMeta()
    {
        return AutoMetaData::getInstance(static::class);
    }
}