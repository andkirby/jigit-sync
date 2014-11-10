<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 12.08.2014
 * Time: 3:47
 */

namespace Jigit;

use Lib\Config\Node;
use Symfony\Component\Yaml\Parser;

/**
 * Class Config
 *
 * @package Jigit
 */
class Config
{
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
        return self::getInstance()->version;
    }

    /**
     * Load config
     *
     * At fist it set loaded config and in other times it will merge into exist one
     *
     * @param array $files
     */
    public static function loadConfig(array $files)
    {
        if (null === self::$_instance) {
            self::$_instance = self::_getConfigInstance(array_shift($files));
        }
        foreach ($files as $file) {
            self::$_instance->merge(self::_getConfigInstance($file));
        }
    }

    /**
     * Get config instance
     *
     * @param string $file
     * @return \Zend_Config
     */
    protected static function _getConfigInstance($file)
    {
        $yaml = new Parser();
        $config = (array) $yaml->parse(
            file_get_contents($file)
        );
        return self::_getConfigNode($config, true);
    }

    /**
     * Get config node
     *
     * @param array $data
     * @param bool  $allowModifications
     * @return Node
     */
    protected static function _getConfigNode(array $data, $allowModifications = false)
    {
        return new Node($data, $allowModifications);
    }

    /**
     * Get instance
     *
     * @throws Exception
     * @return Node
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            throw new Exception('Please use loadConfig() method to set config instance');
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
        return (bool) self::getInstance()->getData('app/debug_mode');
    }

    /**
     * Get output
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
     * @param string $string
     * @throws Exception
     */
    static public function addDebug($string)
    {
        if (self::isDebug()) {
            if (is_array($string)) {
                $string = implode(', ', $string);
            }
            self::getOutput()->add('DEBUG: ' . $string . PHP_EOL);
        }
    }
}
