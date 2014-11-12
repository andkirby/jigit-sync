<?php
namespace Lib\Model\Data;

use Lib\Exception;
use Lib\Record;

/**
 * Class Resource
 *
 * @package Lib\Model\Data
 */
class Resource
{
    /**
     * Resources list
     *
     * @var array
     */
    static protected $_resources = array();

    /**
     * Get resource by name
     *
     * @param string $name
     * @throws Exception
     * @return Record
     */
    public static function getResource($name)
    {
        if (!is_string($name)) {
            throw new Exception('Name is not a string.');
        }
        if (!isset(self::$_resources[$name])) {
            self::$_resources[$name] = new Record(array('filename' => $name . '.ini'));
        }
        return self::$_resources[$name];
    }
}
