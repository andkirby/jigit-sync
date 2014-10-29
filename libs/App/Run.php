<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 8/15/2014
 * Time: 12:09 AM
 */

namespace App;
use \Jigit\Dispatcher;
use \Jigit\Config;
use Jigit\Exception;
use \Jigit\Output;
use \Jigit\Report;
use \Jigit\Git;
use \Jigit\Config\Reader as Reader;
use \Jigit\Jira as JigitJira;
use \chobie\Jira as Jira;
use Jigit\UserException;

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
     * @return $this
     * @throws \Jigit\Exception
     * @throws \Jigit\UserException
     */
    public function run($action, array $params)
    {
        $this->_setAction($action);
        $this->_setProject(@$params['p']);
        $this->_initConfig();
        $this->_setJiraConfig();
        $this->_setProjectConfig();
        $this->_setParams($params);

        $this->_setHeaderOutput();
        $this->_setProjectInfoOutput();

        $this->_processAction();
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
                    Config\Jira::getJiraUsername(), Config\Jira::getPassword()
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
        return $git->getCommits();
    }

    /**
     * Get JQLs for report
     *
     * @param string      $gitKeys
     * @param null|string $file
     * @return array
     */
    protected function _getJqls($gitKeys, $file = null)
    {
        $jqls = new JigitJira\Jql($file);
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
        Config::getInstance()->setData('output', $output);
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
        if (isset($params['low'])) {
            Config\Project::setGitBranchLow($params['low']);
        }
        if (isset($params['top'])) {
            Config\Project::setGitBranchTop($params['top']);
        }
        if (isset($params['ver'])) {
            Config\Project::setJiraTargetFixVersion($params['ver']);
        }
        if (isset($params['i'])) {
            Config\Project::setJiraTargetFixVersionInProgress($params['i']);
        }
        if (isset($params['h'])) {
            $str = $this->getHelp();
            $this->getOutput()->add($str);
            //todo Refactor code to avoid trowing exception
            throw new UserException('Help', 911);
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
        return Config::getInstance()->getData('output');
    }

    /**
     * Init config
     *
     * @return $this
     */
    protected function _initConfig()
    {
        Config::getInstance();
        return $this;
    }

    /**
     * Set JIRA config
     *
     * @return string
     */
    protected function _setJiraConfig()
    {
        $connectFile = JIGIT_ROOT . '/config/jira-config.ini';
        $reader = new Reader\Ini();
        $config = $reader->read($connectFile, $this->_project);
        if (!$config) {
            $config = $reader->read($connectFile, 'main');
        }
        Config\Jira::setJiraUsername($config['jira_user']);
        Config\Jira::setJiraUrl($config['jira_url']);
        return $this;
    }

    /**
     * Set JIRA config
     *
     * @return string
     */
    protected function _setProjectConfig()
    {
        $connectFile = JIGIT_ROOT . '/config/project.ini';
        $reader = new Reader\Ini();
        $config = $reader->read($connectFile, $this->_project);
        Config\Project::getInstance()->addData($config);
        Config\Project::setProjectGitRoot($config['project_git_root']);
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
     * Set project info output
     *
     * @return $this
     */
    protected function _setProjectInfoOutput()
    {
        $inProgress = Config\Project::getJiraTargetFixVersionInProgress() ? 'YES' : 'NO';
        $branches = Config\Project::getGitBranchLow()
            . ' -> ' . Config\Project::getGitBranchTop();
        $this->getOutput()->enableDecorator(true, true)
            ->add("Project:             {$this->_project}")
            ->add("Compare:             " . $branches)
            ->add("Target FixVersion:   " . Config\Project::getJiraTargetFixVersion())
            ->add("Version in progress: $inProgress")
            ->add("Sprint:              " . Config\Project::getJiraActiveSprints())
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
        Config\Project::setJiraProject($this->_project);
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
        Config::getInstance()->setData('debug_mode', $flag);
        return $this;
    }

    /**
     * Get help string
     *
     * @return string
     */
    public function getHelp()
    {
        //@startSkipCommitHooks
        return <<<STR
    [action] - First verb in command.
               Available actions:
               report     - make report to identify problems.
               push-tasks - pushed tasks to done which added to a given version.
    p        - Project key.
    low      - VCS low branch/tag.
    top      - Target VCS branch/tag.
    i        - Version \"In progress\" status.
    v        - Version name.
    debug    - Debug mode.
STR;
        //@finishSkipCommitHooks
    }

    /**
     * Process request by action verb
     *
     * @throws \Jigit\Exception
     */
    protected function _processAction()
    {
        if (self::ACTION_REPORT == $this->_action) {
            $gitKeys       = $this->_getGitKeys();
            $gitKeysString = implode(',', array_keys($gitKeys));

            Config::addDebug($gitKeysString);

            $this->getOutput()->add('Found issues in VCS:');
            $this->getOutput()->add(JigitJira\KeysFormatter::format($gitKeysString, 7));

            $jqlList = $this->_getJqls($gitKeysString);

            $report = new Report();
            $report->make($this->_getApi(), $jqlList);
        } elseif (self::ACTION_PUSH_TASKS == $this->_action) {
            throw new Exception('Not implemented.');
//            $report = new Report();
//            $report->makePushReport($this->_getApi(), $this->getVcs(), $this->_getJqls(null, 'jqls-set-fixversion.csv'));
        } else {
            throw new Exception('Invalid action.');
        }
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
}
