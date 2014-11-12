<?php
namespace App\Helper;

/**
 * Class HelperAbstract
 *
 * @package App\Helper
 */
class Base
{
    /**
     * Get config
     *
     * @return \Zend_Config
     * @throws \Zend_Exception
     */
    public function getConfig()
    {
        return \Zend_Registry::get('config');
    }

    /**
     * Get active modules
     *
     * @return array
     * @throws \Zend_Exception
     */
    public function getActiveModules()
    {
        $modules = array();
        /** @var \Zend_Config $config */
        foreach ($this->getConfig()->app->modules as $module => $config) {
            if ($config->status) {
                $modules[] = $module;
            }
        }
        return $modules;
    }

    /**
     * Check active module
     *
     * @param string $module
     * @return bool
     */
    public function isModuleActive($module)
    {
        if ($this->getConfig()->app->$module) {
            return (bool)$this->getConfig()->app->$module->status;
        }
        return false;
    }
}
