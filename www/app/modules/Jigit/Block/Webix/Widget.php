<?php
namespace Jigit\Block\Webix;

use Lib\Design\Renderer;

/**
 * Class Widget
 *
 * @package Jigit\Block\Webix
 */
abstract class Widget extends Renderer implements WidgetInterface
{
    /**
     * Get config
     *
     * @return array
     * @throws \Lib\Exception
     */
    public function loadConfig()
    {
        $this->prepareConfig();
        return $this->getData();
    }
}
