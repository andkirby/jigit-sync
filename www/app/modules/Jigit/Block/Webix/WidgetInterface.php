<?php
namespace Jigit\Block\Webix;

use Lib\Design\Renderer;

/**
 * Interface WidgetInterface
 *
 * @package Jigit\Block\Webix
 */
interface WidgetInterface
{
    /**
     * Prepare config
     *
     * @return $this
     */
    public function prepareConfig();

    /**
     * Get config
     *
     * @return $this
     */
    public function loadConfig();
}
