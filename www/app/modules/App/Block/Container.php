<?php
namespace App\Block;

use Lib\Design\Renderer;

/**
 * Class Container
 *
 * @package App\Block
 */
class Container extends Renderer
{
    /**
     * Get HTML of children
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->getChildHtml();
    }
}
