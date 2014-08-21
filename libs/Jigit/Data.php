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
     * Get data by key
     *
     * @param string $key
     * @return mixed
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
     * Set data
     *
     * @param string|array $key
     * @param mixed $value
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
}
