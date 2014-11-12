<?php
namespace Lib\Db\Pdo;

/**
 * Class Adapter
 *
 * @package Lib\Db\Pdo
 */
class Mysql extends \Zend_Db_Adapter_Pdo_Mysql
{
    /**
     * Transaction level
     *
     * @var int
     */
    protected $_transactionLevel = 0;

    /**
     * Commit transaction
     *
     * @return $this
     */
    public function commit()
    {
        $this->_transactionLevelDown();
        if ($this->_isTopTransactionLevel()) {
            parent::commit();
        }
        return $this;
    }

    /**
     * Commit transaction
     *
     * @return $this
     */
    public function beginTransaction()
    {
        if ($this->_isTopTransactionLevel()) {
            parent::beginTransaction();
        }
        $this->_transactionLevelUp();
        return $this;
    }

    /**
     * Transaction level
     *
     * @return bool
     */
    protected function _isTopTransactionLevel()
    {
        return 0 === $this->_transactionLevel;
    }

    /**
     * Down transaction level incrementally
     *
     * @return int
     */
    protected function _transactionLevelDown()
    {
        $this->_transactionLevel--;
        return $this;
    }

    /**
     * Down transaction level incrementally
     *
     * @return int
     */
    protected function _transactionLevelUp()
    {
        $this->_transactionLevel++;
        return $this;
    }

    /**
     * Truncate table
     *
     * @param $tableName
     * @throws \Zend_Db_Statement_Exception
     * @return $this
     */
    public function truncate($tableName)
    {
        $this->query(
            'TRUNCATE TABLE ' . $this->quoteTableAs($tableName) . ';'
        );
        return $this;
    }
}
