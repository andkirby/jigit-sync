<?php
namespace Jigit\Jira;

use chobie\Jira\Api;
use chobie\Jira\Issue;
use Jigit\Vcs\InterfaceVcs;

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
                return $sprint;
            }
            return $sprint;
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
        $status = $status['name'];
        return $status;
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
     * Get authors by JQL type
     *
     * @param array            $jqlItem
     * @param Issue       $issue
     * @param Api         $api
     * @param InterfaceVcs $vcs
     * @return array|string
     */
    public function getAuthorsByJqlType($jqlItem, $issue, Api $api, InterfaceVcs $vcs)
    {
        $authors = array();
        if (Jql::TYPE_WITHOUT_FIX_VERSION == $jqlItem['type']
            || Jql::TYPE_OPEN_FOR_IN_PROGRESS_VERSION == $jqlItem['type']
        ) {
            $authors = $this->getIssueAuthors($api, $issue, $vcs->getCommits());
        } elseif (Jql::TYPE_NOT_AFFECTS_CODE == $jqlItem['type']) {
            $authors = $this->getIssueAuthors($api, $issue, array());
        }
        return $authors;
    }

    /**
     * Get issue sprints
     *
     * @param Issue $issue
     * @param Api   $api
     * @param Issue $issue
     * @param array      $gitKeys
     * @return array
     */
    public function getIssueAuthors(Api $api, Issue $issue, $gitKeys)
    {
        $authors = $this->getIssueVcsAuthors($issue, $gitKeys);
        if (!$authors) {
            $authors = $this->getIssueChangeLogAuthors($api, $issue);
        }
        if (!$authors) {
            $assignee = $this->getIssueAssignee($issue);
            $authors  = $assignee;
        }
        return $authors;
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
            $authors = implode(', ', array_keys($gitKeys[$issue->getKey()]['hash']));
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
    public function getIssueChangeLogAuthors(Api $api, Issue $issue)
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
     * @param Issue $issue
     * @return mixed|string
     */
    public function getIssueAssignee(Issue $issue)
    {
        $assignee = $issue->getAssignee();
        return $assignee['displayName'] . ' (assignee)';
    }
}
