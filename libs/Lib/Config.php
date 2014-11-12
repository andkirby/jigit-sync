<?php
namespace Lib;

/**
 * Class Config
 *
 * @package Lib
 */
class Config
{
    /**
     * Default main module
     */
    const DEFAULT_MAIN_MODULE = 'App';

    /**
     * Config file extension
     */
    const CONFIG_EXTENSION = 'ini';

    /**
     * Main module name
     *
     * @var string
     */
    protected $_mainModuleName;

    /**
     * Set module name
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        $this->_mainModuleName = isset($options['main_module']) ? $options['main_module'] : self::DEFAULT_MAIN_MODULE;
    }

    /**
     * Get base config
     *
     * @param string $baseDir
     * @param string $namespace
     * @param array  $options
     * @return \Zend_Config_Ini
     */
    protected function _getBaseConfig($baseDir, $namespace, array $options = array())
    {
        $options['allowModifications'] = true;
        $config = $this->_getConfigFromFile(
            $this->_getEtcConfigFile($baseDir, 'config.' . $this->_getConfigExtension()), $namespace, $options
        );
        return $config;
    }

    /**
     * Get application config
     *
     * @param string $baseDir
     * @param string $namespace
     * @param array  $options
     * @param bool   $useLocal
     * @return \Zend_Config_Ini
     */
    public function getApplicationConfig($baseDir, $namespace, array $options = array(), $useLocal = true)
    {
        //load app/etc/config.ini file
        $config = $this->_getBaseConfig($baseDir, $namespace, $options);

        if ($useLocal) {
            //load app/etc/local.ini file
            $this->_initLocalConfig($baseDir, $config);
        }

        //load app/etc/modules/ModuleName.ini file
        $this->_initModuleLoadConfig(
            $config->app->modulesListDir,
            $config
        );

        //set sorted and active modules names list
        $this->_initModuleSortedList($config);

        //load ModuleName/etc/config.ini file
        $this->_initModulesConfig($config);
        return $config;
    }

    /**
     * Get target install files
     *
     * @param string       $directory
     * @param \Zend_Config $config
     * @return array
     */
    protected function _initModuleLoadConfig($directory, \Zend_Config $config)
    {
        $handle = opendir($directory);
        if ($handle) {
            while (false !== ($entry = readdir($handle))) {
                if ($this->_getConfigExtension() == pathinfo($entry, PATHINFO_EXTENSION)) {
                    $file         = $directory . DIRECTORY_SEPARATOR . $entry;
                    $moduleConfig = $this->_getConfigFromFile($file);
                    $config->merge($moduleConfig);
                }
            }
            closedir($handle);
        }
        return $this;
    }

    /**
     * Merge module config file
     *
     * @param \Zend_Config $config
     * @return array
     */
    protected function _initModulesConfig(\Zend_Config $config)
    {
        $modulesDir = $config->app->modulesDir;
        foreach ($config->app->sortedModules as $moduleName) {
            $file = $modulesDir . DIRECTORY_SEPARATOR . $moduleName
                . 'etc' . DIRECTORY_SEPARATOR . 'config.' . $this->_getConfigExtension();
            if (is_file($file)) {
                $moduleConfig = $this->_getConfigFromFile($file);
                $config->merge($moduleConfig);
            }
        }
        return $this;
    }

    /**
     * Init sorted and enabled modules
     *
     * @param \Zend_Config $config
     * @return array
     */
    protected function _initModuleSortedList(\Zend_Config $config)
    {
        $config->app->sortedModules = $this->_getEnabledSortedModules($config->app->modules);
        return $this;
    }

    /**
     * Get target install files
     *
     * @param string       $directory
     * @param \Zend_Config $config
     * @return array
     */
    protected function _initModuleConfig($directory, \Zend_Config $config)
    {
        $files  = array();
        $handle = opendir($directory);
        if ($handle) {
            while (false !== ($entry = readdir($handle))) {
                if ($this->_getConfigExtension() == pathinfo($entry, PATHINFO_EXTENSION)) {
                    $moduleConfig = $this->_getConfigFromFile($directory . DIRECTORY_SEPARATOR . $entry);
                    $config->merge($moduleConfig);
                }
            }
            closedir($handle);
        }
        return $files;
    }

