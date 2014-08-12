<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 12.08.2014
 * Time: 3:36
 */

namespace Jigit;

/**
 * Class Autoloader
 */
class Autoloader
{
    /**
     * Autoload class
     *
     * @param string $class
     * @return void
     */
    static public function autoload($class)
    {
        $file = str_replace('_', '/', $class) . '.php';
        $file = str_replace('\\', '/', $file);
        $file = str_replace('chobie/', '/', $file); //chobie namespace doesn't exist in the path
        require_once $file;
    }
}
