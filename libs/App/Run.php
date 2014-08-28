<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 8/15/2014
 * Time: 12:09 AM
 */

namespace App;
use \Jigit\Config;
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
class Run
{
    /**
     * Project key in JIRA
     *
     * @var string
     */
    protected $_project;

    /**
     * Run making report
     *
     * @param string $project
     * @param array  $params
     * @throws \Jigit\UserException
     */
    public function run($project, array $params)
    {
        $this->_initConfig();
        $this->_setProject($project);
        $this->_setJiraConfig();
        $this->_setProjectConfig();

        $this->setParams($params);

        $this->_setHeaderOutput();
        $this->_setProjectInfoOutput($project);
        $gitKeys = $this->_getGitKeys();
        $gitKeysString = implode(',', array_keys($gitKeys));

        Config::addDebug($gitKeysString);

        $this->getOutput()->add('Found issues in GIT:');
        $this->getOutput()->add(JigitJira\KeysFormatter::format($gitKeysString, 7));

        $jqlList = $this->_getJqls($gitKeysString);

        $report = new Report();
        $report->make($this->_getApi(), $jqlList);
    }

    /**
     * Get JIRA API object
     *
     * @return Jira\Api
     */
    protected function _getApi()
    {
        return new Jira\Api(
            Config\Jira::getJiraUrl(),
            new Jira\Api\Authentication\Basic(
                Config\Jira::getJiraUsername(), Config\Jira::getPassword()
            )
        );
    }

    /**
     * Get GIT keys found from range
     *
     * @return array
     * @throws \Jigit\UserException
     */
    protected function _getGitKeys()
    {
        $git = new Git();
        return $git->getCommits();
    }

    /**
     * Get JQLs for report
     *
     * @param string $gitKeys
     * @return array
     */
    protected function _getJqls($gitKeys)
    {
        $jqls = new JigitJira\Jql();
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
    public function setParams(array $params)
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
        Config\Project::setGitBranchTop($config['git_branch_top']);
        Config\Project::setGitBranchLow($config['git_branch_low']);
        Config\Project::setJiraTargetFixVersion($config['jira_target_fix_version']);
        Config\Project::setJiraTargetFixVersionInProgress($config['jira_target_fix_version_in_progress']);
        Config\Project::setJiraActiveSprints($config['jira_active_sprints']);
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
    p        - Project key.
    low      - VCS low branch/tag.
    top      - Target VCS branch/tag.
    i        - Version \"In progress\" status.
    v        - Version name.
    debug    - Debug mode.
STR;
        //@finishSkipCommitHooks
    }
}
