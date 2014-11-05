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
     * Process request
     *
     * @param string $type JQL type
     * @return $this
     */
    public function process($type)
    {
        /** @var Api\Result $result */
        $result = $this->_queryJql($type);
        if (!$result->getTotal()) {
            return $this;
        }

        /** @var Issue $issue */
        foreach ($result->getIssues() as $issue) {
            if ($this->canHandleIssue($type, $issue)) {
                $this->handleIssue($type, $issue);
            }
        }
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
        return (int)Config\Project::getJiraTargetFixVersionInProgress() === (int)$this->_jql[$type]['in_progress']
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
        if (!empty($this->_result[$jqlType])) {
            $this->_addOutputHeader($output, $jqlType);
            $output->add('Keys: ' . implode(', ', array_keys($this->_result[$jqlType])));
            foreach ($this->_result[$jqlType] as $issue) {
                $output->add($this->_getIssueContentBlock($jqlType, $issue));
                $output->addDelimiter();
            }
        }
        $this->_postOutput($output, $jqlType);
        return $this;
    }

    /**
     * Add post output
     *
     * @param Output $output
     * @param string $jqlType
     * @return $this
     */
    protected function _postOutput(Output $output, $jqlType)
    {
        return $this;
    }

    /**
     * Get issue content block
     *
     * @param string $jqlType
     * @param Issue  $issue
     * @return array
     */
    protected function _getIssueContentBlock($jqlType, $issue)
    {
        $authors = $this->_getIssueHelper()->getAuthorsByJqlType(
            $this->_jql[$jqlType], $issue,
            $this->getApi(), $this->getVcs()
        );
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

    /**
     * Add output header
     *
     * @param Output $output
     * @param string $jqlType
     * @return $this
     */
    protected function _addOutputHeader(Output $output, $jqlType)
    {
        $output->enableDecorator();
        $output->add($this->_jql[$jqlType]['message']);
        $output->disableDecorator();
        return $this;
    }

    /**
     * Query JQL
     *
     * @param string $type
     * @param int    $max
     * @param int    $offset
     * @param string $fields
     * @return Api\Result
     */
    protected function _queryJql($type, $max = 20, $offset = 0, $fields = '')
    {
        $fields = $fields ?: '*navigable';
        return $this->getApi()->search($this->_jql[$type]['jql'], $max, $offset, $fields);
    }
}
