<?php
namespace Jigit\Output;

use Jigit\Config;
use Jigit\Git;
use Jigit\Jira;
use Jigit\Output;
use Jigit\UserException;

/**
 * Class Cli
 *
 * CLI output class
 *
 * @package Jigit\Output
 */
class Cli
{
    /**
     * Output
     *
     * @var Output
     */
    protected $_output;

    /**
     * Constructor
     *
     * @param Output $output
     */
    public function __construct(Output $output)
    {
        $this->_output = $output;
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
     * Process helpers with result
     *
     * @param array $resultHelpers
     * @param Git   $vcs
     * @return $this
     */
    public function processResult($resultHelpers, Git $vcs)
    {
        $this->_setHeaderOutput();
        $this->_addVcsKeysOutput();

        /** @var Jira\Jql\Helper\Standard $helper */
        foreach ($resultHelpers as $name => $helper) {
            if ($helper->hasFound()) {
                $resultHelpers[$name] = $helper;
                $outputHelper = $this->_getOutputHelper($name);
                $outputHelper->setHelper($helper)
                    ->setResult($helper->getResult())
                    ->setVcs($vcs);
                $outputHelper->addOutput($this->getOutput());
            }
        }
        if (!$resultHelpers) {
            $this->getOutput()->enableDecorator();
            $this->getOutput()->add('SUCCESS! Everything is OK');
            $this->getOutput()->disableDecorator();
        }
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
     * Get help string
     *
     * @return string
     */
    public function getCliHelp()
    {
        return file_get_contents(JIGIT_ROOT . '/config/cli-manual.txt');
    }

    /**
     * Add found VCS keys into output
     *
     * @return $this
     * @throws \Zend_Exception
     */
    protected function _addVcsKeysOutput()
    {
        if (\Zend_Registry::isRegistered('vcs_keys')) {
            $this->getOutput()->add('Found issues in VCS:');
            $this->getOutput()->add(implode(', ', \Zend_Registry::get('vcs_keys')));
        }
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
     * Add help into output
     *
     * @return $this
     */
    public function addHelp()
    {
        $str = $this->getCliHelp();
        $this->getOutput()->add($str);
        return $this;
    }

    /**
     * Get output helper
     *
     * @param string $name
     * @return Output\Cli\Standard
     */
    protected function _getOutputHelper($name)
    {
        $className = '\Jigit\Output\Cli\\' . $name;
        /** @var Output\Cli\Standard $helper */
        return new $className();
    }

    /**
     * Add exception into output
     *
     * @param \Exception $e
     */
    public function addException(\Exception $e)
    {
        if ($e instanceof UserException) {
            if ($e->getCode() == 911) {
                $this->addHelp();
            } else {
                $this->addHelp();
                $this->getOutput()->add('ERROR: ' . $e->getMessage());
            }
        } else {
            $this->getOutput()->add('SYSTEM ERROR: ' . $e->getMessage());
            $this->getOutput()->add('TRACE: ' . PHP_EOL . $e->getTraceAsString());
        }
    }
}
