<?php
namespace Jigit\Block\Webix\Widget\Container;

use Jigit\Block\Webix;
use Lib\Design\Renderer;

/**
 * Class ContainerAbstract
 *
 * @package Jigit\Block\Webix
 * @method Webix\WidgetInterface getChild()
 */
abstract class ContainerAbstract extends Webix\Widget implements Webix\WidgetInterface
{
    /**
     * Prepare config
     *
     * @return $this
     */
    public function prepareConfig()
    {
        $this->loadChildrenConfig();
        return $this;
    }

    /**
     * Load child config
     *
     * @return $this
     */
    public function loadChildrenConfig()
    {
        $items = array();
        /** @var Webix\WidgetInterface $child */
        foreach ($this->getUiChildren() as $name => $child) {
            if ($child instanceof Webix\WidgetInterface) {
                $config  = $child->loadConfig();
                if ($config) {
                    $items[] = $config;
                }
            }
        }
        if ($items) {
            $this->setData($this->getGroupType(), $items);
        }
        return $this;
    }

    /**
     * Load child config
     *
     * @param string $name
     * @return Webix\WidgetInterface|null
     */
    public function loadChildConfig($name)
    {
        $block = $this->getChild($name);
        return $block ? $block->loadConfig() : null;
    }

    /**
     * Get UI children
     *
     * @return \Lib\Design\Renderer[]
     */
    public function getUiChildren()
    {
        return $this->getChildren();
    }

    /**
     * Get group type
     *
     * @return string
     */
    abstract public function getGroupType();
}
