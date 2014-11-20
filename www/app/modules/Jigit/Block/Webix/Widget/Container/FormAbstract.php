<?php
namespace Jigit\Block\Webix\Widget\Container;

use Lib\Design\Renderer;

/**
 * Class FormAbstract
 *
 * @package Jigit\Block\Webix
 */
abstract class FormAbstract extends Row
{
    /**
     * Group type
     */
    const GROUP_TYPE = 'elements';

    /**
     * Form elements
     *
     * @var array
     */
    protected $_elements = array();

    /**
     * Prepare config
     *
     * @return $this
     */
    public function prepareConfig()
    {
        $config = array(
            'view' => 'form',
            'id'   => $this->getId(),
        );
        $this->addData($config);
        $this->_prepareElements();
        parent::prepareConfig();
        return $this;
    }

    /**
     * Prepare elements
     *
     * @return $this
     */
    abstract protected function _prepareElements();

    /**
     * Add elements config
     *
     * @return $this
     */
    public function loadChildrenConfig()
    {
        $this->setData($this->getGroupType(), $this->_getElements(true));
        return $this;
    }

    /**
     * Get form ID
     *
     * @return string
     */
    public function getId()
    {
        return 'form_' . str_replace('\\', '_', get_class($this));
    }

    /**
     * Add element
     *
     * @param string $name
     * @param string $type
     * @param array  $config
     * @param bool   $valueFromRequest
     * @return $this
     */
    protected function _addElement($name, $type, $config, $valueFromRequest = true)
    {
        if (!isset($config['view'])) {
            $config['view'] = $type;
        }
        if (!isset($config['id'])) {
            $config['id'] = $name;
        }
        if (!isset($config['name'])) {
            $config['name'] = $name;
        }
        if ($valueFromRequest && !isset($config['value']) && $this->getRequest()->getGetParam($name)) {
            $config['value'] = $this->getRequest()->getGetParam($name);
        }
        if (empty($config['value'])) {
            unset($config['value']);
        }
        $this->_elements[$name] = $config;
        return $this;
    }

    /**
     * Get items group type
     *
     * @return string
     */
    public function getGroupType()
    {
        return self::GROUP_TYPE;
    }

    /**
     * Get element
     *
     * @param string $name
     * @return array|null
     */
    protected function _getElement($name)
    {
        return isset($this->_elements[$name]) ? $this->_elements[$name] : null;
    }

    /**
     * Get elements
     *
     * @param bool $nonAssoc    If TRUE return array without key-names, ie non-assoc array
     * @return array|null
     */
    protected function _getElements($nonAssoc = false)
    {
        return $nonAssoc ? array_values($this->_elements) : $this->_elements;
    }
}
