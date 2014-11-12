<?php
namespace Lib\Design;

use Lib\Config\Node;
use Lib\Exception;
use Lib\Record;
use Symfony\Component\Yaml\Parser;

/**
 * Class Layout
 *
 * @package Lib\Design
 */
class Layout
{
    /**
     * Modules list
     *
     * @var array|null
     */
    protected $_modules;

    /**
     * Install config
     *
     * @var Record
     */
    protected $_config;

    /**
     * Layout config
     *
     * @var \Zend_Config
     */
    protected $_configLayout;

    /**
     * Blocks list
     *
     * @var Renderer[]
     */
    protected $_blocks = array();

    /**
     * Requested Blocks bconfig
     *
     * @var \Zend_Config
     */
    protected $_blocksConfig = array();

    /**
     * Layout package
     *
     * @var string
     */
    protected $_layoutPackage;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->_modules       = isset($options['modules']) ? $options['modules'] : null;
        $this->_config        = isset($options['config']) ? $options['config'] : null;
        $this->_layoutPackage = isset($options['package']) ? $options['package'] : 'frontend';
    }

    /**
     * Get modules list
     *
     * @return array
     */
    protected function _getModules()
    {
        return $this->_getConfig()->app->sortedModules->toArray();
    }

    /**
     * Load blocks
     *
     * @param string $handle
     * @return $this
     */
    public function loadBlocks($handle)
    {
        $this->_blocksConfig = $this->loadHandle($handle);
        $this->_performBlockConfig($this->_blocksConfig, null);
        return $this;
    }

    /**
     * Get block from layout blocks stack
     *
     * @param string $name
     * @return Renderer|null
     */
    public function getBlock($name)
    {
        return isset($this->_blocks[$name]) ? $this->_blocks[$name] : null;
    }

    /**
     * Load handle configuration
     *
     * @param string $handle
     * @return \Zend_Config
     * @throws Exception
     */
    public function loadHandle($handle)
    {
        @list($module, $controller) = explode('_', $handle);

        /** @var \Zend_Config $config */
        $config = $this->_getBaseConfigLayout();

        $this->_mergeConfig($config, 'x_x_x');
        $this->_mergeConfig($config, $module . '_x_x');
        $this->_mergeConfig($config, $module . '_' . $controller . '_x');
        $this->_mergeConfig($config, $handle);

        if (!empty($this->getConfigLayout()->$handle->_include)) {
            foreach ($this->getConfigLayout()->$handle->_include as $includeHandle) {
                $this->_mergeConfig($config, $includeHandle);
            }
        }

        return $config;
    }

    /**
     * Create block
     *
     * Config properties:
     * _class - Full block class name
     * _module - Module name. It should be set to find template place
     *           if class is not set or class in library
     * _data   - custom data from layout
     *
     * @param string|array|\Zend_Config|null $blockClass
     * @param string                         $name
     * @param \Zend_Config|array             $config
     * @return Renderer
     */
    public function createBlock($blockClass, $name, $config)
    {
        if (is_array($config)) {
            $config = $this->_getConfigNode($config, true);
        }

        $block = $this->_getBlockInstance($config, $blockClass);
        $this->_addBlockToStack($name, $block);
        return $block;
    }

    /**
     * Get config
     *
     * @return \Zend_Config
     * @throws \Zend_Exception
     */
    protected function _getConfig()
    {
        return \Zend_Registry::get('config');
    }

    /**
     * Get base modules directory
     *
     * @return string
     */
    protected function _getBaseModulesDir()
    {
        return rtrim($this->_getConfig()->app->modulesDir, '\\/');
    }

    /**
     * Get module dir
     *
     * @param string $moduleName
     * @return string
     */
    protected function _getModuleDir($moduleName)
    {
        return $this->_getBaseModulesDir() . DIRECTORY_SEPARATOR . $moduleName;
    }

    /**
     * Get module layout file
     *
     * @param string $moduleName
     * @return string
     */
    protected function _getModuleLayoutFile($moduleName)
    {
        $filename = $this->_getLayoutFilename();
        return $this->_getModuleDir($moduleName) . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * Get module layout config
     *
     * @param string $moduleName
     * @return \Zend_Config
     */
    protected function _getModuleLayoutConfig($moduleName)
    {
        $file = $this->_getModuleLayoutFile($moduleName);
        if (!is_file($file)) {
            return null;
        }
        return $this->_getConfigInstance($file);
    }

    /**
     * Get config instance
     *
     * @param string $file
     * @return \Zend_Config
     */
    protected function _getConfigInstance($file)
    {
        $yaml = new Parser();
        $config = (array) $yaml->parse(
            file_get_contents($file)
        );
        return $this->_getConfigNode($config);
    }

    /**
     * Get layout config
     *
     * @return \Zend_Config
     */
    public function getConfigLayout()
    {
        if (null === $this->_configLayout) {
            $this->_configLayout = $this->_getBaseConfigLayout();
            foreach ($this->_getModules() as $module) {
                $config = $this->_getModuleLayoutConfig($module);
                if ($config) {
                    $this->_configLayout->merge($config);
                }
            }
        }
        return $this->_configLayout;
    }

    /**
     * Get base layout config
     *
     * @return \Zend_Config
     */
    protected function _getBaseConfigLayout()
    {
        return $this->_getConfigNode(array(), true);
    }

    /**
     * Merge config
     *
     * @param \Zend_Config        $config        Destination config
     * @param \Zend_Config|string $includeConfig Target config
     *                                           It can be config or handle name
     * @return $this
     * @throws Exception
     */
    protected function _mergeConfig($config, $includeConfig)
    {
        if ($includeConfig instanceof \Zend_Config) {
            $mergeConfig = $includeConfig;
        } elseif (is_string($includeConfig)) {
            $mergeConfig = $this->getConfigLayout()->$includeConfig;
        } else {
            throw new Exception('Unknown include config type.');
        }
        if ($this->_isConfigValid($mergeConfig)) {
            $config->merge($mergeConfig);
            $this->_overwriteConfigBlocks($config, $mergeConfig);
        }
        return $this;
    }

    /**
     * Check system node
     *
     * @param string $name
     * @return bool
     */
    protected function _isSystemNode($name)
    {
        return 0 === strpos($name, '_');
    }

    /**
     * Perform block config
     *
     * @param \Zend_Config $config
     * @param Renderer     $parentBlock
     * @return $this
     */
    protected function _performBlockConfig($config, $parentBlock)
    {
        /** @var \Zend_Config $nodeConfig */
        foreach ($config as $name => $nodeConfig) {
            if ($this->_isSystemNode($name)) {
                continue;
            }
            if ($this->_isBlockRemoved($name)) {
                continue;
            }
            $block = $this->createBlock($nodeConfig->_class, $name, $nodeConfig);
            if ($parentBlock) {
                $parentBlock->setChild($name, $block);
            }
            $this->_mergeReference($name, $nodeConfig);
            $this->_callBlockMethod($block, $nodeConfig);

            $this->_performBlockConfig($nodeConfig, $block);
        }
        return $this;
    }

    /**
     * Merge reference instructions
     *
     * @param Renderer     $block
     * @param \Zend_Config $nodeConfig
     * @return $this
     * @throws Exception
     */
    protected function _callBlockMethod($block, $nodeConfig)
    {
        $actionsList = $nodeConfig->_action;
        if ($actionsList instanceof \Zend_Config) {
            $actionsList = $actionsList->toArray();
        }
        if (is_array($actionsList)) {
            foreach ($actionsList as $actions) {
                foreach ($actions as $method => $params) {
                    if (method_exists($block, $method)) {
                        call_user_func_array(array($block, $method), $params);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Merge reference instructions
     *
     * @param string $name
     * @param \Zend_Config $nodeConfig
     * @throws Exception
     */
    protected function _mergeReference($name, $nodeConfig)
    {
        if ($this->_blocksConfig->_reference && $this->_blocksConfig->_reference->$name) {
            $this->_mergeConfig($nodeConfig, $this->_blocksConfig->_reference->$name);
        }
    }

    /**
     * Check block removing
     *
     * @param string $name
     * @return bool
     */
    protected function _isBlockRemoved($name)
    {
        return $this->_blocksConfig->_remove && in_array($name, $this->_blocksConfig->_remove->toArray());
    }

    /**
     * Check is config valid
     *
     * @param \Zend_Config $mergeConfig
     * @return bool
     */
    protected function _isConfigValid($mergeConfig)
    {
        return $mergeConfig instanceof \Zend_Config;
    }

    /**
     * Overwrite config blocks
     *
     * @param \Zend_Config $config
     * @param \Zend_Config $sourceConfig
     */
    protected function _overwriteConfigBlocks($config, $sourceConfig)
    {
        foreach ($sourceConfig as $name => $item) {
            if ($this->_isSystemNode($name)) {
                continue;
            }
            $config->$name = $item;
        }
    }

    /**
     * Get default block class name
     *
     * @return string
     */
    protected function _getDefaultBlockClass()
    {
        return $this->_layoutPackage == 'frontend' ? '\Lib\Design\Renderer' : '\Lib\Design\Renderer\Admin';
    }

    /**
     * Get block class from config or return default one
     *
     * @param \Zend_Config $config
     * @return string
     */
    protected function _getBlockClass($config)
    {
        return (string)$config->_class ?: $this->_getDefaultBlockClass();
    }

    /**
     * Get block data from config
     *
     * @param \Zend_Config $config
     * @return array
     */
    protected function _getBlockData($config)
    {
        $data            = $config->_data ? $config->_data->toArray() : array();
        $data['_module'] = $config->_module; //todo refactor this into block property
        $data['_class']  = $config->_class; //todo refactor this into block property
        return $data;
    }

    /**
     * Set block template
     *
     * @param \Zend_Config $config
     * @param Renderer     $block
     * @return $this
     */
    protected function _setBlockTemplate(\Zend_Config $config, Renderer $block)
    {
        if ($config->_template) {
            $block->setTemplate($config->_template);
        }
        return $this;
    }

    /**
     * Get block instance
     *
     * @param \Zend_Config $config
     * @param string       $blockClass
     * @throws Exception
     * @return Renderer
     */
    protected function _getBlockInstance($config, $blockClass = null)
    {
        $data = $this->_getBlockData($config);

        if (!$blockClass) {
            $blockClass = $this->_getBlockClass($config);
        }

        /** @var Renderer $block */
        $block = new $blockClass($data);

        if (!($block instanceof Renderer)) {
            throw new Exception('Block must be an instance of \Lib\Design\Renderer class.');
        }

        $this->_setBlockTemplate($config, $block);

        return $block;
    }

    /**
     * Register block in layout blocks stack
     *
     * @param string   $name
     * @param Renderer $block
     * @return $this
     */
    protected function _addBlockToStack($name, Renderer $block)
    {
        $this->_blocks[$name] = $block;
        return $this;
    }

    /**
     * Get layout filename
     *
     * @return string
     */
    protected function _getLayoutFilename()
    {
        return $this->_layoutPackage . '.yml';
    }

    /**
     * Get config node
     *
     * @param array $data
     * @param bool  $allowModifications
     * @return Node
     */
    protected function _getConfigNode(array $data, $allowModifications = false)
    {
        return new Node($data, $allowModifications);
    }
}
