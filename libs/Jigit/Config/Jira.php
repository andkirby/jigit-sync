<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 12.08.2014
 * Time: 3:49
 */

namespace Jigit\Config;
use \Jigit\Config as Config;
use Jigit\Exception;
use \Jigit\Jira\Password as Password;
use Jigit\UserException;

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
        $password = new Password();
        return $password->getPassword();
    }

    /**
     * Get JIRA username
     *
     * @return string
     */
    static public function getJiraUsername()
    {
        return self::getInstance()->getData('jira_username');
    }

    /**
     * Set JIRA username
     *
     * @param string $value
     * @return Config
     */
    static public function setJiraUsername($value)
    {
        return self::getInstance()->setData('jira_username', $value);
    }

    /**
     * Get JIRA url
     *
     * @return string
     */
    static public function getJiraUrl()
    {
        return self::getInstance()->getData('jira_url');
    }

    /**
     * Set JIRA url
     *
     * @param string $value
     * @return Config
     */
    static public function setJiraUrl($value)
    {
        return self::getInstance()->setData('jira_url', $value);
    }
}