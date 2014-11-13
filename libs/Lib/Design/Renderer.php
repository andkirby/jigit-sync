<?php
namespace Lib\Design;
use Lib\Exception;
use Lib\Model\Data;
use Lib\Request;
use Lib\Url;

/**
 * Class Renderer
 *
 * @package Lib\Design
 */
class Renderer extends Data\Object
{
    /**
     * Child blocks
     *
     * @var Renderer[]
     */
    protected $_children = array();

    /**
     * Parent block
     *
     * @var Renderer
     */
    protected $_parent;

    /**
     * Template filename
     *
     * @var string
     */
    protected $_template;

    /**
     * Layout
     *
     * @var Layout
     */
    protected $_layout;

    /**
     * Constructor. Set data
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->setData($data);
    }

    /**
     * Set template
     *
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {
        if (false === strpos($template, '.phtml')) {
            $template .= '.phtml';
        }
        $this->_template = $template;
        return $this;
    }

    /**
     * Get template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * Method which call before template loading
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        return $this;
    }

    /**
     * Method which call after template loading
     *
     * @return $this
     */
    protected function _afterToHtml()
    {
        return $this;
    }

    /**
     * Render template
     *
     * @return string
     * @throws \Exception
     */
    final public function toHtml()
    {
        $this->_beforeToHtml();
        $html = $this->_toHtml();
        $this->_afterToHtml();
        return $html;
    }

    /**
     * Load template
     *
     * @return string
     * @throws \Exception
     */
    protected function _toHtml()
    {
        if (!is_file($this->getTemplateFile())) {
            if ($this->_isQuietRender()) {
                return '';
            } else {
                throw new Exception(sprintf('File %s not found.', $this->_template));
            }
        }
        ob_start();
        $this->_loadTemplate();
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    /**
     * Get URL
     *
     * @param string $path
     * @param array  $params
     * @param bool   $reset
     * @return string
     */
    public function getUrl($path, array $params = array(), $reset = true)
    {
        $url = new Url();
        return $url->getUrl($path, $params, $reset);
    }

    /**
     * Get URL
     *
     * @param string $uri
     * @return string
     */
    public function getLinkUrl($uri)
    {
        $url = new Url();
        return $url->getLinkUrl($uri);
    }

    /**
     * Set child block
     *
     * @param string   $name
     * @param Renderer $child
     * @return $this
     */
    public function setChild($name, $child)
    {
        if ($child->getParent()) {
            $child->getParent()->unsetChild($name);
        }
        $child->setParent($this);
        $this->_children[$name] = $child;
        return $this;
    }

    /**
     * Unset child block
     *
     * @param string $name
     * @return $this
     */
    public function unsetChild($name)
    {
        unset($this->_children[$name]);
        return $this;
    }

    /**
     * Set parent block
     *
     * @param Renderer $block
     * @return $this
     */
    public function setParent(Renderer $block)
    {
        $this->_parent = $block;
        return $this;
    }

    /**
     * Get parent block
     *
     * @return Renderer
     */
    public function getParent()
    {
        return $this->_parent;
    }

    /**
     * Get child HTML
     *
     * @param string|null $name
     * @return string
     */
    public function getChildHtml($name = null)
    {
        if ($name) {
            $block    = $this->getChild($name);
            $children = $block ? array($block) : array();
        } else {
            $children = $this->_children;
        }
        $html = '';
        foreach ($children as $child) {
            $html .= $child->toHtml();
        }
        return $html;
    }

    /**
     * Get child block
     *
     * @param string $name
     * @return Renderer|null
     */
    public function getChild($name)
    {
        if (isset($this->_children[$name])) {
            return $this->_children[$name];
        }
        return null;
    }

    /**
     * Get children blocks
     *
     * @return Renderer[]
     */
    public function getChildren()
    {
        return $this->_children;
    }

    /**
     * Get general template directory
     *
     * @return string
     */
    public function getGeneralTemplateDir()
    {
        return APP_ROOT . DIRECTORY_SEPARATOR . 'design' . DIRECTORY_SEPARATOR
            . $this->_getPackage() . DIRECTORY_SEPARATOR . $this->_getBlockModuleNamespace();
    }

    /**
     * Get package
     *
     * @todo It should be refactored because almost the same logic for layouts.
     * @see AbstractController::_getPackage()
     * @return string
     */
    protected function _getPackage()
    {
        return 'frontend';
    }

    /**
     * Get template directory
     *
     * @return string
     */
    public function getModuleTemplateDir()
    {
        return $this->_getBaseModulesDir() . DIRECTORY_SEPARATOR
            . $this->_getBlockModuleNamespace() . DIRECTORY_SEPARATOR . 'template'
            . DIRECTORY_SEPARATOR . $this->_getPackage();
    }

    /**
     * Get full path to template file
     *
     * @return string
     */
    public function getTemplateFile()
    {
        $template = $this->getGeneralTemplateDir() . DIRECTORY_SEPARATOR . $this->_template;
        if (!is_file($template)) {
            $template = $this->getModuleTemplateDir() . DIRECTORY_SEPARATOR . $this->_template;
        }

        return $template;
    }

    /**
     * Load template
     *
     * @return $this
     */
    protected function _loadTemplate()
    {
        include $this->getTemplateFile();
        return $this;
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
     * Get module namespace
     *
     * @return mixed
     */
    protected function _getBlockModuleNamespace()
    {
        if ($this->getData('_module')) {
            $module = $this->getData('_module');
        } else {
            @list($module) = explode('\\Block\\', get_class($this));
        }
        return $module;
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
     * Set layout
     *
     * @param Layout $layout
     * @return $this
     */
    public function setLayout(Layout $layout)
    {
        $this->_layout = $layout;
        return $this;
    }

    /**
     * Get layout
     *
     * @return Layout
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * Get status of quiet renderer
     *
     * @return bool
     */
    protected function _isQuietRender()
    {
        return !$this->_getConfig()->app->debug;
    }

    /**
     * Get request
     *
     * @return Request
     */
    public function getRequest()
    {
        return Request::getInstance();
    }
}
