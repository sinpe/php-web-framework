<?php

/*
 * This file is part of the long/framework package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework;

/**
 * ArrayObject
 *
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class ArrayObject extends \ArrayObject
{
    /**
     * __construct
     *
     * @param mixed $input
     */
    public function __construct ($input = []) 
    {
        parent::__construct($input);

        $this->setFlags(\ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * Set object item
     *
     * @param string $key   The data key
     * @param mixed  $value The data value
     */
    public function set($key, $value)
    {
        $this->{$key} = $value;
    }

    /**
     * Get object item for key
     *
     * @param string $key     The data key
     * @param mixed  $default The default value to return if data key does not exist
     *
     * @return mixed The key's value, or the default value
     */
    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->{$key} : $default;
    }

    /**
     * Get object keys
     *
     * @return array The object's source data keys
     */
    public function keys()
    {
        return array_keys((array)$this);
    }

    /**
     * Does this object have a given key?
     *
     * @param string $key The data key
     *
     * @return bool
     */
    public function has($key)
    {
        return isset($this->{$key});
    }

    /**
     * Dynamically access object proxies.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        $value = null;

        if ($this->has($key)) {
            $value = $this->get($key);
        }

        if ($value instanceof \ArrayAccess) {
            $value = new self($value);
        }

        return $value;
    }
}