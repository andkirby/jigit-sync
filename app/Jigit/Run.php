<?php
namespace Jigit;

use chobie\Jira as JiraLib;
use Jigit\Config;
use Jigit\Dispatcher;
use Lib\Config as LibConfig;

/**
 * Class Run
 *
 * @package App
 */
class Run implements Dispatcher\InterfaceDispatcher
{
    /**#@+
     * Exception codes
     */
    const CODE_PROJECT_WRONG                 = 10;
    const CODE_VERSION_WRONG                 = 11;
    const CODE_BRANCH_VERSION_NOT_FOUND      = 12;
    const CODE_VERSION_PREFIX_NOT_SET        = 13;
    const CODE_BRANCH_LOW_TOP_EMTPY          = 14;
    const CODE_LOW_BRANCH_NOT_FOUND_NEED_SET = 15;
    const CODE_ACTION_EMPTY                  = 16;
    const CODE_SHOW_HELP                     = 911;
    /**#@-*/

    /**#@+
     * Action names
     */
    const ACTION_REPORT     = 'report';
    const ACTION_PUSH_TASKS = 'push-tasks';
    /**#@-*/

    /**
     * VCS model
     *
     * @var Git
     */
    protected $_vcs;

    /**
     * Api
     *
     * @var Jira\Api
     */
    protected $_api;

    /**
     * Project key in JIRA
     *
     * @var string
     */
    protected $_project;

    /**
     * Available actions
     *
     * @var string
     */
    protected $_availableActions;

    /**
     * Current action verb
     *
     * @var string
     */
    protected $_action;

    /**
     * Set actions
     */
    public function __construct()
    {
        $this->_availableActions = array(
            self::ACTION_REPORT,
            self::ACTION_PUSH_TASKS,
        );
    }

    /**
     * Run making report
     *
     * @param string $action
     * @param array  $params
     * @throws Exception
     * @throws UserException
     * @return Report
     */
    public function run($action, array $params)
    {
        $this->initialize($action, $params);
        $report = new Report();
        return $this->processAction($report);
    }

    /**
     * Run making report
     *
     * @param string $action
     * @param array  $params
     * @throws Exception
     * @throws UserException
     * @return $this
     */
    public function initialize($action, array $params)
    {
        if ($this->_project) {
            return $this;
        }
        $this->setAction($action);
        $this->_checkRequestedHelp($params);
        $this->setProject(@$params['p']);
        $this->initConfig();
        $this->_setParams($params);
        return $this;
    }

    /**
     * Set action verb
     *
     * @param string $action
     * @return $this
     * @throws UserException
     */
    public function setAction($action)
    {
        if (!in_array($action, $this->_availableActions)) {
            throw new UserException('Proper action is not set.', self::CODE_ACTION_EMPTY);
        }
        $this->_action = $action;
        return $this;
    }

    /**
     * Get available actions
     *
     * @return string
     */
    public function getAvailableActions()
    {
        return $this->_availableActions;
    }

    /**
     * Get JIRA API object
     *
     * @return Jira\Api
     */
    public function getApi()
    {
        if (null === $this->_api) {
            $this->_api = new Jira\Api(
                Config\Jira::getJiraUrl(),
                new JiraLib\Api\Authentication\Basic(
                    Config\Jira::getUsername(), Config\Jira::getPassword()
                )
            );
        }
        return $this->_api;
    }

    /**
     * Get GIT keys found from range
     *
     * @return array
     * @throws UserException
     */
    protected function _getGitKeys()
    {
        $git = $this->getVcs();
        return array_keys($git->getCommits());
    }

    /**
     * Get JQLs for report
     *
     * @param string      $gitKeys
     * @param null|string $action
     * @return array
     */
    protected function _getJqls($gitKeys, $action)
    {
        $jqls = new Jira\Jql($action);
        return $jqls->getJqls($gitKeys);
    }

    /**
     * Set custom params
     *
     * @param array $params
     * @return $this
     * @throws UserException
     */
    protected function _setParams(array $params)
    {
        $map = Config::getInstance()->getData('app/query/params_map');
        foreach ($map as $paramName => $configXpath) {
            if (isset($params[$paramName])) {
                Config::getInstance()->setData($configXpath, $params[$paramName]);
            }
        }
        return $this;
    }

    /**
     * Init config
     *
     * @throws Exception
     * @throws UserException
     * @return $this
     */
    public function initConfig()
    {
        Config::loadConfig(array(JIGIT_ROOT . '/config/app.yml'));

        /**
         * Load extra files
         */
        $files = array(
            $this->_getConfigDir() . DIRECTORY_SEPARATOR
                . Config::getInstance()->getData('app/config_files/local'),
        );
        Config::loadConfig($files);
        if ($this->_project) {
            //get project config files
            $this->_loadProjectConfig();
        }
        return $this;
    }

