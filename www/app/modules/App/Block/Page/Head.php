<?php
namespace App\Block\Page;

use Jigit\Exception;
use Lib\Design\Renderer;

/**
 * Class Head
 *
 * This block responsible of content within head HTML tag
 *
 * @package App\Block\Page
 */
class Head extends Renderer
{
    /**
     * Links list
     *
     * @var array
     */
    protected $_links = array(
        'link'   => array(),
        'script' => array(),
    );

    /**
     * Add link
     *
     * @param string $type
     * @param string $uri
     * @param array  $options
     * @return $this
     */
    public function addLink($type, $uri, $options = array())
    {
        if (strpos($uri, '://')) {
            $url = $uri;
        } else {
            $url = $this->getLinkUrl($uri);
        }
        switch ($type) {
            case 'css':
                $default = array(
                        'type'  => 'text/css',
                        'rel'   => 'stylesheet',
                        'media' => 'screen'
                    );
                $options = array_merge($default, $options);
                $options['href'] = $url;
                $this->_links['link'][$uri] = $options;
                break;

            case 'link':
                $options['href'] = $url;
                $this->_links['link'][$uri] = $options;
                break;

            case 'script':
            case 'js':
                $options['src'] = $url;
                $this->_links['script'][$uri] = $options;
                break;

            default:
                break;

        }
        return $this;
    }

    /**
     * Get links by type
     *
     * @param string $type
     * @return mixed
     */
    public function getLinks($type)
    {
        return isset($this->_links[$type]) ? $this->_links[$type] : array();
    }
}
