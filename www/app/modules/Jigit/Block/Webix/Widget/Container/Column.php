<?php
namespace Jigit\Block\Webix\Widget\Container;

use Lib\Design\Renderer;

/**
 * Class Column
 *
 * @package Jigit\Block\WebixWidget
 */
class Column extends ContainerAbstract
{
    /**
     * Get items group type
     *
     * @return string
     */
    public function getGroupType()
    {
        return 'cols';
    }
}
