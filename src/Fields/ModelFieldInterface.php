<?php

namespace Tsukasa\Orm\Fields;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Tsukasa\Orm\ModelInterface;

/**
 * Interface ModelFieldInterface
 * @package Tsukasa\Orm\Fields
 */
interface ModelFieldInterface
{
    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @param ModelInterface $model
     */
    public function setModel(ModelInterface $model);

    /**
     * @param $value
     */
    public function setValue($value);

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @return string|null|bool
     */
    public function getVerboseName();

    /**
     * @return \Doctrine\Dbal\Types\Type|false|null
     */
    public function getSqlType();

    /**
     * @return array|null
     */
    public function getSqlOptions();

    /**
     * @return \Doctrine\Dbal\Schema\Column|null|false
     */
    public function getColumn();

    /**
     * @return \Doctrine\Dbal\Schema\Index[]|array
     */
    public function getSqlIndexes();

    /**
     * @return string
     */
    public function getAttributeName();

    /**
     * Converts a value from its PHP representation to its database representation
     * of this type.
     *
     * @param mixed                                     $value    The value to convert.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
     *
     * @return mixed The database representation of the value.
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform);

    /**
     * Converts a value from its database representation to its PHP representation
     * of this type.
     *
     * @param mixed                                     $value    The value to convert.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
     *
     * @return mixed The PHP representation of the value.
     */
    public function convertToPHPValue($value, AbstractPlatform $platform);

    /**
     * Modifies the SQL expression (identifier, parameter) to convert to a PHP value.
     *
     * @param string                                    $sqlExpr
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     *
     * @return string
     */
    public function convertToPHPValueSQL($sqlExpr, AbstractPlatform $platform);

    /**
     * Modifies the SQL expression (identifier, parameter) to convert to a database value.
     *
     * @param string                                    $sqlExpr
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     *
     * @return string
     */
    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform);

    /**
     * internal event
     * @param ModelInterface $model
     * @param $value
     * @return
     */
    public function afterInsert(ModelInterface $model, $value);

    /**
     * internal event
     * @param ModelInterface $model
     * @param $value
     * @return
     */
    public function afterUpdate(ModelInterface $model, $value);

    /**
     * internal event
     * @param ModelInterface $model
     * @return
     */
    public function afterDelete(ModelInterface $model, $value);

    /**
     * internal event
     * @param ModelInterface $model
     * @param $value
     * @return
     */
    public function beforeInsert(ModelInterface $model, $value);

    /**
     * internal event
     * @param ModelInterface $model
     * @param $value
     * @return
     */
    public function beforeUpdate(ModelInterface $model, $value);

    /**
     * internal event
     * @param ModelInterface $model
     * @param $value
     * @return
     */
    public function beforeDelete(ModelInterface $model, $value);
}