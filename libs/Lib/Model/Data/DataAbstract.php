<?php
namespace Lib\Model\Data;

/**
 * Class DataAbstract
 *
 * @package Lib\Model
 * @abstract
 */
abstract class DataAbstract extends Object
{
    /**
     * Resource model
     *
     * @var mixed
     */
    protected $_resource;

    /**
     * This method must be implemented to init resource
     *
     * Please call protected _init()
     *
     * @return void
     */
    abstract public function init();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Init resource
     *
     * @param string $resourceName
     */
    protected function _init($resourceName = null)
    {
        $this->_resource = $this->getResource($resourceName);
    }

    /**
     * Get resource
     *
     * @param string $resourceName
     * @return mixed
     * @throws \Lib\Exception
     */
    abstract protected function getResource($resourceName);

    /**
     * Get data
     *
     * @param string $key
     * @return array|string
     */
    public function getData($key = null)
    {
        return $this->_getData($key);
    }

    /**
     * Set data
     *
     * @param string      $key
     * @param string|null $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        $this->_setData($key, $value);
        return $this;
    }

    /**
     * Set data into resource
     *
     * @param string|array $key
     * @param string $value
     * @return array
     */
    protected function _setData($key, $value)
    {
        return $this->_resource->setData($key, $value);
    }

    /**
     * Get data from resource
     *
     * @param string|null $key
     * @return array|string
     */
    protected function _getData($key)
    {
        return $this->_resource->getData($key);
    }
}
