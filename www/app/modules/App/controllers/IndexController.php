<?php
namespace App;
use Lib\Controller;

/**
 * Class IndexController
 */
class IndexController extends Controller\AbstractController
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_loadLayout();
        $this->_renderBlocks();
    }
}
