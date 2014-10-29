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
 * Class Project
 *
 * @package Jigit
 */
class Project extends Config
{
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
     * @return array
     */
    static public function getJiraActiveSprints()
    {
        return self::getInstance()->getData('jira_active_sprints');
    }

    /**
     * Set JIRA active sprints
     *
     * @param array|string|int $value
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
        return rtrim(self::getInstance()->getData('project_git_root'), '\\/');
    }

    /**
     * Set project GIT root
     *
     * @param string $path
     * @return Config
     * @throws \Jigit\UserException
     */
    static public function setProjectGitRoot($path)
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new UserException("Directory '$path' is not exists or not readable.");
        }
        if (!file_exists($path . '/.git') || !is_readable($path . '/.git')) {
            throw new UserException("Git directory '$path' is not exists or not readable.");
        }
        return self::getInstance()->setData('project_git_root', $path);
    }

    /**
     * Set project GIT root
     *
     * @deprecated
     * @return array
     */
    static public function getJiraNonAffectsCodeLabels()
    {
        return array('nocode', 'fixedIn');
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
            return self::getInstance()->getData('jira_' . $key);
        } else {
            $config = array();
            foreach (self::getInstance()->getData() as $key => $value) {
                if (0 === strpos($key, 'jira_')) {
                    $config[$key] = $value;
                }
            }
            return $config;
        }
    }

    /**
     * Get JIRA JQL configuration
     *
     * @param null|string $key
     * @return mixed
     */
    static public function getJiraJqlConfig($key = null)
    {
        if ($key) {
            return self::getInstance()->getData('jira_jql_' . $key);
        } else {
            $config = array();
            foreach (self::getInstance()->getData() as $key => $value) {
                if (0 === strpos($key, 'jira_jql_')) {
                    $config[$key] = $value;
                }
            }
            return $config;
        }
    }
}
