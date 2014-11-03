<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 8/15/2014
 * Time: 3:11 AM
 */

namespace Jigit;

use \Jigit\Config;
use \chobie\Jira as Jira;
use \Jigit\Jira as JigitJira;
use Jigit\Dispatcher\InterfaceDispatcher;

/**
 * Class Report
 *
 * @package Jigit
 */
class Report
{
    /**
     * Dispatcher
     *
     * @var InterfaceDispatcher
     */
    protected $_dispatcher;

    /**
     * Make report
     *
     * @param Jira\Api         $api
     * @param Vcs\InterfaceVcs $vcs
     * @param array            $jqlList
     */
    public function make(Jira\Api $api, Vcs\InterfaceVcs $vcs, array $jqlList)
    {
        /**
         * Show found issues
         */
        $inDifferentBranch = array();
        $found = false;
        foreach ($jqlList as $type => $jqlItem) {
            $jqlItem['type'] = $type;
            $this->_debugJqlItem($jqlItem);

            $jql = $jqlItem['jql'];

            //todo Update to using "released" status in JQL
            if (Config\Project::getJiraTargetFixVersionInProgress() && '1' !== $jqlItem['in_progress']
                || !Config\Project::getJiraTargetFixVersionInProgress() && '0' !== $jqlItem['in_progress']
            ) {
                continue;
            }
            $showKeys = array();
            /** @var Jira\Api\Result $result */
            $result = $api->search($jql);

            if (!$result->getTotal()) {
                continue;
            }

            $added = false;
            $toOutput = array();
            /** @var Jira\Issue $issue */
            foreach ($result->getIssues() as $issue) {
                //Skip build notes issue
                if (false !== strpos($issue->getSummary(), 'Build ' . Config\Project::getJiraTargetFixVersion())) {
                    continue;
                }

                /**
                 * Identify issues which not in required branch
                 */
                if (JigitJira\Jql::TYPE_NOT_AFFECTS_CODE == $jqlItem['type']) {
                    if ($this->_isIssueInAnotherBranch($issue)) {
                        $inDifferentBranch[] = $issue->getKey();
                        continue;
                    }
                }

                //check fix version right?
                if ($jqlItem['type'] === JigitJira\Jql::TYPE_WITHOUT_FIX_VERSION
                    && $this->_isIssueFixVersionProper($issue)
                ) {
                    continue;
                }

                $showKeys[] = $issue->getKey();
                $authors = $this->_getAuthorsByJqlType($jqlItem, $issue, $api, $vcs);
                $toOutput[] = $this->_getIssueContentBlock($issue, $authors);
                $added = true;
            }

            if (!$added) {
                continue;
            }
            $this->_getOutput()->enableDecorator();
            $this->_getOutput()->add($jqlItem['message']);
            $this->_getOutput()->disableDecorator();
            $keys = JigitJira\KeysFormatter::format(implode(', ', $showKeys));
            $this->_getOutput()->add('Keys: ' . $keys);

            foreach ($toOutput as $outputItem) {
                $this->_getOutput()->addDelimiter();
                $this->_getOutput()->add($outputItem);
            }
            $found = true;
        }
        if ($inDifferentBranch) {
            $this->_getOutput()->enableDecorator();
            $this->_getOutput()->add('WARNING!!! Issues committed in a different branch');
            $this->_getOutput()->disableDecorator();
            $keys = JigitJira\KeysFormatter::format(implode(', ', $inDifferentBranch));
            $this->_getOutput()->add('Keys: ' . $keys);
            $found = true;
        }

        if (!$found) {
            $this->_getOutput()->enableDecorator();
            $this->_getOutput()->add('SUCCESS! Everything is OK');
            $this->_getOutput()->disableDecorator();
        }
    }

    /**
     * Make report of issues which should get some fixVersion.
     *
     * @param Jira\Api $api
     * @param Git      $vcs
     * @param array    $jqlList
     */
    public function makePushReport(Jira\Api $api, Git $vcs, array $jqlList)
    {
        Config::addDebug('Found tags: ' . PHP_EOL . implode(', ', $this->_getVcsTags($vcs)));
        foreach ($jqlList as $type => $jqlItem) {
            $jqlItem['type'] = $type;
            $this->_getOutput()->enableDecorator();
            $this->_getOutput()->add($jqlItem['message']);
            $this->_getOutput()->disableDecorator();
            $this->_debugJqlItem($jqlItem);

            $issueKeys = $this->_getIssueKeys($api, $jqlItem);
            if ($issueKeys) {
                $keys = JigitJira\KeysFormatter::format(implode(', ', $issueKeys));
                $this->_getOutput()->add('Found JIRA issues: ' . PHP_EOL . $keys);
            } else {
                $this->_getOutput()->add('Found JIRA issues: -');
                continue;
            }

            $vcsIssues = $this->_findIssuesInVcs($vcs, $issueKeys, $keys);

            $issueKeyIdsInTags = array();
            foreach ($vcsIssues as $tag => $ids) {
                foreach ($ids as $id) {
                    $issueKeyIdsInTags[$id][] = $tag;
                    unset($issueKeys[$id]);
                }
            }

            if ($issueKeyIdsInTags) {
                $this->_getOutput()->addDelimiter();
                $this->_getOutput()->add('Following issues should get fix version(s):');
                $project = Config\Project::getJiraProject();
                foreach ($issueKeyIdsInTags as $issueKeyId => $versions) {
                    $this->_getOutput()->add("$project-$issueKeyId: " . implode(', ', $versions));
                }
            }
            if ($issueKeys) {
                $this->_getOutput()->addDelimiter();
                $this->_getOutput()->add('Following issues were not found in VCS:');
                $keys = JigitJira\KeysFormatter::format(implode(', ', $issueKeys));
                $this->_getOutput()->add($keys);
            }
        }
    }

