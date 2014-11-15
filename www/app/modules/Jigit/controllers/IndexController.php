<?php
namespace Jigit;
use Lib\Controller;

/**
 * Class IndexController
 */
class IndexController extends Controller\AbstractController
{
    /**
     * Initialize
     */
    public function init()
    {
        //TODO Refactor this set
        !defined('JIGIT_ROOT') && define('JIGIT_ROOT', realpath(APP_ROOT . '/..'));

        $run = new Run();
        $run->initConfig();
        \Zend_Registry::set('vcs', new Git());
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_loadLayout();
        $this->_renderBlocks();
    }
}
