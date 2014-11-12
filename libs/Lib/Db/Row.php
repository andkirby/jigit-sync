<?php
namespace Lib\Db;

use Lib\Exception;
use Lib\Model\Data\DataAbstract;

/**
 * Class Resource
 *
 * @package Lib\Db
 * @method Table getTable
 * @method Table _getTable
 */
class Row extends \Zend_Db_Table_Row_Abstract
{
    /**
     * Use default rowset class
     *
     * @var bool
     */
    protected $_defaultRowset = true;

    /**
     * Constructor. Set default class if it's not set
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $this->_tableClass = isset($config['tableClass']) ? $config['tableClass'] : __NAMESPACE__ . '\\Table';

        if (isset($config['table']) && is_string($config['table'])) {
            $config['table'] = $this->_initTable($config);
        }

        parent::__construct($config);

        if (!$this->_data) {
            $this->_resetData();
        }
    }

    /**
     * Init table model
     *
     * @param array $config
     * @return array
     * @throws Exception
     */
    protected function _initTable(array $config)
    {
        $adapter    = \Zend_Db_Table::getDefaultAdapter();
        $tableClass = $this->_tableClass;

        if (!isset($config[\Zend_Db_Table::ROW_CLASS])) {
            $config[\Zend_Db_Table::ROW_CLASS] = $this->_getModelClassName();
        }
        if (!isset($config[\Zend_Db_Table::ROWSET_CLASS])) {
            $config[\Zend_Db_Table::ROWSET_CLASS] = $this->_getRowsetClassName();
        }

        $tableConfig = array(
            \Zend_Db_Table::NAME         => $config['table'],
            \Zend_Db_Table::ROW_CLASS    => $config[\Zend_Db_Table::ROW_CLASS],
            \Zend_Db_Table::ROWSET_CLASS => $config[\Zend_Db_Table::ROWSET_CLASS],
            \Zend_Db_Table::ADAPTER      => $adapter
        );
        return new $tableClass($tableConfig);
    }

    /**
     * Get row class name
     *
     * @return string
     * @throws Exception
     */
    protected function _getModelClassName()
    {
        $class = get_class($this);
        $pos   = strpos($class, 'Resource');
        if ($pos === false) {
            throw new Exception('Unknown resource namespace.');
        }
        $lengthModelName = 8; //length of string "Resource"
        return substr_replace($class, 'Model', $pos, $lengthModelName);
    }

    /**
     * Get row class name
     *
     * @return string
     * @throws Exception
     */
    protected function _getRowsetClassName()
    {
        return $this->_defaultRowset
            ? __NAMESPACE__ . '\\Rowset'
            : get_class($this) . '\\Rowset';
    }

    /**
     * Get data
     *
     * @param string|null $key
     * @return array|string|null
     */
    public function getData($key = null)
    {
        if (null === $key) {
            return $this->toArray();
        }
        return $this->$key;
    }

    /**
     * Set data
     *
     * @param string|array         $key
     * @param int|bool|string|null $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $data = $key;
        } else {
            $data = array($key => $value);
        }
        foreach ($data as $key => $value) {
            $this[$key] = $value;
        }
        return $this;
    }

    /**
     * Save with transaction
     *
     * @return int
     */
    public function save()
    {
        $this->_beforeSave();
        $this->getTable()->getAdapter()->beginTransaction();
        $primary = parent::save();
        $this->getTable()->getAdapter()->commit();
        $this->_afterSave();
        return $primary;
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        $primary = $this->getPrimaryKey();
        return reset($primary);
    }

    /**
     * Get cols
     *
     * @return array
     */
    public function getCols()
    {
        return $this->_getTable()->getCols();
    }

    /**
     * Load data
     *
     * @param string|int $value
     * @param string|null $column
     * @return $this
     * @throws \Exception
     * @throws \Zend_Db_Table_Row_Exception
     */
    public function load($value, $column = null, DataAbstract $model = null)
    {
        $this->_beforeLoad();
        if (null === $column) {
            $this->_setPrimaryKey($value);
            $this->_refresh();
        } else {
            $row = $this->_getTable()->fetchRow(
                $this->_getTable()->getAdapter()
                    ->quoteInto("$column = ?", $value, \Zend_Db::INT_TYPE)
            );
            $data = $row ? $row->toArray() : array();

            $this->_data = $data;
            $this->_cleanData = $this->_data;
            $this->_modifiedFields = array();
        }
        if ($model) {
            $model->addData($this->_data);
        }
        $this->_afterLoad();
        return $this;
    }

    /**
     * Refresh object data. Catch the exception when cannot load entry
     *
     * @throws \Exception
     * @throws \Zend_Db_Table_Row_Exception
     */
    protected function _refresh()
    {
        try {
            parent::_refresh();
        } catch (\Zend_Db_Table_Row_Exception $e) {
            if ($e->getMessage() !== 'Cannot refresh row as parent is missing') {
                throw $e;
            }
            //reset data when row not found
            $this->reset();
        }
    }

    /**
     * Get primary column
     *
     * @return string
     */
    public function getPrimaryCol()
    {
        return reset($this->_primary);
    }

    /**
     * Get primary key values
     *
     * @param bool $useDirty
     * @return array
     * @throws \Zend_Db_Table_Row_Exception
     */
    protected function _getPrimaryKey($useDirty = true)
    {
        if (is_array($this->_primary) && !$this->_data) {
            $this->_data = array(reset($this->_primary) => null);
        }
        return parent::_getPrimaryKey($useDirty);
    }

    /**
     * Truncate table
     *
     * @return $this
     */
    protected function _truncate()
    {
        $this->_getTable()->truncate();
        $this->reset();
        return $this;
    }

    /**
     * Reset object data
     *
     * @return $this
     */
    public function reset()
    {
        $this->_modifiedFields
            = $this->_cleanData = array();
        $this->_resetData();
        return $this;
    }

    /**
     * Get primary key
     *
     * @param int $value
     * @return $this
     */
    protected function _setPrimaryKey($value)
    {
        $this->_data[$this->getPrimaryCol()] = $value ? (int)$value : null;
        return $this;
    }

    /**
     * Reset data
     *
     * @return $this
     */
    protected function _resetData()
    {
        foreach ($this->getCols() as $column) {
            $this->_data[$column] = null;
        }
        $this->_setPrimaryKey(null);
        return $this;
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
