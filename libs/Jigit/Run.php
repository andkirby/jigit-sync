<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 8/15/2014
 * Time: 12:09 AM
 */

namespace Jigit;
use chobie\Jira as Jira;
use Jigit\Config;
use Jigit\Config\Reader as Reader;
use Jigit\Dispatcher;
use Jigit\Jira as JigitJira;
use Lib\Config as LibConfig;

/**
 * Class Run
 *
 * @package App
 */
class Run implements Dispatcher\InterfaceDispatcher
{
    /**#@+
     * Action names
     */
    const ACTION_REPORT = 'report';
    const ACTION_PUSH_TASKS = 'push-tasks';
    /**#@-*/

    /**
     * Output
     *
     * @var Output
     */
    protected $_output;

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
     * @param Output $output
     * @throws Exception
     * @throws UserException
     * @return $this
     */
    public function run($action, array $params, $output)
    {
        $this->initialize($action, $params, $output)
            ->_processAction();
        return $this;
    }

    /**
     * Run making report
     *
     * @param string $action
     * @param array  $params
     * @param Output $output
     * @throws Exception
     * @throws UserException
     * @return $this
     */
    public function initialize($action, array $params, $output)
    {
        if ($this->_project) {
            return $this;
        }
        $this->setOutput($output);
        $this->_checkRequestedHelp($params);
        $this->_setProject(@$params['p']);
        $this->_setAction($action);
        $this->_initConfig();
        Config::getInstance()->setData('output', $this->_output);
        $this->_mergeProjectConfig();
        $this->_setParams($params);
        $this->_setHeaderOutput();
        return $this;
    }

    /**
     * Set action verb
     *
     * @param string $action
     * @return $this
     * @throws \Jigit\UserException
     */
    protected function _setAction($action)
    {
        if (!in_array($action, $this->_availableActions)) {
            throw new UserException('Proper action is not set.');
        }
        $this->_action = $action;
        return $this;
    }

