<?php
namespace Tsukasa\Orm;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\DBAL\Connection;
use Exception;
use ArrayAccess;
use Serializable;


use Tsukasa\Orm\Exception\OrmExceptions;
use Tsukasa\Orm\Fields\AutoField;
use Tsukasa\Orm\Fields\Field;
use Tsukasa\Orm\Fields\FileField;
use Tsukasa\Orm\Fields\HasManyField;
use Tsukasa\Orm\Fields\ManyToManyField;
use Tsukasa\Orm\Fields\ModelFieldInterface;
use Tsukasa\Orm\TableMetaData\MetaData;

/**
 * Class NewBase
 * @package Tsukasa\Orm
 * @method static Manager objects($instance = null)
 */
abstract class Base implements ModelInterface, ArrayAccess, Serializable
{

    /**
     * @var bool
     */
    protected $isNewRecord = true;
    /**
     * @deprecated
     * @var bool
     */
    protected $isCreated = false;
    /**
     * @var AttributeCollection
     */
    protected $attributes;

    protected $attributesNotField;
    /**
     * @var string
     */
    protected $using;
    /**
     * @var array
     */
    protected $errors = [];
    /**
     * @var array
     */
    protected $related = [];

    /**
     * @param array $attributes
     *
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        static::getMeta();

        $this->attributes = new AttributeCollection;
        $this->setAttributes($attributes);
    }

    /**
     * @param $name
     * @return string
     */
    public function convertToPrimaryKeyName($name)
    {
        return strtolower($name) === 'pk'
            ? static::getPrimaryKeyName()
            : $name;
    }

    /**
     * @param $name
     * @param $value
     * @return
     * @throws Exception
     */
    public function __set($name, $value)
    {
        $name = $this->convertToPrimaryKeyName($name);

        if ($this->hasField($name)) {
            if ($this->getField($name) instanceof ManyToManyField) {
                $this->related[$name] = $value;
            }
            else {
                $this->setAttribute($name, $value);
            }
        } else {

            $method = 'set' . ucfirst($name);
            if(method_exists($this, $method)) {
                return $this->$method($value);
            }

            throw new \RuntimeException('Setting unknown property ' . get_class($this) . "::" . $name);
        }
    }

    /**
     * Checks if a property value is null.
     * This method overrides the parent implementation by checking if the named attribute is null or not.
     * @param string $name the property name or the event name
     * @return boolean whether the property value is null
     * @throws \ReflectionException
     */
    public function __isset($name)
    {
        $name = $this->convertToPrimaryKeyName($name);
        $meta = static::getMeta();
        return $meta->hasField($name);
    }

    /**
     * @param $name
     *
     * @throws Exception
     */
    public function __unset($name)
    {
        $name = $this->convertToPrimaryKeyName($name);
        $meta = static::getMeta();
        if ($meta->hasField($name)) {
            $this->setAttribute($meta->getField($name)->getAttributeName(), null);
        }
    }

    /**
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function __get($name)
    {

        $name = $this->convertToPrimaryKeyName($name);
        if ($this->hasField($name)) {
            $field = $this->getField($name);

            if ($field instanceof FileField) {
                return $field;
            }

            return $this->getFieldValue($name);
        }

        $method = 'get' . ucfirst($name);
        if(method_exists($this, $method)) {
            return $this->$method();
        }

        throw new \RuntimeException('Setting unknown property ' . \get_class($this) . '::' . $name);
    }

    /**
     * @param string $name
     * @return bool
     * @throws \ReflectionException
     */
    public function hasField($name)
    {
        $name = $this->convertToPrimaryKeyName($name);
        return static::getMeta()->hasField($name);
    }

    /**
     * @return array
     */
    public function getDirtyAttributes()
    {
        return $this->attributes->getDirtyAttributes();
    }

    /**
     * @param string $name
     * @param bool $throw
     * @return ModelFieldInterface|Field|null
     * @throws Exception
     */
    public function getField($name, $throw = false)
    {
        $name = $this->convertToPrimaryKeyName($name);

        if (static::getMeta()->hasField($name)) {
            $field = static::getMeta()->getField($name);
            $field->setModel($this);

            return $field;
        }

        if ($throw) {
            throw new OrmExceptions("Unknown field '{$name}'");
        }

        return null;
    }

