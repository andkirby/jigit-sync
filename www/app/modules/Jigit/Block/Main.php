<?php
namespace Jigit\Block;

use Jigit\Block\Webix\Widget\Container\Layout;

/**
 * Class Main
 *
 * @package Jigit\Block
 */
class Main extends Layout
{
    /**
     * Prepare config
     *
     * @return $this
     */
    public function prepareConfig()
    {
        $config = array(
            'view'   => 'layout',
            'id'     => 'main',
            'height' => 400,
        );
        $this->addData($config);
        parent::prepareConfig();
        return $this;
    }
}
