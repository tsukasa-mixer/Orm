<?php

namespace Tsukasa\Orm\Helpers;

trait Accessor
{

    public function __get($name)
    {

        return $this->_innerGet($name);
    }

    public function __set($name, $value)
    {

        return $this->_innerSet($name, $value);
    }

    public function _innerGet($name)
    {

        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        if (property_exists($this, $name)) {
            return $this->{$name};
        }

        throw new \OutOfBoundsException('Unknown property ' . $name);
    }

    public function _innerSet($name, $value)
    {

        $method = 'set' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->{$method}($value);
        }

        if (property_exists($this, $name)) {
            $this->{$name} = $value;

            return true;
        }

        throw new \OutOfBoundsException('Unknown property ' . $name);
    }

    /**
     * Checks if the named property is set (not null).
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `isset($object->property)`.
     *
     * Note that if the property is not defined, false will be returned.
     *
     * @param string $name the property name or the event name
     *
     * @return boolean whether the named property is set (not null).
     */
    public function __isset($name)
    {

        return $this->_innerIsset($name);
    }

    public function _innerIsset($name)
    {

        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->{$getter}() !== null;
        }

        return false;
    }

    /**
     * Sets an object property to null.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `unset($object->property)`.
     *
     * Note that if the property is not defined, this method will do nothing.
     * If the property is read-only, it will throw an exception.
     *
     * @param string $name the property name
     *
     * @throws \OutOfBoundsException if the property is read only.
     */
    public function __unset($name)
    {

        $this->_innerUnset($name);
    }

    public function _innerUnset($name)
    {

        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->{$setter}(null);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new \OutOfBoundsException('Unsetting read-only property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * Calls the named method which is not a class method.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when an unknown method is being invoked.
     *
     * @param string $name the method name
     * @param array $params method parameters
     *
     * @throws \BadMethodCallException when calling unknown method
     * @return mixed the method return value
     */
    public function __call($name, $params)
    {
        return $this->_innerCall($name, $params);
    }

    public function _innerCall($name, $params)
    {
        throw new \BadMethodCallException('Unknown method: ' . get_class($this) . "::$name()");
    }

    /**
     * Returns a value indicating whether a property is defined.
     * A property is defined if:
     *
     * - the class has a getter or setter method associated with the specified name
     *   (in this case, property name is case-insensitive);
     * - the class has a member variable with the specified name (when `$checkVars` is true);
     *
     * @param string $name the property name
     * @param boolean $checkVars whether to treat member variables as properties
     *
     * @return boolean whether the property is defined
     * @see canGetProperty()
     * @see canSetProperty()
     */
    public function hasProperty($name, $checkVars = true)
    {

        return $this->canGetProperty($name, $checkVars) || $this->canSetProperty($name, false);
    }

    /**
     * Returns a value indicating whether a property can be read.
     * A property is readable if:
     *
     * - the class has a getter method associated with the specified name
     *   (in this case, property name is case-insensitive);
     * - the class has a member variable with the specified name (when `$checkVars` is true);
     *
     * @param string $name the property name
     * @param boolean $checkVars whether to treat member variables as properties
     *
     * @return boolean whether the property can be read
     * @see canSetProperty()
     */
    public function canGetProperty($name, $checkVars = true)
    {

        return (method_exists($this, 'get' . $name) || $checkVars) && property_exists($this, $name);
    }

    /**
     * Returns a value indicating whether a property can be set.
     * A property is writable if:
     *
     * - the class has a setter method associated with the specified name
     *   (in this case, property name is case-insensitive);
     * - the class has a member variable with the specified name (when `$checkVars` is true);
     *
     * @param string $name the property name
     * @param boolean $checkVars whether to treat member variables as properties
     *
     * @return boolean whether the property can be written
     * @see canGetProperty()
     */
    public function canSetProperty($name, $checkVars = true)
    {

        return (method_exists($this, 'set' . $name) || $checkVars) && property_exists($this, $name);
    }

    /**
     * Returns a value indicating whether a method is defined.
     *
     * The default implementation is a call to php function `method_exists()`.
     * You may override this method when you implemented the php magic method `__call()`.
     *
     * @param string $name the property name
     *
     * @return boolean whether the property is defined
     */
    public function hasMethod($name)
    {

        return method_exists($this, $name);
    }

}