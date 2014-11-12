<?php
namespace Lib\Model;

use Lib\Model\Data\DataAbstract;
use Lib\Model\Data\Resource;
use Lib\Record;

/**
 * Class Data
 *
 * @package Lib\Model
 */
abstract class Data extends DataAbstract
{
    /**
     * Namespace in resource data
     *
     * @var string
     */
    protected $_namespace;

    /**
     * Init resource
     *
     * @param string      $resourceName
     * @param string|null $namespace
     * @throws \Lib\Exception
     */
    protected function _init($resourceName = null, $namespace = null)
    {
        $this->_namespace = $namespace ?: strtolower(get_class($this));
        $this->_resource  = $this->getResource($resourceName);
    }

    /**
     * Get resource
     *
     * @param string $resourceName
     * @return Record
     * @throws \Lib\Exception
     */
    public function getResource($resourceName)
    {
        return Resource::getResource($resourceName);
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
        return $this->_resource->setData($this->_namespace, $key, $value);
    }

    /**
     * Get data from resource
     *
     * @param string|null $key
     * @return array|string
     */
    protected function _getData($key)
    {
        return $this->_resource->getData($this->_namespace, $key);
    }
}
