<?php
namespace App\Block;

use Lib\Design\Message;
use Lib\Design\Renderer;

/**
 * Class Json
 *
 * @package App\Block
 */
class Json extends Renderer
{
    /**
     * Get JSON string
     *
     * @return string
     */
    protected function _toHtml()
    {
        $data = array(
            'data'          => $this->getDataToJson(),
            'messages'      => $this->_getMessagesBlock()->getMessages(),
            'has_errors'    => $this->_getMessagesBlock()->hasErrors(),
            'messages_html' => $this->_getMessagesBlock()->toHtml(),
            'redirect'      => $this->_getRedirectUrl(),
        );
        return json_encode($data);
    }

    /**
     * Get redirect URL
     *
     * @return array|string
     */
    protected function _getRedirectUrl()
    {
        return $this->getData('redirect_url');
    }

    /**
     * Get data which need to be convert to JSON
     *
     * @return array|string
     */
    public function getDataToJson()
    {
        return $this->getData('json_data');
    }

    /**
     * Get messages block
     *
     * @return Message
     */
    protected function _getMessagesBlock()
    {
        return $this->getLayout()->getBlock('message');
    }
}
