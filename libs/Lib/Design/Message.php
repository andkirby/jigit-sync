<?php
namespace Lib\Design;
use Lib\Session;

/**
 * Class Messages
 *
 * @package Lib\Design
 */
class Message extends Renderer
{
    /**
     * Set template
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->setTemplate('index/messages.phtml');
        parent::__construct($data);
    }

    /**
     * Clean up messages
     *
     * @return string
     */
    public function cleanUpMessages()
    {
        return $this->_getSession()->cleanUpMessages();
    }

    /**
     * Get messages
     *
     * @return array
     * @throws \Lib\Exception
     */
    public function getMessages()
    {
        return $this->_getSession()->getMessages();
    }

    /**
     * Get messages and clean up them in storage
     *
     * @return array
     */
    public function takeMessages()
    {
        return $this->_getSession()->takeMessages();
    }

    /**
     * Check having errors
     *
     * @return bool
     */
    public function hasErrors()
    {
        return $this->_getSession()->hasErrors();
    }

    /**
     * Get session
     *
     * @return Session
     */
    protected function _getSession()
    {
        return Session::getInstance();
    }
}