    /**
     * Get JIRA API object
     *
     * @return Jira\Api
     */
    protected function _getApi()
    {
        if (null === $this->_api) {
            $this->_api = new Jira\Api(
                Config\Jira::getJiraUrl(),
                new Jira\Api\Authentication\Basic(
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
     * @throws \Jigit\UserException
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
        $jqls = new JigitJira\Jql($action);
        return $jqls->getJqls($gitKeys);
    }

    /**
     * Set output
     *
     * @param Output $output
     * @return $this
     */
    public function setOutput(Output $output)
    {
        $this->_output = $output;
        return $this;
    }

    /**
     * Set custom params
     *
     * @param array $params
     * @return $this
     * @throws \Jigit\UserException
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
     * Get output
     *
     * @return Output
     */
    public function getOutput()
    {
        return $this->_output;
    }

    /**
     * Init config
     *
     * @todo Need to make refactoring
     * @throws Exception
     * @throws UserException
     * @return $this
     */
    protected function _initConfig()
    {
        $files = array(APP_ROOT . '/config/app.yml');
        Config::loadConfig($files);
        $config = Config::getInstance();

        /**
         * Load extra files
         */
        $configDir = APP_ROOT . DIRECTORY_SEPARATOR
            . $config->getData('app/config_files/base_dir');

        //project config files directory
        $projectDir = $configDir . DIRECTORY_SEPARATOR
            . $config->getData('app/config_files/project_dir');

        //get project config files
        $files = array(
            $configDir . DIRECTORY_SEPARATOR . $config->getData('app/config_files/local'),
            $projectDir . DIRECTORY_SEPARATOR . $this->_project . '.yml',
        );
        Config::loadConfig($files);
        Config\Project::setJiraProject($this->_project);
        return $this;
    }

    /**
     * Set header output
     *
     * @return $this
     */
    protected function _setHeaderOutput()
    {
        $version = Config::getVersion();
        $this->getOutput()
            ->enableDecorator(true)
            ->add("JiGIT v$version - JIRA GIT Synchronization Tool")
            ->add('GitHUB: https://github.com/andkirby/jigit-sync')
            ->disableDecorator();
        return $this;
    }

    /**
     * Set project
     *
     * @param string $project
     * @return $this
     * @throws \Jigit\UserException
     */
    protected function _setProject($project)
    {
        if (!$project) {
            $this->getOutput()->add($this->getHelp());
            throw new UserException('Please set project.');
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
     * Get help string
     *
     * @return string
     */
    public function getHelp()
    {
        return file_get_contents(APP_ROOT . '/config/cli-manual.txt');
    }

    /**
     * Process request by action verb
     *
     * @throws \Jigit\Exception
     */
    protected function _processAction()
    {
        $report = new Report();
        $report->setApi($this->_getApi())
            ->setVcs($this->getVcs());
        if (self::ACTION_REPORT == $this->_action) {
            //ignore invalid commits (which does not have issue key)
            $this->getVcs()->setCheckNotValidCommits(
                Config::getInstance()->getData('app/vcs/bad_commit_check')
            );

            $this->analiseRequest();
            $this->setProjectInfoOutput();

            $gitKeys = $this->_getGitKeys();

            $this->getOutput()->add('Found issues in VCS:');
            $this->getOutput()->add(implode(', ', $gitKeys));

            $report->make(
                $this->_getJqls($gitKeys, $this->_action)
            );
        } elseif (self::ACTION_PUSH_TASKS == $this->_action) {
            //ignore invalid commits (which does not have issue key)
            //skip this because we need to work with issue keys only
            $this->getVcs()->setCheckNotValidCommits(false);

            $this->setProjectInfoOutput();

            $report->make(
                $this->_getJqls(null, $this->_action)
            );
        } else {
            throw new Exception('Invalid action.');
        }
    }

    /**
     * Set project info output
     *
     * @return $this
     */
    public function setProjectInfoOutput()
    {
        $inProgress = Config\Project::getJiraTargetFixVersionInProgress() ? 'YES' : 'NO';
        $branches = Config\Project::getGitBranchLow()
            . ' -> ' . Config\Project::getGitBranchTop();
        $project = Config\Project::getJiraProject();
        $output = $this->getOutput();
        $output->enableDecorator(true, true)
            ->add("Project:             {$project}");
        if ($branches) {
            $output
                ->add("Compare:             " . $branches)
                ->add("Target FixVersion:   " . Config\Project::getJiraTargetFixVersion())
                ->add("Version in progress: $inProgress")
                ->add("Sprint:              " . Config\Project::getJiraActiveSprints());
        }
        $output->disableDecorator();
        return $this;
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
            throw new UserException('Please set your target FixVersion at least.');
        }

        $branches = $this->_getBranchesFromFixVersionAliasByFixVersion($fixVersion);
        if (!$branches) {
            $branches = $this->_getBranchesFromVcsByFixVersion($fixVersion);
        }
        if (!$branches) {
            throw new UserException("Branch for FixVersion '$fixVersion' not found. Please set it manually.");
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
            throw new UserException("Please use prefix '$prefix' in in the FixVersion name '$fixVersion'.");
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
            throw new UserException('Please specify your branch top and branch low.');
        }
        return $versionAliases[$fixVersion];
    }

    /**
     * Match branches from VCS
     *
     * @param string $fixVersion
     * @throws Exception
     * @throws UserException
     * @return array
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

        if ($matches && !empty($matches[2])) {
            $branchTop = $matches[2];
            if (!$branchLow) {
                if (false !== strpos($branchTop, 'hotfix')) {
                    $branchLow = 'master';
                } elseif (false !== strpos($branchTop, 'release')) {
                    $branchLow = 'master';
                } else {
                    throw new UserException("Could not find low branch for branch '$branchTop'. You may set it.");
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
            $str = $this->getHelp();
            $this->getOutput()->add($str);
            //todo Refactor code to avoid trowing exception
            throw new UserException('Help', 911);
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
