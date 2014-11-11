<?php
namespace Jigit\Jira;

use chobie\Jira\Issue;

/**
 * Class IssueHelper created to make easy way to get fields information
 *
 * @package Jigit\Jira
 */
class IssueHelper
{
    /**
     * Get Affects Version/s of issue
     *
     * @param Issue $issue
     * @return array
     */
    public function getIssueAffectsVersions($issue)
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
     * @param Issue $issue
     * @return array
     */
    public function getIssueFixVersions($issue)
    {
        $affectedVersions = array();
        foreach ($issue->getFixVersions() as $fix) {
            $affectedVersions[] = $fix['name'];
        }
        return $affectedVersions;
    }

    /**
     * Get issue sprints
     *
     * @param Issue $issue
     * @return string
     */
    public function getIssueSprint($issue)
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
            }
        }
        return $sprint;
    }

    /**
     * Get issue status
     *
     * @param Issue $issue
     * @return string
     */
    public function getIssueStatus($issue)
    {
        $status = $issue->getStatus();
        return isset($status['name']) ? $status['name'] : null;
    }

    /**
     * Get issue type
     *
     * @param Issue $issue
     * @return mixed
     */
    public function getIssueType($issue)
    {
        $type = $issue->getIssueType();
        $type = $type['name'];
        return $type;
    }

    /**
     * Get issue sprints
     *
     * @param Issue $issue
     * @param array $gitKeys
     * @return array
     */
    public function getIssueAuthors(Issue $issue, $gitKeys)
    {
        $authors = $this->getIssueVcsAuthors($issue, $gitKeys);
        if (!$authors) {
            $authors = $this->getIssueChangeLogAuthors($issue);
        }
        if (!$authors) {
            $authors  = array($this->getIssueAssignee($issue) . ' (assignee)');
        }
        return $authors ?: array();
    }

    /**
     * Get issue VCS authors
     *
     * @param Issue $issue
     * @param array      $gitKeys
     * @return string
     */
    public function getIssueVcsAuthors(Issue $issue, $gitKeys)
    {
        $authors = array();
        if ($gitKeys && isset($gitKeys[$issue->getKey()])) {
            $authors = array_keys($gitKeys[$issue->getKey()]['hash']);
        }
        return $authors;
    }

    /**
     * Get issue authors from issue change log
     *
     * @param Api   $api
     * @param Issue $issue
     * @return mixed
     */
    public function getIssueChangeLogAuthors(Issue $issue)
    {
        /**
         * Try to find author for non-code issue
         */
        $authors = array();
        foreach ($this->getChangeLog($issue) as $changes) {
            if (!is_array($changes)) {
                continue;
            }
            foreach ($changes as $key => $change) {
                foreach ($change['items'] as $item) {
                    if ($item['field'] == $item['field'] && $item['toString'] == 'Resolved') {
                        $authors[] = $change['author']['displayName'];
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
     * @param Issue $issue
     * @return mixed|string
     */
    public function getIssueAssignee(Issue $issue)
    {
        $assignee = $issue->getAssignee();
        return $assignee['displayName'];
    }

    /**
     * Get issue change log
     *
     * @param Issue $issue
     * @param bool  $reverse
     * @return array
     */
    public function getChangeLog(Issue $issue, $reverse = true)
    {
        $info      = $issue->getExpandedInformation();
        $changeLog = isset($info['changelog']) ? $info['changelog'] : array();
        if ($reverse) {
            $changeLog = array_reverse($changeLog, true);
        }
        return $changeLog;
    }
}
