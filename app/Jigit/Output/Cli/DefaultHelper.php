<?php
namespace Jigit\Output\Cli;

use Jigit\Config;
use Jigit\Git;
use Jigit\Jira\Api;
use Jigit\Jira\Issue;
use Jigit\Jira\IssueHelper;
use Jigit\Output;

/**
 * Class DefaultHelper
 *
 * @package Jigit\Output\Cli
 */
class DefaultHelper
{
    /**
     * VCS model
     *
     * @var Git
     */
    protected $_vcs;

    /**
     * JQL helper
     *
     * @var \Jigit\Jira\Jql\Helper\DefaultHelper
     */
    protected $_helper;

    /**
     * Process result
     *
     * @var array
     */
    protected $_result = array();

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Get JQL helper
     *
     * @return \Jigit\Jira\Jql\Helper\DefaultHelper
     */
    public function getHelper()
    {
        return $this->_helper;
    }

    /**
     * Set JQL helper
     *
     * @param \Jigit\Jira\Jql\Helper\DefaultHelper $helper
     * @return $this
     */
    public function setHelper($helper)
    {
        $this->_helper = $helper;
        return $this;
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
     * Get result
     *
     * @return array
     */
    public function getResult()
    {
        return $this->_result;
    }

    /**
     * Set result
     *
     * @param array $result
     * @return $this
     */
    public function setResult($result)
    {
        $this->_result = $result;
        return $this;
    }

    /**
     * Add issue into output
     *
     * @param Output $output
     * @param string $jqlType
     * @param Issue  $issue
     * @return $this
     */
    protected function _addIssueIntoOutput(Output $output, $jqlType, $issue)
    {
        if (!$this->_isLineSimpleView()) {
            $output->addDelimiter();
        }
        $output->add($this->_getIssueContentBlock($jqlType, $issue));
        return $this;
    }

    /**
     * Check line simple view
     *
     * @return bool
     */
    protected function _isLineSimpleView()
    {
        return 'line' === Config\Jira::getIssueViewSimple();
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
        $jql = $this->getHelper()->getJql($jqlType);
        $output->add($jql['message']);
        $output->disableDecorator();
        return $this;
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
     * Add result output
     *
     * @param Output $output
     * @return $this
     */
    public function addOutput($output)
    {
        foreach (array_keys($this->_result) as $type) {
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
                $this->_addIssueIntoOutput($output, $jqlType, $issue);
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
        if ($this->_isLineSimpleView()) {
            $strIssue = "{$issue->getKey()}";
        } elseif (Config\Jira::getIssueViewSimple()) {
            $strIssue = "{$issue->getKey()}: {$issue->getSummary()}";
        } else {
            $authors = $this->_getIssueHelper()->getIssueAuthors(
                $issue, $this->getVcs()->getCommits()
            );
            $authors = implode(', ', $authors);
            $issueHelper       = $this->_getIssueHelper();
            $sprint            = $issueHelper->getIssueSprint($issue);
            $status            = $issueHelper->getIssueStatus($issue);
            $type              = $issueHelper->getIssueType($issue);
            $affectedVersions  = implode(', ', $issueHelper->getIssueAffectsVersions($issue));
            $fixVersionsString = implode(', ', $issueHelper->getIssueFixVersions($issue));

//@startSkipCommitHooks
            $strIssue          = <<<STR
{$issue->getKey()}: {$issue->getSummary()}
Type:              {$type}
AffectedVersion/s: {$affectedVersions}
FixVersion/s:      {$fixVersionsString}
Status:            {$status}
Sprint:            {$sprint}
Author/s:          {$authors}
STR;
//@finishSkipCommitHooks
        }
        return $strIssue;
    }
}
