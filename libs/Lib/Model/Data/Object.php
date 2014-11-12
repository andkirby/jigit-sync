<?php
namespace Lib\Model\Data;

use Lib\Exception;

/**
 * Class Object
 *
 * @package Lib\Model\Data
 */
class Object
{
    /**
     * Data
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Get data
     *
     * @param string $key
     * @throws Exception
     * @return array|string
     */
    public function getData($key = null)
    {
        if ($key) {
            if (!is_string($key)) {
                throw new Exception('The key parameter must be string.');
            }
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
}
