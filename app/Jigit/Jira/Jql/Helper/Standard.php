<?php
namespace Jigit\Jira\Jql\Helper;

use Jigit\Config;
use Jigit\Exception;
use Jigit\Git;
use Jigit\Jira\Api;
use Jigit\Jira\Issue;
use Jigit\Jira\IssueHelper;

/**
 * Class Standard
 *
 * @package Jigit\Jira\Jql\Helper
 */
class Standard
{
    /**
     * Process result
     *
     * It should has format: _result[jql_type][issue_key] = new Issue()
     *
     * @var array
     */
    protected $_result = array();

    /**
     * JQL item info
     *
     * @var array
     */
    protected $_jqlItems;

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
     * Constructor
     */
    public function __construct()
    {
    }

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
            throw new Exception('Empty JQL type.');
        }
        $type = $jql['type'];
        if (empty($jql['message'])) {
            throw new Exception("Empty message in JQL type '$type'.");
        }
        if (empty($jql['jql'])) {
            throw new Exception("Empty JQL in JQL type '$type'.");
        }
        if (!isset($jql['in_progress'])) {
            throw new Exception("Empty 'in_progress' key in JQL type '$type'.");
        }
        $this->_jqlItems[$type] = $jql;
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
        return (int)Config\Project::getJiraTargetFixVersionInProgress() === (int)$this->_jqlItems[$type]['in_progress']
            || '-1' == $this->_jqlItems[$type]['in_progress'];
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
        $fields = $fields ?: $this->_getDefaultApiIssueFields();
        /** @var Api\Result $result */
        try {
            $result = $this->getApi()->search($this->_jqlItems[$type]['jql'], $offset, $max, $fields);
        } catch (Api\Exception $e) {
            $this->_processApiErrors($e, $type);
        }
        return $result;
    }

    /**
     * Process JIRA API errors
     *
     * @param Api\Exception $e
     * @param string     $type
     * @throws Api\Exception
     * @return $this
     */
    protected function _processApiErrors($e, $type)
    {
        throw new Api\Exception(
            "Error in JQL type: $type" . PHP_EOL . $e->getMessage()
        );
    }

    /**
     * Get default API issue fields
     *
     * @return string
     */
    protected function _getDefaultApiIssueFields()
    {
        return Config\Jira::getApiIssueFields();
    }

    /**
     * Get result issues list
     *
     * It should has format: _result[jql_type][issue_key] = new Issue()
     *
     * @return array
     */
    public function getResult()
    {
        return $this->_result;
    }

    /**
     * Get all JQL types
     *
     * @return array
     */
    public function getJqlTypes()
    {
        return array_keys($this->_jqlItems);
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
     * Get JQL item
     *
     * @param string $type
     * @return array|null
     */
    public function getJql($type)
    {
        return isset($this->_jqlItems[$type]) ? $this->_jqlItems[$type] : null;
    }
}