    public function getFieldsInit()
    {
        $result = [];
        if ($fields = static::getFields()) {
            $fields = array_keys($fields);
            foreach ($fields as $field) {
                $result[$field] = $this->getField($field);
            }
        }

        return $result;
    }

    /**
     * @param string $name
     * @param $value
     * @return static
     *
     * @throws Exception
     *
     */
    public function setAttribute($name, $value)
    {
        $primaryKeyNames = self::getPrimaryKeyName(true);

        $meta = static::getMeta();
        $name = $meta->getMappingName($name);

        if ($meta->hasField($name)) {
            $field = $meta->getField($name);
            $attributeName = $field->getAttributeName();

            if ($field->getSqlType()) {

                $platform = $this->getConnection()->getDatabasePlatform();
                $value = $field->convertToDatabaseValueSQL($value, $platform);

                if (\in_array($attributeName, $primaryKeyNames, true)) {
                    // If new primary key is empty - mark model as new
                    if (empty($value)) {
                        $this->setIsNewRecord(true);
                    }
                    else if ($this->getAttribute($attributeName) != $value) {
                        // If current model isn't new and new primary key value != new value - mark model as new
                        $this->setIsNewRecord(true);
                    }
                }

                $this->attributes->setAttribute($attributeName, $value);
            } else {
                $this->related[$name] = $value;
            }
        }
//        else {
//            $this->attributesNotField[$name] = $value;
//            throw new Exception(get_class($this) . ' has no attribute named "' . $name . '".');
//        }

        return $this;
    }

    public function getFromQueryAttribute($name)
    {
        if (isset($this->attributesNotField[$name])) {
            return $this->attributesNotField[$name];
        }

        return null;
    }