    /**
     * Load project config
     *
     * @throws Exception
     */
    protected function _loadProjectConfig()
    {
        Config\Project::setJiraProject($this->_project);

        //project config files directory
        $projectDir = $this->_getConfigDir() . DIRECTORY_SEPARATOR
            . Config::getInstance()->getData('app/config_files/project_dir');
        $files      = array(
            $projectDir . DIRECTORY_SEPARATOR . $this->_project . '.yml'
        );
        Config::loadConfig($files);

        $this->_mergeProjectConfig();
        return $this;
    }

    /**
     * Get config directory
     *
     * @return string
     */
    protected function _getConfigDir()
    {
        return JIGIT_ROOT . DIRECTORY_SEPARATOR
        . Config::getInstance()->getData('app/config_files/base_dir');
    }

    /**
     * Set project
     *
     * @param string $project
     * @return $this
     * @throws UserException
     */
    public function setProject($project)
    {
        if (!$project) {
            throw new UserException('Please set project.', self::CODE_PROJECT_WRONG);
        }
        $this->_project = strtoupper($project);
        return $this;
    }

    /**
     * Set debug mode
     *
     * @param bool $flag
     * @return $this
     */
    public function setDebugMode($flag)
    {
        Config::getInstance()->setData('app/debug_mode', $flag);
        return $this;
    }

    /**
     * Process request by action verb
     *
     * @param Report $report
     * @throws Exception
     * @throws UserException
     * @return Report
     */
    public function processAction($report)
    {
        $report->setApi($this->getApi())
            ->setVcs($this->getVcs());
        if (self::ACTION_REPORT == $this->_action) {
            //ignore invalid commits (which does not have issue key)
            $this->getVcs()->setCheckNotValidCommits(
                Config::getInstance()->getData('app/vcs/bad_commit_check')
            );

            $this->analiseRequest();

            $gitKeys = $this->_getGitKeys();
            \Zend_Registry::set('vcs_keys', $gitKeys);

            $report->make(
                $this->_getJqls($gitKeys, $this->_action)
            );
        } elseif (self::ACTION_PUSH_TASKS == $this->_action) {
            //ignore invalid commits (which does not have issue key)
            //skip this because we need to work with issue keys only
            $this->getVcs()->setCheckNotValidCommits(false);

            $report->make(
                $this->_getJqls(null, $this->_action)
            );
        } else {
            throw new Exception('Invalid action.');
        }
        return $report;
    }

    /**
     * Analise requested FixVersion to identify requested branches
     *
     * @return $this
     * @throws Exception
     * @throws UserException
     * @todo Refactor this method
     */
    public function analiseRequest()
    {
        if (Config\Project::getGitBranchLow() && Config\Project::getGitBranchTop()) {
            return $this;
        }
        $fixVersion = Config\Project::getJiraTargetFixVersion();
        if (!$fixVersion) {
            throw new UserException('Please set your target version at least.', self::CODE_VERSION_WRONG);
        }

        $branches = $this->_getBranchesFromFixVersionAliasByFixVersion($fixVersion);
        if (!$branches) {
            $branches = $this->_getBranchesFromVcsByFixVersion($fixVersion);
        }
        if (!$branches) {
            throw new UserException(
                "Branch for version '$fixVersion' not found. Please set it manually.",
                self::CODE_BRANCH_VERSION_NOT_FOUND
            );
        }
        Config\Project::setGitBranchTop($branches['branch_top']);
        Config\Project::setGitBranchLow($branches['branch_low']);
        return $this;
    }

    /**
     * Get JIRA target fix version number
     *
     * @param string $fixVersion
     * @throws Exception
     * @throws UserException
     * @return string
     */
    protected function _getJiraTargetFixVersionNumber($fixVersion)
    {
        $prefix = Config::getInstance()->getData('app/vcs/version/prefix');
        if (false === strpos($fixVersion, $prefix)) {
            throw new UserException(
                "Please use prefix '$prefix' in in the version name '$fixVersion'.",
                self::CODE_VERSION_PREFIX_NOT_SET
            );
        }
        return substr($fixVersion, strlen($prefix));
    }

