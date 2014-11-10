<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 8/15/2014
 * Time: 12:36 PM
 */

namespace Jigit;

/**
 * Data object
 *
 * @package Jigit
 */
class Data
{
    /**
     * Data
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        //Empty
    }

    /**
     * Get data
     *
     * @param string $key
     * @return array|string
     */
    public function getData($key = null)
    {
        if ($key) {
            return isset($this->_data[$key]) ? $this->_data[$key] : null;
        } else {
            return $this->_data;
        }
    }

    /**
     * Set data
     *
     * @param string $key
     * @param mixed  $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->_data = $key;
        } else {
            $this->_data[$key] = $value;
        }
        return $this;
    }

    /**
     * Add data
     *
     * @param array $data
     * @return $this
     */
    public function addData(array $data)
    {
        foreach ($data as $key => $value) {
            $this->setData($key, $value);
        }
        return $this;
    }

    /**
     * Get data array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getData();
    }
}