    /**
     * Get output model
     *
     * @return Output
     */
    protected function _getOutput()
    {
        return Config::getInstance()->getData('output');
    }

    /**
     * Show JQL info on debug mode
     *
     * @param array $jqlItem
     * @return $this
     */
    protected function _debugJqlItem(array $jqlItem)
    {
        Config::addDebug("JQL: {$jqlItem['type']}: \n{$jqlItem['jql']}");
        return $this;
    }

    /**
     * Try to find issue in another branch
     *
     * @param Jira\Issue $issue
     * @return array
     */
    protected function _isIssueInAnotherBranch($issue)
    {
        $key    = $issue->getKey();
        $helper = $this->getDispatcher()->getVcs()->getHelper('IssueInBranches');
        return $helper->process($key);
    }

    /**
     * Get Dispatcher
     *
     * @return InterfaceDispatcher
     */
    public function getDispatcher()
    {
        return $this->_dispatcher;
    }

    /**
     * Set Dispatcher
     *
     * @param InterfaceDispatcher $dispatcher
     * @return $this
     */
    public function setDispatcher(InterfaceDispatcher $dispatcher)
    {
        $this->_dispatcher = $dispatcher;
        return $this;
    }

    /**
     * Get issue keys
     *
     * @param Jira\Api $api
     * @param array    $jqlItem
     * @return array
     */
    protected function _getIssueKeys(Jira\Api $api, $jqlItem)
    {
        $issueKeys = array();
        /** @var Jira\Api\Result $result */
        $result = $api->search($jqlItem['jql'], 0, 300, 'issuekey, fixVersion');
        if ($result->getIssuesCount()) {
            /** @var Jira\Issue $issue */
            foreach ($result->getIssues() as $issue) {
                list(, $id) = explode('-', $issue->getKey());
                $issueKeys[$id] = $issue->getKey();
            }
        }
        return $issueKeys;
    }

    /**
     * Get VCS tags
     *
     * @param Git $vcs
     * @return array
     */
    protected function _getVcsTags(Git $vcs)
    {
        $tags   = $vcs->getTags();
        //add default branches to identify tasks there
        $tags[] = 'master';
        $tags[] = 'develop';
        return $tags;
    }