    /**
     * Get branches from FixVersion alias
     *
     * @param string $fixVersion
     * @return array|null
     * @throws Exception
     * @throws UserException
     */
    protected function _getBranchesFromFixVersionAliasByFixVersion($fixVersion)
    {
        //check aliases
        $versionAliases = Config::getInstance()->getData('app/vcs/version/alias');
        if (!isset($versionAliases[$fixVersion])) {
            return null;
        }
        if (!isset($versionAliases[$fixVersion]['branch_top'])
            || !isset($versionAliases[$fixVersion]['branch_low'])
        ) {
            throw new UserException(
                'Please specify your branch top and branch low.', self::CODE_BRANCH_LOW_TOP_EMTPY
            );
        }

        if (Config\Project::getVcsForceRemoteStatus()) {
            //add remote name prefix
            $remoteNamePrefix = Config\Project::getVcsRemoteName();
            $versionAliases[$fixVersion]['branch_low'] =
                $remoteNamePrefix . '/' . $versionAliases[$fixVersion]['branch_low'];
            $versionAliases[$fixVersion]['branch_top'] =
                $remoteNamePrefix . '/' . $versionAliases[$fixVersion]['branch_top'];
        }
        return $versionAliases[$fixVersion];
    }

    /**
     * Match branches from VCS
     *
     * @param string $fixVersion
     * @throws Exception
     * @throws UserException
     * @return array|null
     * @todo Actually this method contains two methods
     * @todo Methods to implement: 1) getTargetBranchesFromVcsBranches 2) getTargetBranchesFromVcsTags
     */
    protected function _getBranchesFromVcsByFixVersion($fixVersion)
    {
        $startRegular = '(remotes\/)?[\S]*?';
        if (Config\Project::getVcsForceRemoteStatus()) {
            $startRegular = '(remotes\/)[\S]*?';
        }
        $branchLow = Config\Project::getGitBranchLow();
        $version      = $this->_getJiraTargetFixVersionNumber($fixVersion);
        $branchesList = $this->getVcs()->runInProjectDir('git branch -a');
        Config::addDebug($branchesList);
        $versionPrefixInBranch = Config::getInstance()->getData('app/vcs/version/prefix_in_branch');
        $regular               = '~' . $startRegular
            . '(\S*?'. $versionPrefixInBranch . str_replace('.', '\.', $version) . ').*~';
        preg_match($regular, $branchesList, $matches);

        if (!Config\Project::getVcsForceRemoteStatus() && false !== strpos($matches[0], 'remote')) {
            //force using remote because no local branches
            Config\Project::setVcsForceRemoteStatus(true);
        }

        if ($matches && !empty($matches[2])) {
            $branchTop = $matches[2];
            if (!$branchLow) {
                if (false !== strpos($branchTop, 'hotfix')) {
                    $branchLow = Config::getInstance()->getData('app/vcs/git_flow/master');
                } elseif (false !== strpos($branchTop, 'release')) {
                    $branchLow = Config::getInstance()->getData('app/vcs/git_flow/master');
                } else {
                    throw new UserException(
                        "Could not find low branch for branch '$branchTop'. You may set it.",
                        self::CODE_LOW_BRANCH_NOT_FOUND_NEED_SET
                    );
                }
            }

            if (Config\Project::getVcsForceRemoteStatus()) {
                //add remote name prefix
                $remoteNamePrefix = Config\Project::getVcsRemoteName();
                if ($remoteNamePrefix && false === strpos($branchLow, $remoteNamePrefix)) {
                    $branchLow = $remoteNamePrefix . '/' . $branchLow;
                }
            }
        } else {
            /**
             * Try to find tag
             */
            //todo refactor this block into separated method
            $tagsList = $this->getVcs()->runInProjectDir('git tag');
            Config::addDebug($tagsList);
            $tags = $this->getVcs()->getTags();
            $key = array_search($fixVersion, $tags);
            if (false === $key) {
                return null;
            }
            $branchTop = $tags[$key];
            $branchLow = $tags[$key - 1];
        }
        return array(
            'branch_low' => $branchLow,
            'branch_top' => $branchTop
        );
    }

    /**
     * Get VCS
     *
     * @return Git
     */
    public function getVcs()
    {
        if (null === $this->_vcs) {
            $this->_vcs = new Git();
            $this->_vcs->setDispatcher($this);
        }
        return $this->_vcs;
    }

    /**
     * Check requested help
     *
     * @param array $params
     * @return array
     * @throws UserException
     */
    protected function _checkRequestedHelp(array $params)
    {
        if (isset($params['h'])) {
            //todo Refactor code to avoid trowing exception
            throw new UserException('Help!', self::CODE_SHOW_HELP);
        }
        return $params;
    }

    /**
     * Merge project config into application config
     *
     * @return $this
     * @throws Exception
     */
    protected function _mergeProjectConfig()
    {
        $jiraConfig = Config::getInstance()->getData('project/' . $this->_project, false);
        if ($jiraConfig instanceof LibConfig\Node) {
            Config::getInstance()->getData('app', false)->merge($jiraConfig);
        }
        return $this;
    }
}
