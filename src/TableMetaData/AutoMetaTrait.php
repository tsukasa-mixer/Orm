<?php

namespace Tsukasa\TableMetaData\Orm;

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