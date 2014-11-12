<?php
namespace Lib\Db;

/**
 * Class Rowset
 *
 * @package Lib\Db
 */
class Rowset extends \Zend_Db_Table_Rowset
{
    /**
     * Constructor. Set default row and table classes if they are not set
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->_tableClass = isset($config['tableClass']) ? $config['tableClass'] : __NAMESPACE__ . '\\Table';

        if (!isset($config[\Zend_Db_Table::ROW_CLASS])) {
            $config[\Zend_Db_Table::ROW_CLASS] = __NAMESPACE__ . '\\Row';
        }

        parent::__construct($config);
    }
}
