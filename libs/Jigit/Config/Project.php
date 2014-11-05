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
use Jigit\UserException;

/**
 * Class Project
 *
 * @package Jigit
 */
class Project extends Config
{
    /**#@+
     * Configuration paths
     */
    const PATH_TARGET_FIX_VERSION             = 'app/jira/jql/alias/version';
    const PATH_TARGET_FIX_VERSION_IN_PROGRESS = 'app/jira/jql/alias/version_in_progress';
    const PATH_ACTIVE_SPRINTS                 = 'app/jira/jql/alias/active_sprints';
    const PATH_VCS_BRANCH_LOW                 = 'app/git/branch_low';
    const PATH_VCS_BRANCH_TOP                 = 'app/git/branch_top';
    const PATH_JIRA_PROJECT                   = 'app/jira/project';
    const PATH_JIRA_JQL_ALIAS                 = 'app/jira/jql/alias';
    const PATH_JIRA                           = 'app/jira';
    const PATH_VCS_REMOTE_FORCE_USING         = 'app/vcs/remote/force_using';
    const PATH_VCS_REMOTE_NAME                = 'app/vcs/remote/name';
    /**#@-*/

    /**
     * Get JIRA project
     *
     * @return string
     */
    static public function getJiraProject()
    {
        return self::getInstance()->getData(self::PATH_JIRA_PROJECT);
    }

    /**
     * Set JIRA project
     *
     * @param string $value
     * @return Config
     */
    static public function setJiraProject($value)
    {
        return self::getInstance()->setData(self::PATH_JIRA_PROJECT, strtoupper($value));
    }

    /**
     * Get GIT branch top
     *
     * @return string
     */
    static public function getGitBranchTop()
    {
        return self::getInstance()->getData(self::PATH_VCS_BRANCH_TOP);
    }

    /**
     * Set GIT branch top
     *
     * @param string $value
     * @return Config
     */
    static public function setGitBranchTop($value)
    {
        return self::getInstance()->setData(self::PATH_VCS_BRANCH_TOP, $value);
    }

    /**
     * Get GIT branch low
     *
     * @return string
     */
    static public function getGitBranchLow()
    {
        return self::getInstance()->getData(self::PATH_VCS_BRANCH_LOW);
    }

    /**
     * Set GIT branch low
     *
     * @param string $value
     * @return Config
     */
    static public function setGitBranchLow($value)
    {
        return self::getInstance()->setData(self::PATH_VCS_BRANCH_LOW, $value);
    }

    /**
     * Get JIRA target fix version
     *
     * @return string
     */
    static public function getJiraTargetFixVersion()
    {
        return self::getInstance()->getData(self::PATH_TARGET_FIX_VERSION);
    }

    /**
     * Set JIRA target fix version
     *
     * @param string $value
     * @return Config
     */
    static public function setJiraTargetFixVersion($value)
    {
        return self::getInstance()->setData(self::PATH_TARGET_FIX_VERSION, $value);
    }

    /**
     * Get JIRA target fix version in progress
     *
     * @return string
     */
    static public function getJiraTargetFixVersionInProgress()
    {
        return self::getInstance()->getData(self::PATH_TARGET_FIX_VERSION_IN_PROGRESS);
    }

    /**
     * Set JIRA target fix version in progress
     *
     * @param string $value
     * @return Config
     */
    static public function setJiraTargetFixVersionInProgress($value)
    {
        return self::getInstance()->setData(self::PATH_TARGET_FIX_VERSION_IN_PROGRESS, $value);
    }

    /**
     * Get JIRA active sprints
     *
     * @param bool $asString Get concatenated string separated with ", " if TRUE
     * @throws Exception
     * @return array
     */
    static public function getJiraActiveSprints($asString = true)
    {
        if ($asString) {
            return self::getInstance()->getDataString(self::PATH_ACTIVE_SPRINTS);
        } else {
            return self::getInstance()->getData(self::PATH_ACTIVE_SPRINTS);
        }
    }

    /**
     * Set JIRA active sprints
     *
     * @param array|string|int $value
     * @return Config
     */
    static public function setJiraActiveSprints($value)
    {
        return self::getInstance()->setData(self::PATH_ACTIVE_SPRINTS, $value);
    }

    /**
     * Validate config
     *
     * @throws Exception
     * @throws UserException
     * @return Config
     */
    static public function validate()
    {
        $path = self::getProjectRoot();
        if (!is_dir($path) || !is_readable($path)) {
            throw new UserException("Directory '$path' is not exists or not readable.");
        }
        if (!is_dir($path . '/.git') || !is_readable($path . '/.git')) {
            throw new UserException("GIT directory '$path' is not exists or not readable.");
        }
        return true;
    }

    /**
     * Get project GIT root
     *
     * @return string
     */
    static public function getProjectRoot()
    {
        return rtrim(self::getInstance()->getData('project/' . self::getJiraProject() . '/root'), '\\/');
    }

    /**
     * Set project GIT root
     *
     * @param string $path
     * @return Config
     * @throws \Jigit\UserException
     */
    static public function setProjectRoot($path)
    {
        return self::getInstance()->setData('project/' . self::getJiraProject() . 'root', $path);
    }

    /**
     * Set project GIT root
     *
     * @param null|string $key
     * @return mixed
     */
    static public function getJiraConfig($key = null)
    {
        if ($key) {
            return self::getInstance()->getData(self::PATH_JIRA . '/' . $key);
        } else {
            return self::getInstance()->getData(self::PATH_JIRA);
        }
    }

    /**
     * Get JIRA JQL configuration
     *
     * @param null|string $key
     * @param bool        $asString
     * @throws Exception
     * @return mixed
     */
    static public function getJiraJqlAliases($key = null, $asString = true)
    {
        if ($key) {
            if ($asString) {
                return self::getInstance()->getDataString(self::PATH_JIRA_JQL_ALIAS . '/' . $key);
            } else {
                return self::getInstance()->getData(self::PATH_JIRA_JQL_ALIAS . '/' . $key);
            }
        } else {
            if ($asString) {
                return self::getInstance()->getData(self::PATH_JIRA_JQL_ALIAS, false)->getDataString();
            } else {
                return self::getInstance()->getData(self::PATH_JIRA_JQL_ALIAS);
            }
        }
    }

    /**
     * Get JIRA JQL configuration
     *
     * @throws Exception
     * @return bool
     */
    static public function getVcsForceRemoteStatus()
    {
        return (bool)self::getInstance()->getData(self::PATH_VCS_REMOTE_FORCE_USING);
    }

    /**
     * Get JIRA JQL configuration
     *
     * @throws Exception
     * @return bool
     */
    static public function getVcsRemoteName()
    {
        return self::getInstance()->getData(self::PATH_VCS_REMOTE_NAME);
    }
}
