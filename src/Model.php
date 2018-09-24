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
//    use LegacyMethodsTrait;

    private $cache_time = 0;

    /**
     * @return string
     */
    public static function tableName()
    {
        $bundleName = self::getBundleName();
        if (!empty($bundleName)) {
            return sprintf("%s_%s",
                self::normalizeTableName(str_replace('Bundle', '', $bundleName)),
                parent::tableName()
            );
        } else {
            return parent::tableName();
        }
    }

    /**
     * Return module name
     * @return string
     */
    public static function getBundleName()
    {
        $delim = '/';
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $delim = '\\';
        }

        $object = new ReflectionClass(get_called_class());

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
        return static::objects(($instance) ? $instance : $this);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getShortName();
    }

    public function getCache() {
        return $this->cache_time;
    }

    public function cache($life_time = 30) {
        $this->cache_time = $life_time;
        return $this;
    }

    public function noCache() {
        $this->cache_time = 0;
        return $this;
    }
}
