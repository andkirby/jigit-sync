<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 12.08.2014
 * Time: 3:47
 */

namespace Jigit;

/**
 * Class Config
 *
 * @package Jigit
 */
class Config extends Data
{
    /**
     * App version
     */
    const VERSION = '0.6.1';

    /**
     * Instance
     *
     * @var Config
     */
    static protected $_instance;

    /**
     * Get application version
     *
     * @return string
     */
    static public function getVersion()
    {
        return self::VERSION;
    }

    /**
     * Get instance
     *
     * @return Config
     */
    static public function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Get debug mode status
     *
     * @return Config
     */
    static public function isDebug()
    {
        return (bool) self::getInstance()->getData('debug_mode');
    }

    /**
     * Get debug mode status
     *
     * @return Output
     * @throws Exception
     */
    static public function getOutput()
    {
        $output = self::getInstance()->getData('output');
        if ($output instanceof Output) {
            return $output;
        }
        throw new Exception('Output is not set.');
    }

    /**
     * Get debug mode status
     *
     * @param $string
     * @throws Exception
     */
    static public function addDebug($string)
    {
        if (self::isDebug()) {
            self::getOutput()->addDelimiter();
            self::getOutput()->add('DEBUG: ' . $string);
        }
    }
}