    /**
     * Get config main file
     *
     * @param string $baseDir
     * @param        $filename
     * @return string
     */
    protected function _getEtcConfigFile($baseDir, $filename)
    {
        return rtrim($baseDir, '\\/') . '/app/etc/' . $filename;
    }

    /**
     * Get config by file
     *
     * @param string        $file
     * @param string|null   $namespace
     * @param array         $options
     * @return \Zend_Config_Ini
     */
    protected function _getConfigFromFile($file, $namespace = null, array $options = array())
    {
        return new \Zend_Config_Ini($file, $namespace, $options);
    }

    /**
     * Get active sorted modules
     *
     * @param \Zend_Config $modulesConfig
     * @throws Exception
     * @throws Sort\Exception
     * @return array
     */
    protected function _getEnabledSortedModules(\Zend_Config $modulesConfig)
    {
        $sorter = $this->_getSorter();
        $this->_addModulesToSorting($sorter, $modulesConfig);

        try {
            return $sorter->getSortedNodes();
        } catch (Sort\Exception $e) {
            if ($e->getCode() === Sort\Top::ERROR_CYCLIC_EDGES) {
                throw new Exception('Module dependencies error (cyclic dependency): ' . $e->getMessage());
            } elseif ($e->getCode() === Sort\Top::ERROR_NOT_ADDED_EDGE_NODE) {
                throw new Exception('Module dependencies error (module disabled or not exists): ' . $e->getMessage());
            }
            throw $e;
        }
    }

    /**
     * Check if module is main
     *
     * @param string $moduleName
     * @return bool
     */
    protected function _isMainModule($moduleName)
    {
        return $moduleName == $this->_mainModuleName;
    }

    /**
     * Get module dependencies
     *
     * @param Zend_Config $moduleConfig
     * @param string $moduleName
     * @return array
     */
    protected function _getDependedModules($moduleConfig, $moduleName)
    {
        $dependsOn = array($this->_mainModuleName);
        if ($moduleConfig->depends) {
            foreach ($moduleConfig->depends as $module => $status) {
                if ($this->_isMainModule($moduleName) || !$status) {
                    continue;
                }
                $dependsOn[] = $module;
            }
        }
        return $dependsOn;
    }

    /**
     * Check module enabled
     *
     * @param \Zend_Config|null $moduleConfig
     * @return bool
     */
    protected function _isModuleEnabled($moduleConfig)
    {
        return $moduleConfig && $moduleConfig->status;
    }

    /**
     * Get sorted module names
     *
     * @param \Zend_Config $modulesConfig
     * @return array
     */
    protected function _getSortedModuleNames(\Zend_Config $modulesConfig)
    {
        $modules = $modulesConfig->toArray();
        $modules = array_keys($modules);
        sort($modules);
        return $modules;
    }

    /**
     * Add modules into sorter
     *
     * @param Sort\Top     $sorter
     * @param \Zend_Config $modulesConfig
     */
    protected function _addModulesToSorting($sorter, \Zend_Config $modulesConfig)
    {
        //add main module first
        $sorter->addNode($this->_mainModuleName);

        $modules = $this->_getSortedModuleNames($modulesConfig);

        foreach ($modules as $moduleName) {
            $moduleConfig = $modulesConfig->$moduleName;
            if (!$this->_isModuleEnabled($moduleConfig) || $this->_isMainModule($moduleName)) {
                //skip disabled modules and main module
                continue;
            }
            $sorter->addNode(
                $moduleName,
                $this->_getDependedModules($moduleConfig, $moduleName)
            );
        }
    }

    /**
     * Get sorter
     *
     * @return Sort\Top
     * @throws Sort\Exception
     */
    protected function _getSorter()
    {
        $sorter = new Sort\Top();
        $sorter->enableModeSingleNonEdgeNode(true);
        return $sorter;
    }

    /**
     * Init local config
     *
     * @param string $baseDir
     * @param \Zend_Config $config
     * @return $this
     */
    protected function _initLocalConfig($baseDir, \Zend_Config $config)
    {
        $configLocal = $this->_getConfigFromFile(
            $this->_getEtcConfigFile($baseDir, 'local.' . $this->_getConfigExtension())
        );
        $config->merge($configLocal);
        return $this;
    }

    /**
     * Config files extension
     *
     * @return string
     */
    protected function _getConfigExtension()
    {
        return self::CONFIG_EXTENSION;
    }
}
