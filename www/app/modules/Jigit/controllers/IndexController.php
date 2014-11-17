<?php
namespace Jigit;
use Lib\Controller;

/**
 * Class IndexController
 */
class IndexController extends Controller\AbstractController
{
    /**
     * Runner
     *
     * @var Run
     */
    protected $_runner;

    /**
     * Initialize
     */
    public function init()
    {
        //TODO Refactor this set
        !defined('JIGIT_ROOT') && define('JIGIT_ROOT', realpath(APP_ROOT . '/..'));

        $run = $this->_getRunner();
        if ($this->getRequest()->isPost()) {
            $run->initConfig();
        } else {
            $run->initialize($this->getRequest()->getPostParam('action'), $this->getRequest()->getPost());
        }
        \Zend_Registry::set('vcs', $run->getVcs());
        \Zend_Registry::set('runner', $run);
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_loadLayout();
        $this->_renderBlocks();
    }

    /**
     * Post action
     */
    public function postAction()
    {
        $this->_loadLayout();
        $this->_renderBlocks();
    }

    /**
     * Get runner
     *
     * @return Run
     */
    protected function _getRunner()
    {
        if (null === $this->_runner) {
            $this->_runner = new Run();
        }
        return $this->_runner;
    }
}
