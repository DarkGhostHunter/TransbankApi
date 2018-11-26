<?php

namespace Transbank\Wrapper\Helpers;

use ArrayAccess;
use JsonSerializable;

/**
 * Class Fluent
 *
 * @package Transbank\Helpers
 */
class Fluent implements ArrayAccess, JsonSerializable
{
    /**
     * Attributes container.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Attributes to hide on serialization (JSON, array)
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Fluent constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            // If the attribute has a setter, use that
            if (method_exists($this, $method = 'set'. lcfirst($key) . 'Attribute')) {
                $this->$method($value);
            } else {
                $this->attributes[$key] = $value;
            }
        }
    }

    /**
     * Return the attribute from the attributes container.
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        // If there is a getter, use that instead of the offset
        if (method_exists($this, $method = 'get'. lcfirst($key) . 'Attribute')) {
            return $this->$method();
        }

        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }

        return $default;
    }

    /**
     * Returns all the attributes as an array.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Replace a set of Attributes for another
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Merge an array of attributes with the existing one
     *
     * @param array $attributes
     */
    public function mergeAttributes(array $attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);
    }

    /**
     * Transform the object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return Helpers::arrayExcept($this->attributes, $this->hidden);
    }

    /**
     * Transform the object to JSON.
     *
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Dynamically set an attribute as a function.
     *
     * @param string $method
     * @param array $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        $this->attributes[$method] = count($parameters) > 0 ? $parameters[0] : true;

        return $this;
    }

    /**
     * Return the value of an attribute.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Set attribute by the given value
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        // If there is a setter, use that instead of the offset
        $this->offsetSet($key, $value);
    }

    /**
     * Check if an attribute key is set.
     *
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Unset an attribute.
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }

    /**
     * Serializes the object to a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Serialize the object to something JSON-encodeable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Returns if the attribute exists.
     *
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * Return the value for the attribute.
     *
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Set the attribute by the given value.
     *
     * @param string $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (method_exists($this, $method = 'set'. lcfirst($offset) . 'Attribute')) {
            $this->$method($value);
        } else {
            $this->attributes[$offset] = $value;
        }
    }

    /**
     * Unset an attribute
     *
     * @param string $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }
}