    /**
     * @return array|int|null|string
     */
    public function getPrimaryKeyValues()
    {
        $keys = self::getPrimaryKeyName(true);
        $values = [];
        foreach ($keys as $name) {
            $values[$name] = $this->attributes->getAttribute($name);
        }
        return $values;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getOldAttribute($name)
    {
        return $this->attributes->getOldAttribute($name);
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function getAttributes()
    {
        $attributes = [];
        foreach (static::getMeta()->getAttributes() as $name) {
            $attributes[$name] = $this->attributes->getAttribute($name);
        }
        return $attributes;
    }

    /**
     * @return array
     */
    public function getOldAttributes()
    {
        return $this->attributes->getOldAttributes();
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasAttribute($name)
    {
        return \in_array($name, static::getMeta()->getAttributes(), true);
    }

    /**
     * @param array $attributes
     *
     * @throws Exception
     */
    public function setAttributes(array $attributes)
    {
        foreach ($attributes as $name => $value) {
            $this->setAttribute($name, $value);
        }
    }

    /**
     * Returns the bundle's container extension class.
     *
     * @return string
     */
    protected static function getManagerClass()
    {
        return static::getNamespace() . '\\' . static::classNameShort(). 'Manager';
    }

    /**
     * Gets the Bundle namespace.
     *
     * @return string The Bundle namespace
     */
    public static function getNamespace()
    {
        $class = static::class;
        return substr($class, 0, strrpos($class, '\\'));
    }

    /**
     * @param null|ModelInterface $instance
     * @return Manager
     */
    public static function objectsManager($instance = null)
    {
        if (!$instance) {
            $className = static::class;
            $instance = new $className;
        }

        if (class_exists($managerClass = self::getManagerClass())) {
            return new $managerClass($instance, $instance->getConnection());
        }

        return new Manager($instance, $instance->getConnection());
    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     * @throws Exception
     */
    public function __call($method, $args)
    {
        $manager = $method . 'Manager';
        if (method_exists($this, $manager)) {
            return \call_user_func_array([$this, $manager], array_merge([$this], $args));

        }

        if (method_exists($this, $method)) {
            return \call_user_func_array([$this, $method], $args);

        }

        throw new OrmExceptions('Call unknown method ' . $method);
    }

    /**
     * @param string $name
     * @param string $tablePrefix
     * @return string
     */
    public static function getRawTableName($name, $tablePrefix = '')
    {
        if (strpos($name, '{{') !== false) {
            $name = preg_replace('/\\{\\{(.*?)\\}\\}/', '\1', $name);
            return str_replace('%', $tablePrefix, $name);
        }

        return $name;
    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     * @throws Exception
     */
    public static function __callStatic($method, $args)
    {
        $manager = $method . 'Manager';
        $className = static::class;

        if ($method === 'tableName') {
            $tableName = \call_user_func([$className, $method]);
            return self::getRawTableName($tableName);

        }

        if (method_exists($className, $manager)) {
            return \call_user_func_array([$className, $manager], $args);

        }

        if (method_exists($className, $method)) {
            return \call_user_func_array([$className, $method], $args);

        }

        throw new OrmExceptions("Call unknown method {$method}");
    }

    /**
     * @return array
     */
    public static function getFields()
    {
        return [];
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function isValid()
    {
        $errors = [];
        $meta = static::getMeta();

        /* @var $field \Tsukasa\Orm\Fields\Field */
        foreach ($meta->getAttributes() as $name) {
            $field = $this->getField($name);

            if (
                $field instanceof AutoField ||
                $field instanceof ManyToManyField ||
                $field instanceof HasManyField
            ) {
                continue;
            }

            $field->setValue($this->getAttribute($field->getAttributeName()));

            if ($field->isValid() === false) {
                $errors[$name] = $field->getErrors();
            }
        }

        $this->setErrors($errors);
        return \count($errors) === 0;
    }

    /**
     * @param string $name
     * @return int|null|string
     */
    public function getAttribute($name)
    {
        $name = $this->convertToPrimaryKeyName($name);

        if ($this->hasAttribute($name)) {
            return $this->attributes->getAttribute($name);
        }

        if (isset($this->related[$name])) {
            return $this->related[$name];
        }
        return null;
    }

    /**
     * @param array $errors
     * @return $this
     */
    protected function setErrors(array $errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    abstract public function update(array $fields = []);

    abstract public function insert(array $fields = []);

    public function beforeSave($owner, $isNew) { }

    public function afterSave($owner, $isNew) { }

    public function beforeDelete($owner) { }

    public function afterDelete($owner) { }

    protected function beforeInsertInternal()
    {
        $meta = static::getMeta();
        foreach ($meta->getAttributes() as $name) {
            $field = $this->getField($name);
            $field->beforeInsert($this, $this->getAttribute($field->getAttributeName()));
        }

        $this->beforeSave($this, true);
    }

    protected function afterInsertInternal()
    {
        $meta = static::getMeta();
        foreach ($meta->getAttributes() as $name) {
            $field = $this->getField($name);
            $field->afterInsert($this, $this->getAttribute($field->getAttributeName()));
        }

        $this->afterSave($this, true);
        $this->attributes->reflectOldAttributes();
    }

    protected function beforeUpdateInternal()
    {
        $meta = static::getMeta();
        foreach ($meta->getAttributes() as $name) {
            $field = $this->getField($name);
            $field->beforeUpdate($this, $this->getAttribute($field->getAttributeName()));
        }

        $this->beforeSave($this, false);
    }

    protected function afterUpdateInternal()
    {
        $meta = static::getMeta();
        foreach ($meta->getAttributes() as $name) {
            $field = $this->getField($name);
            $field->afterUpdate($this, $this->getAttribute($field->getAttributeName()));
        }

        $this->afterSave($this, false);
    }

    /**
     * @param array $fields
     * @return bool
     */
    public function save(array $fields = [])
    {
        if ($this->getIsNewRecord()) {
            return $this->insert($fields);
        }

        return $this->update($fields);
    }

    protected function beforeDeleteInternal()
    {
        $meta = static::getMeta();
        foreach ($meta->getAttributes() as $name) {
            $field = $this->getField($name);
            $field->beforeDelete($this, $this->getAttribute($field->getAttributeName()));
        }
        $this->beforeDelete($this);
    }

    protected function afterDeleteInternal()
    {
        $meta = static::getMeta();
        foreach ($meta->getAttributes() as $name) {
            $field = $this->getField($name);
            $field->afterDelete($this, $this->getAttribute($field->getAttributeName()));
        }
        $this->afterDelete($this);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function delete()
    {
        $this->beforeDeleteInternal();
        $result = $this->objects()->delete($this->getPrimaryKeyValues());
        if ($result) {
            $this->afterDeleteInternal();
        }
        return $result;
    }

    /**
     * @param array $attributes
     * @return ModelInterface
     */
    public static function create(array $attributes = [])
    {
        $className = get_called_class();
        /** @var ModelInterface $model */
        $model = new $className($attributes);
        $model->setIsNewRecord(false);
        return $model;
    }

    /**
     * @return MetaData
     * @throws \ReflectionException
     */
    public static function getMeta()
    {
        return MetaData::getInstance(static::class);
    }

    /**
     * @return bool
     */
    public function getIsNewRecord()
    {
        return $this->isNewRecord;
    }

    /**
     * @param bool $value
     */
    public function setIsNewRecord($value)
    {
        $this->isNewRecord = $value;
        $this->attributes->resetOldAttributes();

        if ($value === false) {
            $this->attributes->reflectOldAttributes();
        }
    }

    /**
     * @deprecated
     * @return bool
     */
    public function getIsCreated()
    {
        return $this->isCreated;
    }

    /**
     * @deprecated
     * @param bool $value
     *
     * @return $this
     */
    public function setIsCreated($value)
    {
        $this->isCreated = $value;
        return $this;
    }

    /**
     * @param bool $asArray
     * @return array|string
     */
    public static function getPrimaryKeyName($asArray = false)
    {
        return static::getMeta()->getPrimaryKeyName($asArray);
    }

    /**
     * @param string $name
     *
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException|\Exception
     */
    protected function getFieldValue($name)
    {
        if ($field = $this->getField($name)) {
            if ($field->getSqlType()) {
                $platform = $this->getConnection()->getDatabasePlatform();

                $attributeValue = $this->getAttribute($field->getAttributeName());

                if ($name == $field->getAttributeName()) {
                    return $field->convertToPHPValueSQL($attributeValue, $platform);
                }

                return $field->convertToPHPValue($attributeValue, $platform);
            }

            return $field->getValue();
        }

        return null;
    }

    /**
     * @return string
     * @alias
     */
    public static function classNameShort()
    {
        $class = static::class;
        return substr($class, strrpos($class, '\\', 0));
    }

    /**
     * @return string
     * @deprecated
     */
    public static function getShortName()
    {
        return self::classNameShort();
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        $shortName = self::classNameShort();
        $shortName = str_replace('Model', '', $shortName);
        return self::normalizeTableName($shortName);
    }

    /**
     * @param string $tableName
     * @return string
     */
    public static function normalizeTableName($tableName)
    {
        return Inflector::tableize($tableName);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->hasField($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     * @throws \Exception
     */
    public function offsetGet($offset)
    {
        return $this->getFieldValue($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws Exception
     */
    public function offsetSet($offset, $value)
    {
        $this->setAttribute($offset, $value);
    }

    /**
     * @param mixed $offset
     * @throws Exception
     */
    public function offsetUnset($offset)
    {
        $this->setAttribute($offset, null);
    }

    /**
     * @param string|null $name Connection name from config
     * @return $this
     */
    public function using($name = null)
    {
        $this->using = $name;
        return $this;
    }

    /**
     * @return Connection
     * @throws Exception
     */
    public function getConnection()
    {
        return ConnectionManager::getInstance()
            ->getConnection($this->using);
    }

    /**
     * Update related models
     */
    public function updateRelated()
    {
        foreach ($this->related as $name => $value) {
            if ($value instanceof Manager) {
                continue;
            }

            /** @var \Tsukasa\Orm\Fields\RelatedField $field */
            $field = $this->getField($name);
            if ($field->editable)
            {
                if (empty($value)) {
                    $field->getManager()->clean();
                }
                else {
                    $field->setValue($value);
                }
            }
        }
        $this->related = [];
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize([
            'attributes' => $this->getAttributes()
        ]);
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @throws \Exception
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        $us = unserialize($serialized);

        $this->attributes = new AttributeCollection;
        $this->setAttributes($us['attributes']);
    }
}
