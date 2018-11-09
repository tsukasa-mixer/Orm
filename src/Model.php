<?php

namespace Tsukasa\Orm;

use ReflectionClass;

/**
 * Class Model
 * @property (int)|(string) pk
 * @package Tsukasa\Orm
 */
class Model extends AbstractModel
{
    protected $cache_time = 0;

    /**
     * @return string
     * @throws \ReflectionException
     */
    public static function tableName()
    {
        $bundleName = self::getBundleName();
        if (!empty($bundleName)) {
            return sprintf('%s_%s',
                self::normalizeTableName(str_replace('Bundle', '', $bundleName)),
                parent::tableName()
            );
        }

        return parent::tableName();
    }

    /**
     * Return module name
     * @return string
     * @throws \ReflectionException
     */
    public static function getBundleName()
    {
        $delim = DIRECTORY_SEPARATOR;

        $object = new ReflectionClass(static::class);

        // For classical modules
        if ($pos = strpos($object->getFileName(), 'Modules')) {
            $shortPath = substr($object->getFileName(), $pos + 8);
            return substr($shortPath, 0, strpos($shortPath, $delim));
        }

        // For symphony bundles
        if ($pos = strpos($object->getFileName(), 'Bundle')) {
            $shortPath = substr($object->getFileName(), $pos + 7);
            return substr($shortPath, 0, strpos($shortPath, $delim));
        }

        return '';
    }

    public function getObjects($instance = null)
    {
        return static::objects($instance ?: $this);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)self::classNameShort();
    }

    public function getCache()
    {
        return $this->cache_time;
    }

    public function cache($life_time = 30)
    {
        $this->cache_time = $life_time;
        return $this;
    }

    public function noCache()
    {
        $this->cache_time = 0;
        return $this;
    }
}
