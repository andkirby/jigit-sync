<?php
/**
 * Created by PhpStorm.
 * User: a.roslik
 * Date: 8/13/2014
 * Time: 3:11 PM
 */

use \Jigit\Output as Output;
use \Jigit\Jira as JigitJira;
use \Jigit\Config\User as ConfigUser;
use \Jigit\Config;
use \chobie\Jira as Jira;

/**
 * Show found issues
 */
$hasNoSprint = array();
$inDifferentBranch = array();
$found = false;
foreach ($jqlList as $jqlItem) {
    $jql = $jqlItem['jql'];
    if (ConfigUser::getJiraTargetFixVersionInProgress() && '1' !== $jqlItem['in_progress']
        || !ConfigUser::getJiraTargetFixVersionInProgress() && '0' !== $jqlItem['in_progress']
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
        if (false !== strpos($issue->getSummary(), 'Build ' . $requiredFixVersion)) {
            continue;
        }

        /**
         * Identify issues which not in required branch
         */
        if (JigitJira\Jql::TYPE_NOT_AFFECTS_CODE == $jqlItem['type']) {
            $key = $issue->getKey();
            $gitRoot = ConfigUser::getProjectGitRoot();
            $log = `git --git-dir $gitRoot/.git/ log --all --grep="$key"`;
            if ($log) {
                $inDifferentBranch[] = $key;
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
                    if ($affVer == trim($requiredFixVersion, 'v')
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

        $toOutput[] = <<<ISSUE
Key:               {$issue->getKey()}: {$issue->getSummary()}
Type:              {$type}
Status:            {$status}
AffectedVersion/s: {$affectedVersionsString}
FixVersion/s:      {$fixVersionsString}
Sprint:            {$sprint}
Author/s:          {$authors}
ISSUE;
        $added = true;
    }

    if (!$added) {
        continue;
    }
    $output->enableDecorator();
    $output->add($jqlItem['message']);
    $output->disableDecorator();
    $keys = JigitJira\KeysFormatter::format(implode(', ', $showKeys));
    $output->add('Keys: ' . $keys);

    foreach ($toOutput as $outputItem) {
        $output->addDelimiter();
        $output->add($outputItem);
    }
    $found = true;
}
if ($inDifferentBranch) {
    $output->enableDecorator();
    $output->add('WARNING!!! Issues committed in a different branch');
    $output->disableDecorator();
    $keys = JigitJira\KeysFormatter::format(implode(', ', $inDifferentBranch));
    $output->add('Keys: ' . $keys);
    $found = true;
}

if (!$found) {
    $output->enableDecorator();
    $output->add('SUCCESS! Everything is OK');
    $output->disableDecorator();
}
