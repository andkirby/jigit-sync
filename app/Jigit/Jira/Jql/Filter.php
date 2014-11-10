<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 8/15/2014
 * Time: 12:38 PM
 */

namespace Jigit\Jira\Jql;

/**
 * Filter to replace JQL variables with values
 *
 * @package Jigit\Jira\Jql
 */
class Filter
{
    /**
     * Variable wrapper
     */
    const VAR_WRAPPER = '%';

    /**
     * Filter JQL variables
     *
     * @param string $jql
     * @param array  $data
     * @param bool   $filterKeysFromData
     * @throws Filter\Exception
     * @return string
     */
    public function filter($jql, array $data, $filterKeysFromData = false)
    {
        if ($filterKeysFromData) {
            /**
             * Variable names will taken from data
             */
            $names = array_keys($data);
            return $this->_setVarValues($jql, $data, $names);
        } else {
            /**
             * Variable names will taken from JQL
             * All words wrapped with "%"
             * E.g.: %my_pretty_name%
             */
            $names = $this->_getJqlVars($jql);
            return $this->_setVarValues($jql, $data, $names);
        }
    }

    /**
     * Get value from data
     *
     * @param array  $data
     * @param string $var
     * @throws Filter\Exception
     * @return string|null
     */
    protected function _getValue(array $data, $var)
    {
        if (!isset($data[$var])) {
            throw new Filter\Exception("Key '$var' doesn't exist.");
        }
        return $data[$var];
    }

    /**
     * Parse JQL to get vars
     *
     * @param string $jql
     * @return array
     */
    protected function _getJqlVars($jql)
    {
        preg_match_all('/' . self::VAR_WRAPPER . '([A-z_]+)' . self::VAR_WRAPPER . '/', $jql, $vars);
        return (array)array_unique($vars[1]);
    }

    /**
     * Set var values into JQL
     *
     * @param string $jql
     * @param array  $data
     * @param array  $names
     * @throws Filter\Exception
     * @return mixed
     */
    protected function _setVarValues($jql, array $data, array $names)
    {
        foreach ($names as $var) {
            $jqlVar = self::VAR_WRAPPER . $var . self::VAR_WRAPPER;
            $jql = str_replace(
                $jqlVar, $this->_getValue($data, $var), $jql
            );
        }
        return $jql;
    }
}
