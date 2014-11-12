<?php
namespace Lib;

/**
 * Class Request
 */
class Request
{
    /**
     * Instance of Request
     *
     * @var Request
     */
    static protected $_instance;

    /**
     * Get instance
     *
     * @return Request
     */
    static public function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * Get GET parameter
     *
     * @param string $name
     * @param string|array $default
     * @return string|array
     */
    public function getGetParam($name, $default = null)
    {
        $get = $this->getGet();
        return isset($get[$name]) ? $get[$name] : $default;
    }

    /**
     * Get POST parameter
     *
     * @param string $name
     * @param string|array $default
     * @return string|array
     */
    public function getPostParam($name, $default = null)
    {
        $post = $this->getPost();
        return isset($post[$name]) ? $post[$name] : $default;
    }

    /**
     * Get post parameters
     *
     * @return array
     */
    public function getPost()
    {
        //@startSkipCommitHooks
        return $_POST;
        //@finishSkipCommitHooks
    }

    /**
     * Get post parameters
     *
     * @return array
     */
    public function getGet()
    {
        //@startSkipCommitHooks
        return $_GET;
        //@finishSkipCommitHooks
    }

    /**
     * Check request method is POST
     *
     * @return bool
     */
    public function isPost()
    {
        return $this->getMethod() == 'post';
    }

    /**
     * Get controller name
     *
     * @return string
     */
    public function getControllerName()
    {
        return $this->getGetParam('c', 'index');
    }

    /**
     * Get controller name
     *
     * @return string
     */
    public function getModuleName()
    {
        return $this->getGetParam('m', 'app');
    }

    /**
     * Get action name
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->getGetParam('a', 'index');
    }

    /**
     * Check admin panel request
     *
     * @return bool
     */
    public function isAdmin()
    {
        //@startSkipCommitHooks
        return 0 === strpos(strtolower($this->getServer('REQUEST_URI')), '/admin/')
            || '/admin' === $this->getServer('REQUEST_URI');
        //@finishSkipCommitHooks
    }

    /**
     * Get server value
     *
     * @param string $key
     * @return string|array null
     */
    public function getServer($key)
    {
        //@startSkipCommitHooks
        return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
        //@finishSkipCommitHooks
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod()
    {
        return strtolower($this->getServer('REQUEST_METHOD'));
    }
}

