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

/**
 * Class Report
 *
 * @package Jigit
 */
class Report
{
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

                $toOutput[] = "Key:               {$issue->getKey()}: {$issue->getSummary()}";
                $toOutput[] = "Type:              {$type}";
                $toOutput[] = "AffectedVersion/s: {$affectedVersionsString}";
                $toOutput[] = "FixVersion/s:      {$fixVersionsString}";
                $toOutput[] = "Status:            {$status}";
                $toOutput[] = "Sprint:            {$sprint}";
                $toOutput[] = "Author/s:          {$authors}";

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
        $key     = $issue->getKey();
        $gitRoot = Config\Project::getProjectGitRoot();
        //@startSkipCommitHooks
        $log = `git --git-dir $gitRoot/.git/ log --all --no-merges --grep="$key"`;
        //@finishSkipCommitHooks
        return (bool) $log;
    }
}
