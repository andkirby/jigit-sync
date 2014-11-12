<?php
namespace Lib;

/**
 * Class Url
 *
 * @package Lib
 */
class Url
{
    /**
     * Request params to generate URL
     *
     * @var array
     */
    protected $_params = array();

    /**
     * Request
     *
     * @var Request
     */
    protected $_request;

    /**
     * Secure mode status
     *
     * @var bool
     */
    protected $_secure = false;

    /**
     * Set options
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->_request = isset($options['request'])
            ? $options['request'] : Request::getInstance();
    }

    /**
     * Get URI
     *
     * @param string $path
     * @param array  $params
     * @param bool   $reset     Reset exists parameters. If TRUE they will set again.
     * @return string
     */
    public function getUrl($path, $params = array(), $reset = true)
    {
        @list($module, $controller, $action) = explode('/', $path);

        //perform module
        if ($module == '*') {
            $module = $this->_getRequest()->getModuleName();
        } elseif (!$module) {
            $module = $this->getDefaultModuleName();
        }
        $params[$this->getModuleKey()] = $module;

        //perform controller
        if ($controller == '*') {
            $controller = $this->_getRequest()->getControllerName();
        } elseif (!$controller) {
            $controller = $this->getDefaultControllerName();
        }
        $params[$this->getControllerKey()] = $controller;

        //perform action
        if ($action == '*') {
            $action = $this->_getRequest()->getActionName();
        } elseif (!$action) {
            $action = $this->getDefaultActionName();
        }
        $params[$this->getActionKey()] = $action;

        //merge exists params
        if (!$reset) {
            $requestedGet = $this->_getRequest()->getGet();
            $params = array_merge($requestedGet, $params);
        }

        //clean default params
        if ($params[$this->getActionKey()] == $this->getDefaultActionName()) {
            unset($params[$this->getActionKey()]);
        }
        if ($params[$this->getControllerKey()] == $this->getDefaultControllerName()) {
            unset($params[$this->getControllerKey()]);
        }
        if ($params[$this->getModuleKey()] == $this->getDefaultModuleName()) {
            unset($params[$this->getModuleKey()]);
        }

        return $this->getBaseUrl() . ltrim($this->_getQuery($params), '/');
    }

    /**
     * Get request
     *
     * @return Request
     */
    protected function _getRequest()
    {
        return $this->_request;
    }

    /**
     * Get query string
     *
     * @param array $params
     * @return string
     */
    protected function _getQuery(array $params)
    {
        $url = $this->_getBaseUrl($params);

        unset($params['_force_admin']);
        unset($params['_force_frontend']);

        if ($params) {
            $url .= '?' . http_build_query($params);
        }
        return $url;
    }

    /**
     * Get base URL
     *
     * @return string
     */
    protected function _getBaseUrl(array $params)
    {
        $isAdmin = null;
        if (!empty($params['_force_admin'])) {
            $isAdmin = true;
        } elseif (!empty($params['_force_frontend'])) {
            $isAdmin = false;
        } else {
            $isAdmin = $this->_getRequest()->isAdmin();
        }
        return $isAdmin ? '/admin/' : '/';
    }

    /**
     * Get default module name
     *
     * @return string
     */
    public function getDefaultModuleName()
    {
        return 'app';
    }

    /**
     * Get default module name
     *
     * @return string
     */
    public function getDefaultControllerName()
    {
        return 'index';
    }

    /**
     * Get default module name
     *
     * @return string
     */
    public function getDefaultActionName()
    {
        return 'index';
    }

    /**
     * Get module key
     *
     * @return string
     */
    public function getModuleKey()
    {
        return 'm';
    }

    /**
     * Get controller key
     *
     * @return string
     */
    public function getControllerKey()
    {
        return 'c';
    }

    /**
     * Get action key
     *
     * @return string
     */
    public function getActionKey()
    {
        return 'a';
    }

    /**
     * Get base URL
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_getProtocol() . '://' . rtrim($this->_getConfig()->webhost, '/') . '/';
    }

    /**
     * Get base link URL
     *
     * Get URLs for skin files (CSS, JS, images, etc)
     *
     * @return string
     */
    public function getBaseLinkUrl()
    {
        return $this->_getProtocol() . '://' . rtrim($this->_getConfig()->webhostLink, '/') . '/';
    }

    /**
     * Get base link URL
     *
     * Get URLs for skin files (CSS, JS, images, etc)
     *
     * @param string $uri
     * @return string
     */
    public function getLinkUrl($uri)
    {
        return $this->getBaseLinkUrl() . ltrim($uri, '/');
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
     * Get URL protocol
     *
     * @return string
     */
    protected function _getProtocol()
    {
        return $this->_secure ? 'https' : 'http';
    }
}
