<?php
namespace Lib\Config;

use Zend_Config;

/**
 * Class Node
 *
 * @package Lib\Config
 */
class Node extends \Zend_Config
{
    /**
     * Rewrite config to use self instance
     *
     * @param array $array
     * @param bool  $allowModifications
     */
    public function __construct(array $array, $allowModifications = false)
    {
        $this->_allowModifications = (boolean)$allowModifications;
        $this->_loadedSection      = null;
        $this->_index              = 0;
        $this->_data               = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->_data[$key] = new Node($value, $this->_allowModifications);
            } else {
                $this->_data[$key] = $value;
            }
        }
        $this->_count = count($this->_data);
    }

    /**
     * Check array is associative
     *
     * @param array $arr
     * @return bool
     */
    protected function _isAssoc($arr)
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * Merge config
     *
     * It updated to use class node instead default one.
     * Also added merging non-associative arrays via adding new elements.
     *
     * @param Zend_Config $merge
     * @return $this|Zend_Config
     */
    public function merge(Zend_Config $merge)
    {
        if ($this->_isNonAssociativeMerge($merge)) {
            $this->_nonAssocMerge($merge);
            return $this;
        }
        foreach ($merge as $key => $item) {
            if (array_key_exists($key, $this->_data)) {
                if ($item instanceof Zend_Config && $this->$key instanceof Zend_Config) {
                    $this->$key = $this->$key->merge(new Node($item->toArray(), !$this->readOnly()));
                } else {
                    $this->$key = $item;
                }
            } else {
                if ($item instanceof Zend_Config) {
                    $this->$key = new Node($item->toArray(), !$this->readOnly());
                } else {
                    $this->$key = $item;
                }
            }
        }

        return $this;
    }

    /**
     * Only allow setting of a property if $allowModifications
     * was set to true on construction. Otherwise, throw an exception.
     *
     * @param  string $name
     * @param  mixed  $value
     * @throws \Zend_Config_Exception
     * @return void
     */
    public function __set($name, $value)
    {
        if ($this->_allowModifications) {
            if (is_array($value)) {
                $this->_data[$name] = new self($value, true);
            } else {
                $this->_data[$name] = $value;
            }
            $this->_count = count($this->_data);
        } else {
            /** @see Zend_Config_Exception */
            require_once 'Zend/Config/Exception.php';
            throw new \Zend_Config_Exception('Zend_Config is read only');
        }
    }

    /**
     * Make non-associative merge
     *
     * @param Zend_Config $merge
     * @return $this
     */
    protected function _nonAssocMerge(Zend_Config $merge)
    {
        foreach ($merge as $key => $item) {
            $this->_data[] = $item;
        }
        return $this;
    }

    /**
     * Check if target and destination arrays are non-assoc arrays
     *
     * @param Zend_Config $merge
     * @return bool
     */
    protected function _isNonAssociativeMerge(Zend_Config $merge)
    {
        return !$this->_isAssoc($this->_data) && !$this->_isAssoc($merge->toArray());
    }

    /**
     * Get data
     *
     * @param string $key
     * @param bool   $asArray
     * @throws Exception
     * @return $this|mixed
     */
    public function getData($key = null, $asArray = true)
    {
        if (null === $key) {
            $value = $this;
        } else {
            if (strpos($key, '/')) {
                $arr = explode('/', $key);
                $key = array_shift($arr);
                if (!is_object($this->$key)) {
                    return null;
                }
                $value = $this->$key->getData(implode('/', $arr), $asArray);
            } else {
                $value = $this->$key;
            }
        }
        if ($asArray && $value instanceof Node) {
            $value = $value->toArray();
        }
        return $value;
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
            if (strpos($key, '/')) {
                $arr = explode('/', $key);
                $key = array_shift($arr);
                if (null === $this->$key) {
                    $this->$key = new Node(array(), true);
                }
                $this->$key->setData(implode('/', $arr), $value);
            } else {
                $this->$key = $value;
            }
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
