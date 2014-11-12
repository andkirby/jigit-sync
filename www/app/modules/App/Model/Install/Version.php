<?php
namespace App\Model\Install;

use Lib\Db\Install\VersionInterface;

/**
 * Class Version
 *
 * @package App\Model\Install
 */
class Version implements VersionInterface
{
    /**
     * Versions table
     */
    const VERSION_TABLE = 'app_version';

    /**
     * Module versions
     *
     * @var string
     */
    static protected $_moduleVersions;

    /**
     * Get version
     *
     * @param string $module
     * @return string|null
     */
    static public function getVersion($module)
    {
        self::initVersions();
        return isset(self::$_moduleVersions[$module]) ? self::$_moduleVersions[$module] : null;
    }

    /**
     * Get versions
     *
     * @return array
     */
    static public function getVersions()
    {
        self::initVersions();
        return self::$_moduleVersions;
    }

    /**
     * Init version
     *
     * @throws \Zend_Db_Select_Exception
     */
    public static function initVersions()
    {
        if (null === self::$_moduleVersions) {
            $table  = new \Zend_Db_Table(array('name' => self::VERSION_TABLE));
            $select = $table->select()
                ->from(self::VERSION_TABLE)
                ->where('module_name IN (?)', self::_getAvailableModules());
            self::$_moduleVersions = $table->getAdapter()->fetchPairs($select);
        }
    }

    /**
     * Get available modules
     *
     * @return array
     * @throws \Zend_Exception
     */
    protected static function _getAvailableModules()
    {
        /** @var \Zend_Config $config */
        $config = \Zend_Registry::get('config');
        return $config->app->sortedModules->toArray();
    }

    /**
     * Set version
     *
     * @param string $module
     * @param string $version
     */
    static public function setVersion($module, $version)
    {
        self::_saveVersionIntoDb($module, $version);
        self::$_moduleVersions[$module] = $version;
    }

    /**
     * Set version into database
     *
     * @param string $module
     * @param string $version
     */
    protected static function _saveVersionIntoDb($module, $version)
    {
        $table = self::VERSION_TABLE;
        $db = \Zend_Db_Table::getDefaultAdapter();
        $sql = <<<SQL
INSERT INTO `$table` (`module_name`, `module_version`)
    VALUES (:module_name, :module_version)
    ON DUPLICATE KEY UPDATE `module_version` = :module_version
SQL;
        $bind = array(
            'module_name'    => $module,
            'module_version' => $version,
        );
        $db->query($sql, $bind);
    }
}
