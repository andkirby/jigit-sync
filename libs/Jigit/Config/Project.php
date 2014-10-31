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
        return self::getInstance()->getData('app/jira/project');
    }

    /**
     * Set JIRA project
     *
     * @param string $value
     * @return Config
     */
    static public function setJiraProject($value)
    {
        return self::getInstance()->setData('app/jira/project', strtoupper($value));
    }

    /**
     * Get GIT branch top
     *
     * @return string
     */
    static public function getGitBranchTop()
    {
        return self::getInstance()->getData('app/git/branch_top');
    }

    /**
     * Set GIT branch top
     *
     * @param string $value
     * @return Config
     */
    static public function setGitBranchTop($value)
    {
        return self::getInstance()->setData('app/git/branch_top', $value);
    }

    /**
     * Get GIT branch low
     *
     * @return string
     */
    static public function getGitBranchLow()
    {
        return self::getInstance()->getData('app/git/branch_low');
    }

    /**
     * Set GIT branch low
     *
     * @param string $value
     * @return Config
     */
    static public function setGitBranchLow($value)
    {
        return self::getInstance()->setData('app/git/branch_low', $value);
    }

    /**
     * Get JIRA target fix version
     *
     * @return string
     */
    static public function getJiraTargetFixVersion()
    {
        return self::getInstance()->getData('app/jira/jql/alias/target_fix_version');
    }

    /**
     * Set JIRA target fix version
     *
     * @param string $value
     * @return Config
     */
    static public function setJiraTargetFixVersion($value)
    {
        return self::getInstance()->setData('app/jira/jql/alias/target_fix_version', $value);
    }

    /**
     * Get JIRA target fix version in progress
     *
     * @return string
     */
    static public function getJiraTargetFixVersionInProgress()
    {
        return self::getInstance()->getData('app/jira/jql/alias/target_fix_version_in_progress');
    }

    /**
     * Set JIRA target fix version in progress
     *
     * @param string $value
     * @return Config
     */
    static public function setJiraTargetFixVersionInProgress($value)
    {
        return self::getInstance()->setData('app/jira/jql/alias/target_fix_version_in_progress', $value);
    }

    /**
     * Get JIRA active sprints
     *
     * @return array
     */
    static public function getJiraActiveSprints()
    {
        return self::getInstance()->getData('app/jira/jql/alias/active_sprints');
    }

    /**
     * Set JIRA active sprints
     *
     * @param array|string|int $value
     * @return Config
     */
    static public function setJiraActiveSprints($value)
    {
        return self::getInstance()->setData('app/jira/jql/alias/active_sprints', $value);
    }

    /**
     * Set JIRA active sprints
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
     * @deprecated
     * @return array
     */
    static public function getJiraNonAffectsCodeLabels()
    {
        return self::getInstance()->getData('app/jira/jql/alias/non_affects_code_labels');
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
            return self::getInstance()->getData('app/jira/' . $key);
        } else {
            return self::getInstance()->getData('app/jira');
        }
    }

    /**
     * Get JIRA JQL configuration
     *
     * @param null|string $key
     * @return mixed
     */
    static public function getJiraJqlAliases($key = null)
    {
        if ($key) {
            return self::getInstance()->getData('app/jira/jql/alias/' . $key);
        } else {
            return self::getInstance()->getData('app/jira/jql/alias');
        }
    }
}
