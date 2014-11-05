<?php
namespace Jigit\Jira\Jql\Helper;

use chobie\Jira\Api;
use chobie\Jira\Issue;
use Jigit\Config;
use Jigit\Jira\Jql;
use Jigit\Output;

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
     * Issues which not in code
     *
     * @var Issue[][]
     */
    protected $_issuesNotInCode;

    /**
     * Missed issue versions
     *
     * @var array
     */
    protected $_missedIssueVersions;

    /**
     * Issues in different branch
     *
     * @var array
     */
    protected $_inDifferentBranch = array();

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
            unset($this->_issuesNotInCode[$type][$issue->getKey()]);
        }
        return $this;
    }

    /**
     * Check fix version added properly
     *
     * @param string $type
     * @throws \Jigit\Exception
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
        $this->_issuesNotInCode[$type] = $issues;
        $issueKeyIdsInTags = array();
        foreach ($vcsIssues as $tag => $ids) {
            foreach ($ids as $id) {
                $issueKeyIdsInTags[$id][] = $tag;
                unset($this->_issuesNotInCode[$type][$id]);
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
     * Add additional output header
     *
     * @param Output $output
     * @param string $jqlType
     * @return $this
     */
    protected function _addOutputHeader(Output $output, $jqlType)
    {
        parent::_addOutputHeader($output, $jqlType);
        $output->add('Following issues should get version(s):');
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
        $versions = $this->_missedIssueVersions[$jqlType][$issue->getKey()];
        if ($this->_isLineSimpleView()) {
            //return "line" issue view
            $versions = implode(', ', $versions);
            return parent::_getIssueContentBlock($jqlType, $issue) . ": $versions";
        }

        $strIssue = parent::_getIssueContentBlock($jqlType, $issue);
        $requiredFixVersion = array_pop($versions);
        $strIssue .= PHP_EOL . "REQUIRED FixVersion:          {$requiredFixVersion}";
        if ($versions) {
            $versions = implode(', ', $versions);
            $strIssue .= PHP_EOL . "REQUIRED AffectsVersion/s:{$versions}";
        }
        return $strIssue;
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
        //add report about not exists issues
        if (!empty($this->_issuesNotInCode[$jqlType])) {
            $output->addDelimiter();
            $output->add('Following issues were not found in VCS:');
            $keys = implode(', ', array_keys($this->_issuesNotInCode[$jqlType]));
            $output->add($keys);
        }
        return $this;
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
        $issueKeyIdsString = implode('|', $issueKeys);
        $project           = Config\Project::getJiraProject();
        foreach ($tags as $tag) {
            Config::addDebug("Find issues between: $prevTag..$tag");
            $result = $this->getVcs()->runInProjectDir(
                "log $prevTag..$tag --no-merges --reverse --oneline | grep -E \"($issueKeyIdsString)\""
            );
            if ($result) {
                preg_match_all('/\s(' . $project . '-\d+)/', $result, $keys);
                $vcsIssues[$tag] = array_unique($keys[1]);
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
