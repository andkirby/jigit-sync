<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 13.08.2014
 * Time: 1:25
 */

namespace Jigit\Jira\Jql\Reader;

/**
 * Class Csv
 *
 * @package Jigit\Jira\Jql\Reader
 */
class Csv
{
    /**
     * Get data from CSV file in array
     *
     * @param string $filename
     * @return array
     */
    public function toArray($filename)
    {
        return $this->_parse($filename);
    }

    /**
     * Get data from CSV file in array key-value
     *
     * @param string $filename
     * @return array
     */
    public function toAssocArray($filename)
    {
        return $this->_parse($filename, true);
    }

    /**
     * Parse file
     *
     * @param string $filename
     * @param bool   $assoc
     * @param string $delimiter
     * @param string $enclosure
     * @return array
     */
    protected function _parse($filename, $assoc = false, $delimiter = ',', $enclosure = '"')
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }

        $header = null;
        $data = array();
        $handle = fopen($filename, 'r');
        if ($handle) {
            if (!$assoc) {
                $header = fgetcsv($handle, 1000, $delimiter, $enclosure);
            }
            while (false !== ($row = fgetcsv($handle, 1000, $delimiter))) {
                if ($assoc) {
                    $data[$row[0]] = $row[1];
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }
        return $data;
    }
}
