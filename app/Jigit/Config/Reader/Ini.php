<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 8/15/2014
 * Time: 1:53 AM
 */

namespace Jigit\Config\Reader;
use Jigit\Exception;

/**
 * Class Ini
 *
 * @package Jigit\Config\Reader
 */
class Ini
{
    /**
     * Read ini file
     *
     * @param string      $file
     * @param string|null $section
     * @return array
     * @throws \Jigit\Exception
     */
    public function read($file, $section = null)
    {
        if (!is_file($file) || !is_readable($file)) {
            throw new Exception("File '$file' not found.");
        }
        $result = (array) parse_ini_file($file, true);
        if ($section) {
            return isset($result[$section]) ? $result[$section] : array() ;
        } else {
            return $result;
        }
    }
}
