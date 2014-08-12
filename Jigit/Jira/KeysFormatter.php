<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 12.08.2014
 * Time: 1:50
 */

namespace Jigit\Jira;

/**
 * Class KeysFormatter
 *
 * @package Jigit\Jira
 */
class KeysFormatter
{
    const DEFAULT_KEYS_COUNT = 5;

    /**
     * @param string $keys
     * @param int    $count     Number keys in line
     * @param string $delimiter
     * @return string
     */
    static public function format($keys, $count = self::DEFAULT_KEYS_COUNT, $delimiter = PHP_EOL)
    {
        return preg_replace('/(([A-Za-z-0-9]+,\s*){' . $count . '})/', '$1' . $delimiter, $keys);
    }
}
