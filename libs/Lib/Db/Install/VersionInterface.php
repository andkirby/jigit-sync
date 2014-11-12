<?php
namespace Lib\Db\Install;

/**
 * Interface VersionInterface
 *
 * Interface will push to make a model which will work version setting/getting
 *
 * @package Lib\Db\Install
 */
interface VersionInterface
{
    /**
     * Get version
     *
     * @param string $module
     * @return string
     * @static
     */
    public static function getVersion($module);

    /**
     * Get versions
     *
     * @return string
     * @static
     */
    public static function getVersions();

    /**
     * Set version
     *
     * @param string $module
     * @param string $version
     * @return void
     * @static
     */
    public static function setVersion($module, $version);
}
