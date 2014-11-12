<?php
namespace Lib\Db;
use Lib\Db;
use Lib\Exception;
use Zend_Paginator;

/**
 * Class Table
 *
 * @package Lib\Db
 * @method Db\Pdo\Mysql getAdapter()
 */
class Table extends \Zend_Db_Table_Abstract
{
    /**
     * Select object
     *
     * @var
     */
    protected $_select;
    /**
     * Collection
     *
     * @var
     */
    protected $_collection;

    /**
     * Constructor. Set default row and rowset classes if they are not set
     *
     * @param array $config
     * @throws Exception
     */
    public function __construct($config = array())
    {
        if (!isset($config[self::ROW_CLASS])) {
            $config[self::ROW_CLASS] = __NAMESPACE__ . '\\Row';
        }
        if (!isset($config[self::ROW_CLASS])) {
            $config[self::ROWSET_CLASS] = __NAMESPACE__ . '\\Rowset';
        }
        if (!isset($config['name'])) {
            throw new Exception('Please define table name.');
        }
        parent::__construct($config);
    }

    /**
     * Get columns
     *
     * @return array
     */
    public function getCols()
    {
        return $this->_getCols();
    }

    /**
     * Truncate table
     *
     * @return $this
     * @throws \Zend_Db_Statement_Exception
     * @throws \Zend_Db_Table_Exception
     */
    public function truncate()
    {
        return $this->getAdapter()
            ->truncate($this->info(self::NAME));
    }

    /**
     * Return collection
     *
     * @return Zend_Paginator
     */
    public function getCollection()
    {
        if (null === $this->_collection) {
            $this->_collection = $this->_getPaginator($this->getSelect());
        }
        return $this->_collection;
    }

    /**
     * Return Paginator collection
     *
     * @param \Zend_Db_Select $select
     * @return Zend_Paginator
     */
    protected function _getPaginator(\Zend_Db_Select $select)
    {
        return  Zend_Paginator::factory($select);
    }

    /**
     * Return select object (singleton)
     *
     * @return \Zend_Db_Table_Select
     */
    public function getSelect()
    {
        if (null === $this->_select) {
            $this->_select = $this->_getDefaultSelect();
        }
        return $this->_select;
    }

    /**
     * Return select
     *
     * @return \Zend_Db_Table_Select
     */
    protected function _getDefaultSelect()
    {
        return $this->select()->from($this->_name);
    }
}
