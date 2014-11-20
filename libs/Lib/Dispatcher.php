<?php
namespace Lib;

use App\ErrorController;
use Lib\Controller\AbstractController;
use Lib\Db\Install;
use Lib\Db\Pdo\Mysql;

/**
 * Class Dispatcher
 *
 * @package Lib
 */
class Dispatcher
{
    /**
     * Flag of initializing DB on run application
     *
     * @var bool
     */
    protected $_initDb = true;

    /**
     * Get status of initializing database
     *
     * @return boolean
     */
    public function isInitDb()
    {
        return $this->_initDb;
    }

    /**
     * Set status of initializing database
     *
     * @param boolean $initDb
     * @return $this
     */
    public function setInitDb($initDb)
    {
        $this->_initDb = $initDb;
        return $this;
    }

    /**
     * Run application
     */
    public function run()
    {
        try {
            $this->_defineRoot();
            $this->_initConfig();

            if ($this->isInitDb() && !$this->_isInstalled()) {
                $this->_redirectToInstall();
            }

            $this->_initResponse();

            $this->_startSession();
            $this->_setSessionPath($this->_getDefaultSessionPath());

            if ($this->isInitDb()) {
                $this->_initDb();
                $this->_install();
            }

            $controllerName = $this->getRequest()->getControllerName();
            $actionName     = $this->getRequest()->getActionName();
            $moduleName     = $this->getRequest()->getModuleName();

            $controller = $this->_getController($controllerName, $moduleName);

            $controller->setActionName($actionName);
            $controller->setControllerName($controllerName);

            $this->_startCatchingOutput();
            $this->_callControllerRequest($controller);
            $output = $this->_finishCatchingOutput();
            echo $output;
        } catch (\Exception $mainException) {
            try {
                //show error
                /** @var ErrorController $controller */
                $controller = $this->_getController('error', 'app');
                $controller->setActionName('error');
                $controller->setControllerName('error');
                $controller->setException($mainException);
                $this->_startCatchingOutput();
                $this->_callControllerRequest($controller);
                $output = $this->_finishCatchingOutput();
                echo $output;
            } catch (\Exception $e) {
                echo $mainException;
                echo PHP_EOL;
                echo $e;
            }
        }
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
     * Define root dir
     *
     * @return $this
     */
    protected function _defineRoot()
    {
        !defined('APP_ROOT') && define('APP_ROOT', realpath(__DIR__ . '/../..'));
        return $this;
    }

    /**
     * Get controller
     *
     * @param string $controllerName
     * @param string $moduleName
     * @return AbstractController
     */
    protected function _getController($controllerName, $moduleName)
    {
        $moduleName = $this->_upperName($moduleName);
        $controllerName = $this->_upperName($controllerName);
        $controllerClassName = $controllerName . 'Controller';

        if ($this->getRequest()->isAdmin()) {
            $controllerClassName = 'Admin\\' . $controllerClassName;
        }

        $this->_loadControllerFile($controllerClassName, $moduleName);

        /** @var AbstractController $controller */
        $controllerFullClassName = $moduleName . '\\' . $controllerClassName;
        return new $controllerFullClassName;
    }

    /**
     * Load controller file
     *
     * @param string $controllerClassName
     * @param string $moduleName
     * @return $this
     */
    protected function _loadControllerFile($controllerClassName, $moduleName)
    {
        include_once $this->_getConfig()->app->modulesDir . DIRECTORY_SEPARATOR
            . str_replace('\\', DIRECTORY_SEPARATOR, $moduleName)
            . '/controllers/'
            . str_replace('\\', DIRECTORY_SEPARATOR, $controllerClassName)
            . '.php';
        return $this;
    }

    /**
     * Call request to controller
     *
     * @param AbstractController $controller
     * @return $this
     * @throws Exception
     */
    protected function _callControllerRequest(AbstractController $controller)
    {
        $controller->init();
        $controller->preDispatch();
        $controller->callAction();
        $controller->postDispatch();
        return $this;
    }

    /**
     * Start catching output
     *
     * @return $this
     */
    protected function _startCatchingOutput()
    {
        ob_start();
        return $this;
    }

    /**
     * Finish catching output
     *
     * @return string
     */
    protected function _finishCatchingOutput()
    {
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    /**
     * Start session
     *
     * @return $this
     */
    protected function _startSession()
    {
        Session::initialize();
        return $this;
    }

    /**
     * Set session path
     *
     * @param string $path
     * @return $this
     */
    protected function _setSessionPath($path)
    {
        Session::setSessionPath($path);
        return $this;
    }

    /**
     * Get default session path
     *
     * @return string
     */
    protected function _getDefaultSessionPath()
    {
        return APP_ROOT . '/var/session';
    }

    /**
     * Init database
     */
    protected function _initDb()
    {
        $config = $this->_getConfig();
        $db = new Mysql($config->database->params);
        \Zend_Db_Table::setDefaultAdapter($db);
        return $this;
    }

    /**
     * Convert to class name part
     *
     * @param string $name
     * @return array
     */
    protected function _upperName($name)
    {
        $arr = explode('-', $name);
        foreach ($arr as &$item) {
            $item = ucfirst($item);
        }
        return implode('\\', $arr);
    }

    /**
     * Method to check application installation status
     *
     * @return bool
     */
    protected function _isInstalled()
    {
        return is_file(APP_ROOT . '/app/etc/local.ini');
    }

    /**
     * Install modules
     *
     * @return $this
     */
    protected function _install()
    {
        $install = new Install(
            \Zend_Db_Table::getDefaultAdapter(),
            $this->_getConfig()->app->sortedModules,
            $this->_getVersion()
        );
        $install->install();
        return $this;
    }

    /**
     * Init config
     *
     * @return \Zend_Config_Ini
     */
    protected function _initConfig()
    {
        $config = new Config();
        $host = $this->getRequest()->getServer('HTTP_HOST');
        //try to load config by hostname
        try {
            $appConfig = $config->getApplicationConfig(APP_ROOT, $host, array(), $this->isInitDb());
        } catch (\Zend_Config_Exception $e) {
            $appConfig = $config->getApplicationConfig(APP_ROOT, 'development', array(), $this->isInitDb());
        }
        \Zend_Registry::set('config', $appConfig);
        return $this;
    }

    /**
     * Get application config
     *
     * @return \Zend_Config
     * @throws \Zend_Exception
     */
    protected function _getConfig()
    {
        return \Zend_Registry::get('config');
    }

    /**
     * Get version model
     *
     * @return Install\VersionInterface
     */
    protected function _getVersion()
    {
        $class = $this->_getConfig()->app->versionClass;
        return new $class;
    }

    /**
     * Redirect to install page
     */
    protected function _redirectToInstall()
    {
        //todo add checking domain directory
        header('Location: /install.php', true);
        exit;
    }

    /**
     * Init response
     *
     * @throws \Zend_Controller_Exception
     */
    protected function _initResponse()
    {
        \Zend_Controller_Front::getInstance()->setResponse(new \Zend_Controller_Response_Http());
    }
}
