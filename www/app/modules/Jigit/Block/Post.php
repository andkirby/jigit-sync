<?php
namespace Jigit\Block;

use App\Block\Json;
use Jigit\Jira\Issue;
use Jigit\Report;
use Jigit\Run;

/**
 * Class Post
 *
 * @package Jigit\Block
 */
class Post extends Json
{
    /**
     * Get data which need to be convert to JSON
     *
     * @return array|string
     */
    protected function _getDataToJson()
    {
        $output = array();
        $report = $this->_processAction();

        //TODO refactor getting such keys
        $output['vcs_issues'] = $this->_getVscIssues();

        foreach ($report->getJqlHelpers() as $name => $helper) {
            if ($helper->hasFound()) {
                $helper->getJqlTypes();
                foreach ($helper->getResult() as $type => $issues) {
                    $jql = $helper->getJql($type);

                    $output['result'][$type]['message']    = $jql['message'];
                    $output['result'][$type]['issue_keys'] = array_keys($issues);
                    /** @var Issue $issue */
                    foreach ($issues as $key => $issue) {
                        $output['result'][$type]['issues'][$key] = array(
                            'key'            => $issue->getKey(),
                            'fixVersion'     => $issue->getFixVersionsNames(),
                            'affectsVersion' => $issue->getAffectsVersionsNames(),
                            'sprints'        => $issue->getSprintsSimple(true),
                            'authors'        => $issue->getAuthors($report->getVcs()->getCommits()),
                            'status'         => $issue->getStatusName(),
                            'type'           => $issue->getTypeName(),
                        );
                    }
                }
            }
        }
        return $output;
    }

    /**
     * Process action
     *
     * @return Report
     * @throws \Jigit\Exception
     */
    protected function _processAction()
    {
        $report = new Report();
        return $this->_getRunner()->processAction($report);
    }

    /**
     * Get runner
     *
     * @return Run
     * @throws \Zend_Exception
     */
    protected function _getRunner()
    {
        return \Zend_Registry::get('runner');
    }

    /**
     * Get VCS issues
     *
     * @return array|mixed
     * @throws \Zend_Exception
     */
    protected function _getVscIssues()
    {
        return \Zend_Registry::isRegistered('vcs_keys') ? \Zend_Registry::get('vcs_keys') : array();
    }
}
