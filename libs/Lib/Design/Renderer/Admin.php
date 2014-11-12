<?php
namespace Lib\Design\Renderer;

use Lib\Design\Renderer;

/**
 * Class Renderer\Admin
 *
 * @package Lib\Design
 */
class Admin extends Renderer
{
    /**
     * Get package
     *
     * @return string
     */
    protected function _getPackage()
    {
        return 'admin';
    }
}
