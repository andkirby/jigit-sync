<?php
/**
 * Created by PhpStorm.
 * User: a.roslik
 * Date: 10/7/2014
 * Time: 2:24 PM
 */

namespace Lib\Db;

use Lib\Db\Install\VersionInterface;

/**
 * Class Install
 *
 * @package Lib\Db
 */
class Install
{
    /**
     * Default module version
     */
    const DEFAULT_MODULE_VERSION = '0.0.0';

    /**
     * DB adapter
     *
     * @var \Zend_Db_Adapter_Abstract
     */
    protected $_adapter;

    /**
     * Version reader
     *
     * @var VersionInterface
     */
    protected $_version;

    /**
     * Modules to install
     *
     * @var \Zend_Config
     */
    protected $_modules = array();

    /**
     * Set db adapter
     *
     * @param \Zend_Db_Adapter_Abstract $adapter
     * @param \Zend_Config|array        $modules
     * @param VersionInterface          $version
     */
    public function __construct(\Zend_Db_Adapter_Abstract $adapter, $modules, VersionInterface $version)
    {
        $this->_adapter = $adapter;
        $this->_modules = $modules instanceof \Zend_Config ? $modules->toArray() : $modules;
        $this->_version = $version;
    }

    /**
     * Install
     */
    public function install()
    {
        /** @var \Zend_Config $moduleConfig */
        foreach ($this->getModules() as $moduleName) {
            $this->_getModuleInstalledVersion($moduleName);
            $files = $this->_getTargetInstallFiles(
                $moduleName, $this->_getModuleInstalledVersion($moduleName)
            );
            $this->_performQueryFiles($files, $moduleName);
        }
        return $this;
    }

    /**
     * Get module version
     *
     * @param string $moduleName
     * @return string
     */
    protected function _getModuleInstalledVersion($moduleName)
    {
        return $this->_getVersion()->getVersion($moduleName) ?: self::DEFAULT_MODULE_VERSION;
    }

    /**
     * Get install dir
     *
     * @param string $moduleName
     * @return string
     */
    protected function _getInstallDir($moduleName)
    {
        return $this->_getModuleDir($moduleName) . DIRECTORY_SEPARATOR . 'install';
    }

    /**
     * Get target install files
     *
     * @param string $moduleName
     * @return array
     */
    protected function _getTargetInstallFiles($moduleName)
    {
        $files          = array();
        $installedVersion = $this->_getModuleInstalledVersion($moduleName);
        $installDir     = $this->_getInstallDir($moduleName);
        $currentVersion = $this->_getModuleCurrentVersion($moduleName);
        if (!is_dir($installDir) || !version_compare($installedVersion, $currentVersion, '<')) {
            return $files;
        }
        $handle = opendir($installDir);
        if ($handle) {
            while (false !== ($entry = readdir($handle))) {
                if ($this->_isFileShouldExecuted($installedVersion, $entry)) {
                    $files[$this->_getFileVersion($entry)]
                        = $installDir . DIRECTORY_SEPARATOR . $entry;
                }
            }
            closedir($handle);
        }
        ksort($files);
        return $files;
    }

    /**
     * Get module dir
     *
     * @param $moduleName
     * @return string
     */
    protected function _getModuleDir($moduleName)
    {
        return $this->_getModulesDir() . DIRECTORY_SEPARATOR . $moduleName;
    }

    /**
     * Get modules base dir
     *
     * @return string
     */
    protected function _getModulesDir()
    {
        return $this->_getBaseDir() . DIRECTORY_SEPARATOR . 'app';
    }

    /**
     * Check if query file should be executed
     *
     * @param string $installedVersion
     * @param string $entry
     * @return bool
     */
    protected function _isFileShouldExecuted($installedVersion, $entry)
    {
        if ('sql' === strtolower(pathinfo($entry, PATHINFO_EXTENSION))) {
            $version = $this->_getFileVersion($entry);
            return (bool)version_compare($installedVersion, $version, '<');
        }
        return false;
    }

    /**
     * Get version from filename
     *
     * @param string $entry
     * @return mixed
     */
    protected function _getFileVersion($entry)
    {
        preg_match('/(([0-9]+\.)+[0-9]+)/', $entry, $matches);
        return $matches[0];
    }

    /**
     * Get module version file
     *
     * @param string $moduleName
     * @return string
     */
    protected function _getModuleVersionFile($moduleName)
    {
        return $this->_getModuleDir($moduleName) . DIRECTORY_SEPARATOR
        . 'install' . DIRECTORY_SEPARATOR . 'version.txt';
    }

    /**
     * Get current version of module
     *
     * @param string $moduleName
     * @return string
     */
    protected function _getModuleCurrentVersion($moduleName)
    {
        $file = $this->_getModuleVersionFile($moduleName);
        if (!is_file($file)) {
            return self::DEFAULT_MODULE_VERSION;
        }
        return trim(file_get_contents($file));
    }

    /**
     * Get SQL
     *
     * @param string $file
     * @return string
     */
    protected function _getSql($file)
    {
        return file_get_contents($file);
    }

    /**
     * Get base dir
     *
     * @return string
     */
    protected function _getBaseDir()
    {
        return APP_ROOT;
    }

    /**
     * Get config
     *
     * @return VersionInterface
     */
    protected function _getVersion()
    {
        return $this->_version;
    }

    /**
     * Write installed version
     *
     * @param string $moduleName
     * @param string $version
     * @return void
     */
    protected function _writeInstalledVersion($moduleName, $version)
    {
        $this->_getVersion()->setVersion($moduleName, $version);
    }

    /**
     * Modules list
     *
     * @return \Zend_Config
     */
    public function getModules()
    {
        return $this->_modules;
    }

    /**
     * Perform install files
     *
     * @param array  $files
     * @param string $moduleName
     * @return $this
     */
    protected function _performQueryFiles($files, $moduleName)
    {
        foreach ($files as $version => $file) {
            $sql = $this->_getSql($file);
            $this->_adapter->beginTransaction();
            $this->_adapter->query($sql);
            $this->_writeInstalledVersion($moduleName, $version);
            $this->_adapter->commit();
        }
        return $this;
    }
}
