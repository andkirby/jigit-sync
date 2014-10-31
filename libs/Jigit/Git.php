<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 8/15/2014
 * Time: 3:13 AM
 */

namespace Jigit;
use \Jigit\Config;
use \Jigit\Vcs\InterfaceVcs;
use \Jigit\Dispatcher\InterfaceDispatcher;

/**
 * GIT adapter
 *
 * @package Jigit
 */
class Git implements InterfaceVcs
{
    /**#@+
     * Log delimiters
     */
    const LOG_PARAM_DELIMITER = '|@|';
    const LOG_DELIMITER = '|@||';
    /**#@-*/

    /**
     * Dispatcher
     *
     * @var InterfaceDispatcher
     */
    protected $_dispatcher;

    /**
     * Requested commits list
     *
     * @var array
     */
    protected $_commits;

    /**
     * Get JIRA keys from range
     *
     * @return array
     * @throws UserException
     */
    public function getCommits()
    {
        if (null === $this->_commits) {
            /**
             * Get issues between different code versions
             */
            $branchLow = Config\Project::getGitBranchLow();
            $branchTop = Config\Project::getGitBranchTop();
            $gitRoot = Config\Project::getProjectRoot();
            $project = Config\Project::getJiraProject();

            $this->isBranchValid($gitRoot, $branchLow);
            $this->isBranchValid($gitRoot, $branchTop);

            $format = $this->_getLogFormat();
            $gitRoot = Config\Project::getProjectRoot();

            $log = $this->_getLog($gitRoot, $branchLow, $branchTop, $format);

            Config::addDebug('LOG: ' . $log);
            if (!$log) {
                throw new UserException('No VCS log found.');
            }
            $this->_commits = $this->_getGroupedCommits($log, $project);
        }
        return $this->_commits;
    }

    /**
     * Run GIT command in project dir
     *
     * @param string        $command
     * @param null|string   $gitRoot
     * @return mixed
     */
    public function runInProjectDir($command, $gitRoot = null)
    {
        if (false === strpos($command, 'git ')) {
            $command = 'git ' . $command;
        }
        $gitRoot = $gitRoot ?: Config\Project::getProjectRoot();
        $command = str_replace('git ', "git --git-dir $gitRoot/.git/ ", $command);
        return $this->run($command);
    }

    /**
     * Get tags list
     *
     * @param bool $reverse
     * @return array
     */
    public function getTags($reverse = true)
    {
        $tags = explode("\n", trim($this->runInProjectDir('tag -l')));
        //@startSkipCommitHooks
        $callFunction = function ($a, $b) {
            return -1 * version_compare($a, $b);
        };
        //@finishSkipCommitHooks
        usort($tags, $callFunction);
        if ($reverse) {
            $tags = array_reverse($tags);
        }
        return $tags;
    }

    /**
     * Run GIT command
     *
     * @param string $command
     * @return string
     */
    static public function run($command)
    {
        Config::addDebug('GIT command: ' . $command);
        return `$command`;
    }

    /**
     * Get commits grouped by jira keys.
     *
     * @param string $commit
     * @param string $project
     * @return array
     * @throws UserException
     */
    protected function _getGroupedCommits($commit, $project)
    {
        $keys = array();
        $commits = explode($this->_getCommitDelimiter(), $commit);
        foreach ($commits as $commit) {
            $info = $this->_getCommitInfo($commit);
            $issueKey = $this->_getIssueKey($project, $info['message']);
            if (!$issueKey) {
                throw new UserException("Issue key is not set for hash '{$info['hash']}' of {$info['author']}.");
            }
            $keys[$issueKey]['hash'][$info['author']][] = $info['hash'];
        }
        return $keys;
    }

    /**
     * Get issue key
     *
     * @param string $project   Project key
     * @param string $message   Commit message
     * @return mixed
     */
    protected function _getIssueKey($project, $message)
    {
        $matches = array();
        preg_match('/' . $project . '-[0-9]+/', $message, $matches);
        return $matches[0];
    }

    /**
     * Get commit info
     *
     * @param string $commit
     * @return array
     */
    protected function _getCommitInfo($commit)
    {
        @list($hash, $author, $message) = explode($this->_getCommitParamDelimiter(), trim($commit));
        return array(
            'hash'    => $hash,
            'author'  => $author,
            'message' => $message,
        );
    }

    /**
     * Get commit delimiter
     *
     * @return string
     */
    protected function _getCommitDelimiter()
    {
        return self::LOG_DELIMITER;
    }

    /**
     * Get commit param delimiter
     *
     * @return string
     */
    protected function _getCommitParamDelimiter()
    {
        return self::LOG_PARAM_DELIMITER;
    }

    /**
     * Validate branches
     *
     * @param string $gitRoot
     * @param string $branch
     * @return bool
     * @throws UserException
     */
    public function isBranchValid($gitRoot, $branch)
    {
        //@startSkipCommitHooks
        $branchFound = (bool)$this->run("git --git-dir $gitRoot/.git/ branch -a --list $branch");
        if (!$branchFound) {
            $branchFound = (bool)$this->run("git --git-dir $gitRoot/.git/ tag --list $branch");
            if (!$branchFound) {
                throw new UserException("Branch or tag $branch not found.");
            }
        }
        //@finishSkipCommitHooks
        return $branchFound;
    }

    /**
     * Get log format
     *
     * @return string
     */
    protected function _getLogFormat()
    {
        $delimiter    = $this->_getCommitParamDelimiter();
        $logDelimiter = $this->_getCommitDelimiter();
        return "%h$delimiter%cn$delimiter%s$logDelimiter";
    }

    /**
     * Get log between branches
     *
     * @param string $gitRoot
     * @param string $branchLow
     * @param string $branchTop
     * @param string $format
     * @return string
     */
    protected function _getLog($gitRoot, $branchLow, $branchTop, $format)
    {
        //@startSkipCommitHooks
        $log = $this->run(
            "git --git-dir $gitRoot/.git/ log $branchLow..$branchTop --pretty=format:\"$format\" --no-merges"
        );
        //@finishSkipCommitHooks
        return trim($log, $this->_getCommitDelimiter());
    }

    /**
     * Get VCS helper
     *
     * @param string $name
     * @param array  $options
     * @return Git\Helper\AbstractHelper
     */
    public function getHelper($name, array $options = array())
    {
        $class = "Git\\Helper\\$name";
        /** @var Git\Helper\AbstractHelper $helper */
        $helper = new $class($options);
        $helper->setEngine($this);
        return $helper;
    }

    /**
     * Get Dispatcher
     *
     * @return InterfaceDispatcher
     */
    public function getDispatcher()
    {
        return $this->_dispatcher;
    }

    /**
     * Set Dispatcher
     *
     * @param InterfaceDispatcher $dispatcher
     * @return $this
     */
    public function setDispatcher(InterfaceDispatcher $dispatcher)
    {
        $this->_dispatcher = $dispatcher;
        return $this;
    }
}
