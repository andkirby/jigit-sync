<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 12.08.2014
 * Time: 3:49
 */

namespace Jigit\Config;
use \Jigit\Config as Config;
use \Jigit\Jira\Password as Password;

/**
 * Class User
 *
 * @package Jigit
 */
class User extends Config
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

    /**
     * Get JIRA project
     *
     * @return string
     */
    static public function getJiraProject()
    {
        return self::getInstance()->getData('jira_project');
    }

    /**
     * Set JIRA project
     *
     * @param string $value
     * @return Config
     */
    static public function setJiraProject($value)
    {
        return self::getInstance()->setData('jira_project', $value);
    }

    /**
     * Get GIT branch top
     *
     * @return string
     */
    static public function getGitBranchTop()
    {
        return self::getInstance()->getData('git_branch_top');
    }

    /**
     * Set GIT branch top
     *
     * @param string $value
     * @return Config
     */
    static public function setGitBranchTop($value)
    {
        return self::getInstance()->setData('git_branch_top', $value);
    }

    /**
     * Get GIT branch low
     *
     * @return string
     */
    static public function getGitBranchLow()
    {
        return self::getInstance()->getData('git_branch_low');
    }

    /**
     * Set GIT branch low
     *
     * @param string $value
     * @return Config
     */
    static public function setGitBranchLow($value)
    {
        return self::getInstance()->setData('git_branch_low', $value);
    }

    /**
     * Get JIRA target fix version
     *
     * @return string
     */
    static public function getJiraTargetFixVersion()
    {
        return self::getInstance()->getData('jira_target_fix_version');
    }

    /**
     * Set JIRA target fix version
     *
     * @param string $value
     * @return Config
     */
    static public function setJiraTargetFixVersion($value)
    {
        return self::getInstance()->setData('jira_target_fix_version', $value);
    }

    /**
     * Get JIRA target fix version in progress
     *
     * @return string
     */
    static public function getJiraTargetFixVersionInProgress()
    {
        return self::getInstance()->getData('jira_target_fix_version_in_progress');
    }

    /**
     * Set JIRA target fix version in progress
     *
     * @param string $value
     * @return Config
     */
    static public function setJiraTargetFixVersionInProgress($value)
    {
        return self::getInstance()->setData('jira_target_fix_version_in_progress', $value);
    }

    /**
     * Get JIRA active sprints
     *
     * @return string
     */
    static public function getJiraActiveSprints()
    {
        return self::getInstance()->getData('jira_active_sprints');
    }

    /**
     * Set JIRA active sprints
     *
     * @param string $value
     * @return Config
     */
    static public function setJiraActiveSprints($value)
    {
        return self::getInstance()->setData('jira_active_sprints', $value);
    }

    /**
     * Get project GIT root
     *
     * @return string
     */
    static public function getProjectGitRoot()
    {
        return self::getInstance()->getData('project_git_root');
    }

    /**
     * Set project GIT root
     *
     * @param string $value
     * @return Config
     */
    static public function setProjectGitRoot($value)
    {
        return self::getInstance()->setData('project_git_root', $value);
    }
} 
