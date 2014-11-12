<?php
namespace App\Admin;
use App\Controller\Admin\AbstractController;

/**
 * Class ErrorController
 */
class ErrorController extends AbstractController
{
    /**
     * Caught Exception
     *
     * @var \Exception
     */
    protected $_exception;

    /**
     * Index action
     */
    public function errorAction()
    {
        echo '<pre>';
        echo $this->_exception;
        echo '</pre>';
    }

    /**
     * Set exception
     *
     * @param \Exception $e
     * @return $this
     */
    public function setException(\Exception $e)
    {
        $this->_exception = $e;
        return $this;
    }
}
