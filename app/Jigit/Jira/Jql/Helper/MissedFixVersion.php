<?php
namespace Jigit\Jira\Jql\Helper;

use Jigit\Config;
use Jigit\Jira\Api;
use Jigit\Jira\Issue;
use Jigit\Jira\Jql;
use Lib\Exception;

/**
 * Class WithoutFixVersion
 *
 * @package Jigit\Jira\Jql\Helper
 */
class MissedFixVersion extends DefaultHelper
{
    /**
     * Default issues max count for JIRA API
     */
    const ISSUES_COUNT = 300;

    /**
     * Type name of issues which don't affect a code
     */
    const TYPE_ISSUES_NOT_IN_CODE    = 'issuesNotInCode';

    /**
     * Missed issue versions
     *
     * @var array
     */
    protected $_missedIssueVersions;

    /**
     * Add extra type
     */
    public function __construct()
    {
        parent::__construct();
        $this->setJql(
            array(
                'type'          => self::TYPE_ISSUES_NOT_IN_CODE,
                'message'       => 'Following issues should get version(s)',
                'jql'           => ' ',
                'in_progress'   => '-1',
            )
        );
    }

    /**
     * Handle issue
     *
     * @param string $type
     * @param Issue  $issue
     * @param array  $versions
     * @return $this
     */
    public function handleIssue($type, Issue $issue, $versions = array())
    {
        if ($versions) {
            parent::handleIssue($type, $issue);
            $this->_missedIssueVersions[$type][$issue->getKey()] = $versions;
        } else {
            parent::handleIssue(self::TYPE_ISSUES_NOT_IN_CODE, $issue);
        }
        return $this;
    }

    /**
     * Check fix version added properly
     *
     * @param string $type
     * @throws Exception
     * @return $this
     */
    public function process($type)
    {
        $result = $this->_queryJql($type, self::ISSUES_COUNT);
        $issues = $this->_getIssues($result);

        $issueKeys = array_keys($issues);
        if (!$issueKeys) {
            return $this;
        }

        $vcsIssues = $this->_findIssuesInVcs($issueKeys);
        $issuesNotInCode[$type] = $issues;
        $issueKeyIdsInTags = array();
        foreach ($vcsIssues as $tag => $ids) {
            foreach ($ids as $id) {
                $issueKeyIdsInTags[$id][] = $tag;
                unset($issuesNotInCode[$type][$id]);
            }
        }
        if ($issueKeyIdsInTags) {
            //check exists fixVersion
            foreach ($issueKeyIdsInTags as $id => $versions) {
                $this->handleIssue(
                    $type, $issues[$id],
                    $this->_getIssueMissedVersions($issues[$id], $versions)
                );
            }
        }
        if ($issuesNotInCode) {
            //add issues not in the code
            foreach ($issuesNotInCode as $id => $issue) {
                $this->handleIssue($type, $issues[$id]);
            }
        }
        return $this;
    }

    /**
     * Skip checking ability to process JQL
     *
     * @param string $type
     * @return bool
     */
    public function canProcessJql($type)
    {
        return true;
    }

    /**
     * Get required issue version
     *
     * @param string $jqlType
     * @param Issue  $issue
     * @return array
     */
    public function getRequiredIssueVersions($jqlType, $issue)
    {
        $versions = $this->_missedIssueVersions[$jqlType][$issue->getKey()];
        $requiredVersions = array(
            'fix'    => array(array_pop($versions)),
            'affect' => array(),
        );
        if ($versions) {
            $requiredVersions['affect'] = $versions;
        }
        return $versions;
    }

    /**
     * Get issue keys
     *
     * @param Api\Result $result
     * @return array
     */
    protected function _getIssues($result)
    {
        $issueKeys = array();
        if ($result->getIssuesCount()) {
            /** @var Issue $issue */
            foreach ($result->getIssues() as $issue) {
                $issueKeys[$issue->getKey()] = $issue;
            }
        }
        return $issueKeys;
    }

    /**
     * Get VCS tags
     *
     * @return array
     */
    protected function _getVcsTags()
    {
        $tags   = $this->getVcs()->getTags();
        //add default branches to identify tasks there
        $tags[] = 'master';
        $tags[] = 'develop';
        return $tags;
    }

    /**
     * Find issues in VCS
     *
     * @param array $issueKeys
     * @return array
     */
    protected function _findIssuesInVcs($issueKeys)
    {
        $vcsIssues         = array();
        $tags              = $this->_getVcsTags();
        $prevTag           = array_shift($tags);
        $issuesRegular     = implode('[^0-9]|', $issueKeys) . '[^0-9]';
        foreach ($tags as $tag) {
            Config::addDebug("Find issues between: $prevTag..$tag");
            $log = $this->getVcs()->getLog(
                $prevTag, $tag, $this->getVcs()->getLogFormat(), "--reverse -E --grep=\"($issuesRegular)\""
            );
            $issues = $this->getVcs()->aggregateCommitsByLog($log);
            if ($issues) {
                $vcsIssues[$tag] = array_keys($issues);
                Config::addDebug(
                    "Result GIT searching for tag '$tag': "
                    . PHP_EOL . implode(', ', $vcsIssues[$tag])
                );
            }
            $prevTag = $tag;
        }
        return $vcsIssues;
    }

    /**
     * Get missed issue versions
     *
     * @param Issue $issue
     * @param array $versions
     * @return array
     */
    protected function _getIssueMissedVersions($issue, $versions)
    {
        $issueHelper     = $this->_getIssueHelper();
        $affectsVersions = $issueHelper->getIssueAffectsVersions($issue);
        $fixVersions     = $issueHelper->getIssueAffectsVersions($issue);
        foreach ($versions as $v => $version) {
            if (in_array($version, $affectsVersions)
                || in_array($version, $fixVersions)
            ) {
                //TODO check versions from parent
                unset($versions[$v]);
            }
        }
        return $versions;
    }
}
