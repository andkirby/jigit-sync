<?php
namespace Jigit\Jira\Jql\Helper;

use chobie\Jira\Api;
use chobie\Jira\Issue;
use Jigit\Config;
use Jigit\Exception;
use Jigit\Git;
use Jigit\Jira\IssueHelper;
use Jigit\Output;

/**
 * Class DefaultHelper
 *
 * @package Jigit\Jira\Jql\Helper
 */
class DefaultHelper
{
    /**
     * Process result
     *
     * @var array
     */
    protected $_result = array();

    /**
     * JQL item info
     *
     * @var array
     */
    protected $_jql;

    /**
     * JQL item info
     *
     * @var Git
     */
    protected $_vcs;

    /**
     * JIRA API model
     *
     * @var Api
     */
    protected $_api;

    /**
     * Get VCS
     *
     * @return Git
     */
    public function getVcs()
    {
        return $this->_vcs;
    }

    /**
     * Set VCS
     *
     * @param Git $vcs
     * @return $this
     */
    public function setVcs($vcs)
    {
        $this->_vcs = $vcs;
        return $this;
    }

    /**
     * Get JIRA API model
     *
     * @return Api
     */
    public function getApi()
    {
        return $this->_api;
    }

    /**
     * Set JIRA API model
     *
     * @param Api $api
     * @return $this
     */
    public function setApi($api)
    {
        $this->_api = $api;
        return $this;
    }

    /**
     * Set jql
     *
     * @param array $jql
     * @throws Exception
     * @return $this
     */
    public function setJql($jql)
    {
        if (empty($jql['type'])) {
            throw new Exception('Unknown JQL type.');
        }
        $type = $jql['type'];
        if (empty($jql['message'])) {
            throw new Exception("Empty message in JQL type '$type'.");
        }
        if (empty($jql['jql'])) {
            throw new Exception("Empty JQL in JQL type '$type'.");
        }
        if (!isset($jql['jql'])) {
            throw new Exception("Empty 'in_progress' key in JQL type '$type'.");
        }
        $this->_jql[$type] = $jql;
        return $this;
    }

    /**
     * Handle issue
     *
     * @param string $type
     * @param Issue  $issue
     * @return $this
     */
    public function handleIssue($type, Issue $issue)
    {
        $this->_result[$type][$issue->getKey()] = $issue;
        return $this;
    }

    /**
     * Check ability to handle issue
     *
     * @param string $type
     * @param Issue  $issue
     * @return $this
     */
    public function canHandleIssue($type, Issue $issue)
    {
        //Skip build notes issue
        return false === strpos($issue->getSummary(), 'Build ' . Config\Project::getJiraTargetFixVersion());
    }

    /**
     * Can process JQL
     *
     * @param string $type JQL type
     * @return bool
     * @todo Update to using "released" status in JQL
     */
    public function canProcessJql($type)
    {
        return (int)Config\Project::getJiraTargetFixVersionInProgress() !== (int)$this->_jql[$type]['in_progress']
            || '-1' == $this->_jql[$type]['in_progress'];
    }

    /**
     * Check found issues
     *
     * @return bool
     */
    public function hasFound()
    {
        return (bool)$this->_result;
    }

    /**
     * Add result output
     *
     * @param Output $output
     * @return $this
     */
    public function addOutput($output)
    {
        foreach (array_keys($this->_jql) as $type) {
            $this->_addJqlOutput($output, $type);
        }
        return $this;
    }

    /**
     * Add JQL output
     *
     * @param Output $output
     * @param string $jqlType
     * @internal param array $jql
     * @return $this
     */
    protected function _addJqlOutput(Output $output, $jqlType)
    {
        if (empty($this->_result[$jqlType])) {
            return $this;
        }

        $output->enableDecorator();
        $output->add($this->_jql[$jqlType]['message']);
        $output->disableDecorator();

        $output->add('Keys: ' . implode(', ', array_keys($this->_result[$jqlType])));

        foreach ($this->_result[$jqlType] as $issue) {
            $authors = $this->_getIssueHelper()->getAuthorsByJqlType(
                $this->_jql[$jqlType], $issue,
                $this->getApi(), $this->getVcs()
            );
            $output->add($this->_getIssueContentBlock($issue, $authors));
        }
        return $this;
    }

    /**
     * Get issue content block
     *
     * @param Issue $issue
     * @param array $authors
     * @return array
     */
    protected function _getIssueContentBlock($issue, $authors)
    {
        $issueHelper       = $this->_getIssueHelper();
        $sprint            = $issueHelper->getIssueSprint($issue);
        $status            = $issueHelper->getIssueStatus($issue);
        $type              = $issueHelper->getIssueType($issue);
        $affectedVersions  = implode(', ', $issueHelper->getIssueAffectsVersions($issue));
        $fixVersionsString = implode(', ', $issueHelper->getIssueFixVersions($issue));
        $strIssue          = array();
        $strIssue[]        = "Key:               {$issue->getKey()}: {$issue->getSummary()}";
        $strIssue[]        = "Type:              {$type}";
        $strIssue[]        = "AffectedVersion/s: {$affectedVersions}";
        $strIssue[]        = "FixVersion/s:      {$fixVersionsString}";
        $strIssue[]        = "Status:            {$status}";
        $strIssue[]        = "Sprint:            {$sprint}";
        $strIssue[]        = "Author/s:          {$authors}";
        return $strIssue;
    }

    /**
     * Get issue helper
     *
     * @return IssueHelper
     */
    protected function _getIssueHelper()
    {
        return new IssueHelper();
    }
}
