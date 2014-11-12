<?php
namespace Lib;

use Lib\Model\Data;

/**
 * Class Session
 *
 * @package Lib
 */
class Session extends Data
{
    /**
     * Data key of messages
     */
    const KEY_MESSAGES = '__messages';

    /**
     * Instance
     *
     * @var Session
     */
    static protected $_instance;

    /**
     * Data namespace
     *
     * @var string
     */
    protected $_namespace;

    /**
     * Constructor
     */
    public function __construct()
    {
        self::start();
        parent::__construct();
    }

    /**
     * Check active session
     *
     * @return bool
     */
    public static function isActive()
    {
        return (bool)session_id();
    }

    /**
     * Start session
     */
    public static function start()
    {
        session_start();
    }

    /**
     * Set session path
     *
     * @param string|null $path
     * @return string
     */
    public static function setSessionPath($path = null)
    {
        ini_set('session.save_path', $path);
//        session_save_path($path);
    }

    /**
     * Init resource
     *
     * @return void
     */
    public function init()
    {
        //@startSkipCommitHooks
        $this->_resource = &$_SESSION;
        //@finishSkipCommitHooks
    }

    /**
     * Get instance
     *
     * @param string $namespace
     * @return Session
     */
    public static function getInstance($namespace = 'default')
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        self::$_instance->setNamespace($namespace);
        return self::$_instance;
    }

    /**
     * Initialize session
     *
     * Start via crating session instance
     *
     * @return bool
     */
    public static function initialize()
    {
        self::getInstance();
        return self::isActive();
    }

    /**
     * Set data namespace
     *
     * @param string $namespace
     * @return $this
     */
    public function setNamespace($namespace)
    {
        $this->_namespace = (string)$namespace;
        if (!isset($this->_resource[$this->_namespace])) {
            $this->_resource[$this->_namespace] = array();
        }
        return $this;
    }

    /**
     * Set data
     *
     * @param string $key
     * @param array|string|int|null   $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->_resource[$this->_namespace] = $key;
        } else {
            $this->_resource[$this->_namespace][$key] = $value;
        }
        return $this;
    }

    /**
     * Get data
     *
     * @param string $key
     * @return array|string|null
     */
    public function getData($key = null)
    {
        if (null === $key) {
            if (isset($this->_resource[$this->_namespace])) {
                return $this->_resource[$this->_namespace];
            }
            return array();
        } elseif (isset($this->_resource[$this->_namespace][$key])) {
            return $this->_resource[$this->_namespace][$key];
        }
        return null;
    }

    /**
     * Add error message
     *
     * @param string $message
     * @return $this
     */
    public function addError($message)
    {
        $messages = $this->getData(self::KEY_MESSAGES);
        $messages[] = array('type' => 'error', 'message' => (string)$message);
        $this->setData(self::KEY_MESSAGES, $messages);
        return $this;
    }

    /**
     * Add success message
     *
     * @param string $message
     * @return $this
     */
    public function addSuccess($message)
    {
        $messages = $this->getData(self::KEY_MESSAGES);
        $messages[] = array('type' => 'success', 'message' => $message);
        $this->setData(self::KEY_MESSAGES, $messages);
        return $this;
    }

    /**
     * Get messages
     *
     * @return array
     */
    public function getMessages()
    {
        return (array)$this->getData(self::KEY_MESSAGES);
    }

    /**
     * Clean up messages
     *
     * @return $this
     */
    public function cleanUpMessages()
    {
        $this->setData(self::KEY_MESSAGES, array());
        return $this;
    }
}
