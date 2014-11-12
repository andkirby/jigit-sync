<?php
namespace App\Model\Install;

use Lib\Db\Install\VersionInterface;
use Lib\Record;

/**
 * Class Version
 *
 * @package App\Model\Install
 */
class VersionIni implements VersionInterface
{
    /**
     * Module versions
     *
     * @var string
     */
    static protected $_moduleVersions;

    /**
     * Record model
     *
     * @var Record
     */
    static protected $_config;

    /**
     * Get config
     *
     * @return Record
     */
    protected static function _getConfig()
    {
        if (null === self::$_config) {
            self::$_config = new Record(
                array(
                    'dir'      => 'app/etc',
                    'filename' => 'install.ini',
                )
            );
        }
        return self::$_config;
    }

    /**
     * Get version
     *
     * @param string $module
     * @return string|null
     */
    static public function getVersion($module)
    {
        return self::_getConfig()->getData('version', $module);
    }

    /**
     * Get versions
     *
     * @return array
     */
    static public function getVersions()
    {
        return self::_getConfig()->getData('version');
    }

    /**
     * Set version
     *
     * @param string $module
     * @param string $version
     */
    static public function setVersion($module, $version)
    {
        self::_getConfig()->setData('version', $module, $version);
    }
}
