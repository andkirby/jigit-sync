<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 12.08.2014
 * Time: 3:49
 */

namespace Jigit\Config;
use Jigit\Config as Config;
use Jigit\Exception;
use Jigit\Jira\Password as Password;

/**
 * Class User
 *
 * @package Jigit
 */
class Jira extends Config
{
    /**
     * Get JIRA password
     *
     * @return string
     */
    static public function getPassword()
    {
        $password = new Password(
            self::getInstance()->getData('app/jira/password_file')
            ?: JIGIT_ROOT . DIRECTORY_SEPARATOR . self::_getConfigDir()
            . DIRECTORY_SEPARATOR . 'jira.password'
        );
        return $password->getPassword();
    }

    /**
     * Get JIRA username
     *
     * @return string
     */
    static public function getUsername()
    {
        return self::getInstance()->getData('app/jira/username');
    }

    /**
     * Set JIRA username
     *
     * @param string $value
     * @return Config
     */
    static public function setJiraUsername($value)
    {
        return self::getInstance()->setData('app/jira/username', $value);
    }

    /**
     * Get JIRA url
     *
     * @return string
     */
    static public function getJiraUrl()
    {
        return self::getInstance()->getData('app/jira/url');
    }

    /**
     * Set JIRA url
     *
     * @param string $value
     * @return Config
     */
    static public function setJiraUrl($value)
    {
        return self::getInstance()->setData('app/jira/url', $value);
    }

    /**
     * Get config dir
     *
     * @return string|null
     * @throws Exception
     * @throws \Lib\Config\Exception
     */
    protected static function _getConfigDir()
    {
        return self::getInstance()->getData('app/config_files/base_dir');
    }

    /**
     * Get request API issue fields list
     *
     * @return string
     * @throws Exception
     */
    public static function getApiIssueFields()
    {
        if (self::getInstance()->getData('app/jira/issue/use_fields_list')) {
            return self::getInstance()->getDataString('app/jira/issue/fields', ',');
        }
        return '*navigable';
    }

    /**
     * Get request API issue fields list
     *
     * @return bool
     * @throws Exception
     */
    public static function getIssueViewSimple()
    {
        return self::getInstance()->getData('app/jira/issue/simple_view');
    }
}