    /**
     * Find issues in VCS
     *
     * @param Git $vcs
     * @param array $issueKeys
     * @param array $keys
     * @return array
     */
    protected function _findIssuesInVcs(Git $vcs, $issueKeys, $keys)
    {
        $vcsIssues         = array();
        $tags              = $this->_getVcsTags($vcs);
        $prevTag           = array_shift($tags);
        $issueKeyIdsString = implode('|', array_keys($issueKeys));
        $project           = Config\Project::getJiraProject();
        foreach ($tags as $tag) {
            Config::addDebug("Find issues between: $prevTag..$tag");
            $result = $vcs->runInProjectDir(
                "log $prevTag..$tag --no-merges --reverse --oneline | grep -E \"$project-($issueKeyIdsString)\""
            );
            if ($result) {
                preg_match_all('/\s' . $project . '-(\d+)/', $result, $keys);
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
     * Get Affects Version/s of issue
     *
     * @param Jira\Issue $issue
     * @return array
     */
    protected function _getIssueAffectsVersions($issue)
    {
        $affectedVersions = array();
        foreach ($issue->get('Affects Version/s') as $fix) {
            $affectedVersions[] = $fix['name'];
        }
        return $affectedVersions;
    }

    /**
     * Get Fix Version/s of issue
     *
     * @param Jira\Issue $issue
     * @return array
     */
    protected function _getIssueFixVersions($issue)
    {
        $affectedVersions = array();
        foreach ($issue->getFixVersions() as $fix) {
            $affectedVersions[] = $fix['name'];
        }
        return $affectedVersions;
    }

    /**
     * Check fix version added properly
     *
     * @param Jira\Issue $issue
     * @return array
     */
    protected function _isIssueFixVersionProper($issue)
    {
        $fixVersions      = $this->_getIssueFixVersions($issue);
        $affectedVersions = $this->_getIssueAffectsVersions($issue);
        foreach ($affectedVersions as $affVer) {
            $affVer = ltrim($affVer, 'v');
            foreach ($fixVersions as $fixVer) {
                $fixVer = ltrim($fixVer, 'v');
                if ($affVer == trim(Config\Project::getJiraTargetFixVersion(), 'v')
                    && version_compare($fixVer, $affVer, '>')
                ) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get issue sprints
     *
     * @param Jira\Issue $issue
     * @return string
     */
    protected function _getIssueSprint($issue)
    {
        $sprint = $issue->get('Sprint');
        if ($sprint) {
            $matches = array();
            preg_match_all('/name\=([^,]+),.*?id\=([0-9]+)/', $sprint[0], $matches);
            $sprint = '';
            if ($matches[2]) {
                //get sprint ID
                $sprint .= $matches[2][0];
            } else {
                $sprint .= '0';
            }
            if ($matches[1]) {
                //get sprint name
                $sprint .= ', ' . $matches[1][0];
                return $sprint;
            }
            return $sprint;
        }
        return $sprint;
    }

    /**
     * Get issue status
     *
     * @param Jira\Issue $issue
     * @return string
     */
    protected function _getIssueStatus($issue)
    {
        $status = $issue->getStatus();
        $status = $status['name'];
        return $status;
    }

    /**
     * Get issue type
     *
     * @param Jira\Issue $issue
     * @return mixed
     */
    protected function _getIssueType($issue)
    {
        $type = $issue->getIssueType();
        $type = $type['name'];
        return $type;
    }

    /**
     * Get issue sprints
     *
     * @param Jira\Issue $issue
     * @param Jira\Api   $api
     * @param Jira\Issue $issue
     * @param array      $gitKeys
     * @return array
     */
    protected function _getIssueAuthors(Jira\Api $api, Jira\Issue $issue, $gitKeys)
    {
        $authors = $this->_getIssueVcsAuthors($issue, $gitKeys);
        if (!$authors) {
            $authors = $this->_getIssueChangeLogAuthors($api, $issue);
        }
        if (!$authors) {
            $assignee = $this->_getIssueAssignee($issue);
            $authors  = $assignee;
        }
        return $authors;
    }

    /**
     * Get issue VCS authors
     *
     * @param Jira\Issue $issue
     * @param array      $gitKeys
     * @return string
     */
    protected function _getIssueVcsAuthors(Jira\Issue $issue, $gitKeys)
    {
        $authors = array();
        if ($gitKeys && isset($gitKeys[$issue->getKey()])) {
            $authors = implode(', ', array_keys($gitKeys[$issue->getKey()]['hash']));
        }
        return $authors;
    }

    /**
     * Get issue authors from issue change log
     *
     * @param Jira\Api   $api
     * @param Jira\Issue $issue
     * @return mixed
     */
    protected function _getIssueChangeLogAuthors(Jira\Api $api, Jira\Issue $issue)
    {
        /**
         * Try to find author for non-code issue
         */
        $authors = array();
        $issueResult              = $api->getIssue($issue->getKey(), 'changelog')->getResult();
        $issueResult['changelog'] = array_reverse($issueResult['changelog'], true);
        foreach ($issueResult['changelog'] as $changes) {
            if (!is_array($changes)) {
                continue;
            }
            foreach ($changes as $key => $change) {
                foreach ($change['items'] as $item) {
                    if ($item['field'] == $item['field'] && $item['toString'] == 'Resolved') {
                        $authors = $change['author']['displayName'];
                        break 3;
                    }
                }
            }
        }
        return $authors;
    }

    /**
     * Get issue assignee
     *
     * @param Jira\Issue $issue
     * @return mixed|string
     */
    protected function _getIssueAssignee(Jira\Issue $issue)
    {
        $assignee = $issue->getAssignee();
        return $assignee['displayName'] . ' (assignee)';
    }

    /**
     * Get authors by JQL type
     *
     * @param array            $jqlItem
     * @param Jira\Issue       $issue
     * @param Jira\Api         $api
     * @param Vcs\InterfaceVcs $vcs
     * @return array|string
     */
    protected function _getAuthorsByJqlType($jqlItem, $issue, Jira\Api $api, Vcs\InterfaceVcs $vcs)
    {
        $authors = '';
        if (JigitJira\Jql::TYPE_WITHOUT_FIX_VERSION == $jqlItem['type']
            || JigitJira\Jql::TYPE_OPEN_FOR_IN_PROGRESS_VERSION == $jqlItem['type']
        ) {
            $authors = $this->_getIssueAuthors($api, $issue, $vcs->getCommits());
            return $authors;
        } elseif (JigitJira\Jql::TYPE_NOT_AFFECTS_CODE == $jqlItem['type']) {
            $authors = $this->_getIssueAuthors($api, $issue, array());
            return $authors;
        }
        return $authors;
    }

    /**
     * Get issue content block
     *
     * @param Jira\Issue $issue
     * @param array $authors
     * @return array
     */
    protected function _getIssueContentBlock($issue, $authors)
    {
        $sprint            = $this->_getIssueSprint($issue);
        $status            = $this->_getIssueStatus($issue);
        $type              = $this->_getIssueType($issue);
        $affectedVersions  = implode(', ', $this->_getIssueAffectsVersions($issue));
        $fixVersionsString = implode(', ', $this->_getIssueFixVersions($issue));
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
}
