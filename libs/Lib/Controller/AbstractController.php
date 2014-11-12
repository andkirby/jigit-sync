<?php
namespace Lib\Controller;

use Lib\Design\Layout;
use Lib\Exception;
use Lib\Design\Renderer;
use Lib\Design\Message;
use Lib\Request;
use Lib\Session;
use Lib\Url;

/**
 * Class AbstractController
 *
 * @package Lib\Controller
 */
abstract class AbstractController
{
    /**
     * Is AJAX status
     *
     * @var bool
     */
    protected $_isAjax = false;

    /**
     * Action name
     *
     * @var string
     */
    protected $_actionName;

    /**
     * Controller name
     *
     * @var string
     */
    protected $_controllerName;

    /**
     * Module name
     *
     * @var string
     */
    protected $_moduleName = 'app';

    /**
     * Layout
     *
     * @var Layout
     */
    protected $_layout;

    /**
     * Set status to render wrapper
     *
     * @param boolean $flag
     * @return $this
     */
    public function setRenderWrapper($flag)
    {
        $this->_renderWrapper = $flag;
        return $this;
    }

    /**
     * Get action name
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->_actionName;
    }

    /**
     * Set action name
     *
     * @param string $actionName
     * @return $this
     */
    public function setActionName($actionName)
    {
        $this->_actionName = $actionName;
        return $this;
    }

    /**
     * Get method action name
     *
     * @return string
     */
    public function getMethodActionName()
    {
        return $this->getActionName() . 'Action';
    }

    /**
     * Get controller name
     *
     * @return string
     */
    public function getControllerName()
    {
        return $this->_controllerName;
    }

    /**
     * Set controller name
     *
     * @param string $controllerName
     * @return $this
     */
    public function setControllerName($controllerName)
    {
        $this->_controllerName = $controllerName;
        return $this;
    }

    /**
     * Get module name
     *
     * @return string
     */
    public function getModuleName()
    {
        return $this->_moduleName;
    }

    /**
     * Set module name
     *
     * @param string $moduleName
     * @return $this
     */
    public function setModuleName($moduleName)
    {
        $this->_moduleName = $moduleName;
        return $this;
    }

    /**
     * Pre-dispatch method
     */
    public function preDispatch()
    {
    }

    /**
     * Post-dispatch method
     */
    public function postDispatch()
    {
    }

    /**
     * Initialize
     */
    public function init()
    {
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

    /**
     * Call action in controller
     *
     * @return $this
     * @throws Exception
     */
    public function callAction()
    {
        $methodActionName = $this->getMethodActionName();
        if (!method_exists($this, $methodActionName)) {
            $controllerClassName = get_class($this);
            throw new Exception("Action '$methodActionName' not found in class '$controllerClassName'.");
        }
        $this->$methodActionName();
        return $this;
    }

    /**
     * Render request
     *
     * @param array       $data
     * @param string|null $template
     * @param string|null $blockClass
     * @return $this
     */
    protected function _render(array $data = array(), $template = null, $blockClass = null)
    {
        //create block
        if ($blockClass) {
            list($module) = explode('\\', get_class($this));
            $blockClass = '\\' . $module . '\\Block\\' . $blockClass;
            $block = new $blockClass($data);
        } else {
            $block = new Renderer($data);
        }

        if (!$template) {
            //set template
            $template = $this->getControllerName() . DIRECTORY_SEPARATOR
                . $this->getActionName() . '.phtml';
        }
        $block->setTemplate($template);

        //get action HTML
        $actionHtml = $block->toHtml();

        if (!$this->isAjax()) {
            $block = new Renderer(array('content' => $actionHtml));
            $block->setTemplate('index.phtml');

            //add system messages block
            $message = new Message();
            $message->setTemplate('index/messages.phtml');
            $block->setChild('message', $message);

            echo $block->toHtml();
        } else {
            echo $actionHtml;
        }
        return $this;
    }

    /**
     * Get layout
     *
     * @return Layout
     */
    protected function _getLayout()
    {
        if (null === $this->_layout) {
            $this->_layout = new Layout(array('package' => $this->_getPackage()));
        }
        return $this->_layout;
    }

    /**
     * Get package
     *
     * @return string
     */
    protected function _getPackage()
    {
        return $this->getRequest()->isAdmin() ? 'admin' : 'frontend';
    }

    /**
     * Get request handle name
     *
     * @return string
     */
    protected function _getRequestedHandleName()
    {
        return $this->getRequest()->getModuleName() . '_'
            . $this->getRequest()->getControllerName() . '_'
            . $this->getRequest()->getActionName();
    }

    /**
     * Load layout
     *
     * @return $this
     */
    protected function _loadLayout()
    {
        $this->_getLayout()->loadBlocks(
            $this->_getRequestedHandleName()
        );
        return $this;
    }

    /**
     * Render blocks
     *
     * @return $this
     */
    protected function _renderBlocks()
    {
        $root = $this->_getLayout()->getBlock('root');
        if ($root) {
            echo $root->toHtml();
        }
        return $this;
    }

    /**
     * Redirect by URL parameters
     *
     * @param string $path
     * @param array  $params
     * @param bool   $reset
     * @return string
     */
    protected function _redirect($path, array $params = array(), $reset = true)
    {
        header('Location: ' . $this->_getUrl($path, $params, $reset));
        exit;
    }

    /**
     * Get URL
     *
     * @param string $path
     * @param array  $params
     * @param bool   $reset
     * @return string
     */
    protected function _getUrl($path, array $params = array(), $reset = true)
    {
        $url = new Url();
        return $url->getUrl($path, $params, $reset);
    }

    /**
     * Get session
     *
     * @param string $namespace
     * @return Session
     */
    protected function _getSession($namespace = 'default')
    {
        return Session::getInstance($namespace);
    }

    /**
     * Check ajax (or console request)
     *
     * @return bool
     */
    public function isAjax()
    {
        if (null === $this->_isAjax) {
            if ($this->getRequest()->getGetParam('isAjax') == 'false') {
                $this->_isAjax = false;
            } else {
                $this->_isAjax = strtolower($this->getRequest()->getServer('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest'
                    || $this->getRequest()->getGetParam('isAjax') == 'true'
                    || $this->getRequest()->getGetParam('isAjax') == '1';
            }
        }
        return $this->_isAjax;
    }
}
