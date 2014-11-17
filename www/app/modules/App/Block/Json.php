<?php
namespace App\Block;

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
        return json_encode($this->_getDataToJson());
    }

    /**
     * Get data which need to be convert to JSON
     *
     * @return array|string
     */
    protected function _getDataToJson()
    {
        return $this->getData('json_data');
    }
}
