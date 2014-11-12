<?php
namespace Lib\Db;

use Lib\Model\Data\DataAbstract;
use Lib\Exception;
use Lib\Model\Data\Object;

/**
 * Class Data
 *
 * @package Lib\Model
 */
abstract class ModelAbstract extends DataAbstract
{
    /**
     * Resource model
     *
     * @var Resource
     */
    protected $_resource;

    /**
     * Constructor. Set data
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        if (isset($config['data'])) {
            $this->setData((array)$config['data']);
        }
        if (!empty($config['table'])) {
            $this->_setResource($config['table']);
        }

        parent::__construct();
    }

    /**
     * Get array data from resource
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getResource()->toArray();
    }

    /**
     * Init resource
     *
     * @param string|null $tableName
     * @param string|null $resourceClass
     */
    protected function _init($tableName = null, $resourceClass = null)
    {
        $this->_setResource($tableName, $resourceClass);
    }

    /**
     * Get resource
     *
     * @param string $name
     * @return \Lib\Db\Row
     */
    public function getResource($name = null)
    {
        return $this->_resource;
    }

    /**
     * Get row data
     *
     * @param string|null $key
     * @throws Exception
     * @return array
     */
    public function getRowData($key = null)
    {
        if (null !== $key) {
            if (!in_array($key, $this->getResource()->getCols())) {
                throw new Exception("Row $key column not found.");
            }
            return $this->getData($key);
        }

        $data = array();
        foreach ($this->getResource()->getCols() as $column) {
            $data[$column] = $this->getData($column);
        }
        return $data;
    }

    /**
     * Get row class name
     *
     * @return string
     * @throws Exception
     */
    protected function _getResourceClassName()
    {
        $class = get_class($this);
        $pos   = strpos($class, 'Model');
        if ($pos === false) {
            throw new Exception('Unknown model namespace.');
        }
        $lengthModelName = 5; //length of string "Model"
        return substr_replace($class, 'Resource', $pos, $lengthModelName);
    }

    /**
     * Set resource
     *
     * @param string               $tableName
     * @param string|null|Resource $resourceClass
     * @return $this
     * @throws Exception
     */
    protected function _setResource($tableName, $resourceClass = null)
    {
        if (null !== $this->_resource) {
            return $this;
        }
        $resourceClass = $resourceClass ?: $this->_getResourceClassName();
        if (is_string($resourceClass)) {
            $config   = array(
                'table'                      => $tableName,
                'data'                       => $this->getData(),
                'tableClass'                 => $this->_getTableClassName(),
//                \Zend_Db_Table::ROWSET_CLASS => $this->_getRowsetClassName(),
                \Zend_Db_Table::ROW_CLASS    => get_class($this),
            );
            $resource = new $resourceClass($config);
        } else {
            $resource = $resourceClass;
        }
        if (!($resource instanceof Row)) {
            throw new Exception('Unknown row class.');
        }
        $this->_resource = $resource;
        return $this;
    }

    /**
     * Get rowset
     *
     * @return Table
     */
    public function getTable()
    {
        return $this->getResource()->getTable();
    }

    /**
     * Load
     *
     * @param int|string  $value
     * @param string|null $key
     * @return $this
     */
    public function load($value, $key = null)
    {
        $this->_beforeLoad();
        $this->getResource()->load($value, $key, $this);
        $this->_afterLoad();
        return $this;
    }

    /**
     * Save
     *
     * @return $this
     * @throws Exception
     */
    public function save()
    {
        $this->_beforeSave();
        $this->getResource()
            ->setData($this->getRowData())
            ->save();
        $this->setId(
            $this->getResource()->getId()
        );
        $this->_afterSave();
        return $this;
    }

    /**
     * Delete
     *
     * @return $this
     * @throws \Zend_Db_Table_Row_Exception
     */
    public function delete()
    {
        if ($this->getId()) {
            $this->getResource()->delete();
            $this->setData(array());
        }
        return $this;
    }

    /**
     * Get data
     *
     * @param null|string $key
     * @return array|object|string
     */
    public function getData($key = null)
    {
        return Object::getData($key);
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
        return Object::setData($key, $value);
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(
            $this->getResource()->getPrimaryCol()
        );
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return ModelAbstract
     */
    public function setId($id)
    {
        return $this->setData(
            $this->getResource()->getPrimaryCol(),
            $id
        );
    }

    /**
     * Get rowset class name
     *
     * @return string
     */
    protected function _getTableClassName()
    {
        return $this->_getResourceClassName() . '\\Table';
    }
    /**
     * BeforeLoad method
     */
    protected function _beforeLoad()
    {
    }

    /**
     * AfterLoad method
     */
    protected function _afterLoad()
    {
    }

    /**
     * BeforeSave method
     */
    protected function _beforeSave()
    {
    }

    /**
     * AfterSave method
     */
    protected function _afterSave()
    {
    }
}
