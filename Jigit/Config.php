<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 12.08.2014
 * Time: 3:47
 */

namespace Jigit;

class Config
{
    /**
     * Instance
     *
     * @var Config
     */
    static protected $_instance;

    /**
     * Data
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Get instance
     *
     * @return Config
     */
    static public function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Get data by key
     *
     * @param string $key
     * @return null
     */
    public function getData($key = null)
    {
        if (null === $key) {
            return $this->_data;
        }

        $key = (string) $key;
        if (isset($this->_data[$key])) {
            return $this->_data[$key];
        }
        return null;
    }

    /**
     * Set data item
     *
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function setData($key, $value)
    {
        $this->_data[$key] = $value;
        return $this;
    }
} 
