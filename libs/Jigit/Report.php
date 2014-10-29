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
     * @param Jira\Api $api
     * @param array    $jqlList
     */
    public function make(Jira\Api $api, array $jqlList)
    {
        /**
         * Show found issues
         */
        $inDifferentBranch = array();
        $found = false;
        foreach ($jqlList as $jqlItem) {
            $this->_debugJqlItem($jqlItem);

            $jql = $jqlItem['jql'];
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

                $fields = $issue->getFields();

                //fixVersions
                $fixVersionsString = '';
                $fixVersions = $issue->getFixVersions();
                foreach ($fixVersions as $fix) {
                    $fixVersionsString .= $fix['name'] . ' ';
                }

                //affectedVersions
                $affectedVersionsString = '';
                $affectedVersions = $fields['Affects Version/s'];
                foreach ($affectedVersions as $fix) {
                    $affectedVersionsString .= $fix['name'] . ' ';
                }

                //sprint
                $sprint = $fields['Sprint'];
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
                    }
                }

                //check fix version right?
                if ($jqlItem['type'] === JigitJira\Jql::TYPE_WITHOUT_FIX_VERSION) {
                    $fixVersions = explode(' ', trim($fixVersionsString));
                    $affectedVersions = explode(' ', trim($affectedVersionsString));
                    foreach ($affectedVersions as $affVer) {
                        $affVer = ltrim($affVer, 'v');
                        foreach ($fixVersions as $fixVer) {
                            $fixVer = ltrim($fixVer, 'v');
                            if ($affVer == trim(Config\Project::getJiraTargetFixVersion(), 'v')
                                && version_compare($fixVer, $affVer, '>')
                            ) {
                                continue 3;
                            }
                        }
                    }
                }

                $status = $issue->getStatus();
                $status = $status['name'];
                $type = $issue->getIssueType();
                $type = $type['name'];

                $authors = '';
                if (JigitJira\Jql::TYPE_WITHOUT_FIX_VERSION == $jqlItem['type']
                    || JigitJira\Jql::TYPE_OPEN_FOR_IN_PROGRESS_VERSION == $jqlItem['type']
                ) {
                    if (isset($gitKeys[$issue->getKey()])) {
                        $authors = implode(', ', array_keys($gitKeys[$issue->getKey()]['hash']));
                    } else {
                        $assignee = $issue->getAssignee();
                        $authors = $assignee['displayName'] . ' (assignee)';
                    }
                } elseif (JigitJira\Jql::TYPE_NOT_AFFECTS_CODE == $jqlItem['type']) {
                    /**
                     * Try to find author for non-code issue
                     */
                    $issueResult = $api->getIssue($issue->getKey(), 'changelog')->getResult();
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
                    if (!$authors) {
                        $assignee = $issue->getAssignee();
                        $authors = $assignee['displayName'] . ' (assignee)';
                    }
                }

                $showKeys[] = $issue->getKey();
                $strIssue = array();
                $strIssue[] = "Key:               {$issue->getKey()}: {$issue->getSummary()}";
                $strIssue[] = "Type:              {$type}";
                $strIssue[] = "AffectedVersion/s: {$affectedVersionsString}";
                $strIssue[] = "FixVersion/s:      {$fixVersionsString}";
                $strIssue[] = "Status:            {$status}";
                $strIssue[] = "Sprint:            {$sprint}";
                $strIssue[] = "Author/s:          {$authors}";
                $toOutput[] = $strIssue;

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
        $tagsString = JigitJira\KeysFormatter::format(implode(', ', $this->_getVcsTags($vcs)));
        Config::addDebug('Found tags: ' . PHP_EOL . $tagsString);

        foreach ($jqlList as $jqlItem) {
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
        Config::addDebug("JQL: {$jqlItem['type']}: {$jqlItem['jql']}");
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
}
