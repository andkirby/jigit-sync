<?php
require_once 'jira-init.php'; //initialize

/**
 * Class with JQL query types
 */
class JiraJql
{
    /**#@+
     * Query types
     */
    const TYPE_WITHOUT_FIX_VERSION                  = 'inBranchWithoutFixVersion';
    const TYPE_WITHOUT_AFFECTED_VERSION             = 'inBranchWithoutAffectedVersion';
    const TYPE_WITHOUT_OPEN_FOR_IN_PROGRESS_VERSION = 'inBranchWithoutFixVersionNotDone';
    /**#@-*/
}

$api = new \chobie\Jira\Api(
    $jiraUrl,
    new \chobie\Jira\Api\Authentication\Basic($jiraUser, $jiraPassword)
);
$gitRoot = str_replace('\\', '/', $gitRoot);
/**
 * Get issues between different code versions
 */
$log = `git --git-dir $gitRoot/.git/ log $branchLow..$branchTop --oneline --no-merges`;
preg_match_all('/' . $project . '-[0-9]+/', $log, $matches);
$keys = array_unique($matches[0]);
$keys = implode(', ', $keys);
//add line separators
$keys = preg_replace('/(([A-Za-z-0-9]+,\s*){4})/', '$1' . PHP_EOL, $keys);
$output[] = 'Found issues: ' . PHP_EOL . $keys;
$output[] = '===============================================';

/**
 * Request problem issues
 */
$jqlList = array();
//no fix version
$message = 'Has no fix version.';
//@startSkipCommitHooks
$jql = <<<JQL
project = $project
    AND (
        (fixVersion != $requiredFixVersion OR fixVersion is EMPTY)
        AND status NOT IN (Open, Reopened, 'In Progress')
    )
    AND type NOT IN ('Sub-Task Task', 'Sub-Task Question')
    AND key IN ($keys)
JQL;
//@finishSkipCommitHooks
$jqlList[] = array(
    'message' => $message,
    'jql'     => $jql,
    'type'    => JiraJql::TYPE_WITHOUT_FIX_VERSION,
);

if ($requiredFixVersionInProgress) {
    //open issues for in progress version
    $message = 'Open issues for "in progress" fix version.';

    //@startSkipCommitHooks
    $jql = <<<JQL
        project = $project
        AND status IN (Open, Reopened, 'In Progress')
        AND key IN ($keys)
JQL;
    //@finishSkipCommitHooks
    $jqlList[] = array(
        'message' => $message,
        'jql'     => $jql,
        'type'    => JiraJql::TYPE_WITHOUT_OPEN_FOR_IN_PROGRESS_VERSION,
    );

    //done issues of open parent ones for in progress version
    //todo retest this query
    $message = 'Open TOP issues for "in progress" fix version.';
    //@startSkipCommitHooks
    $jql = <<<JQL
        project = $project
        AND status IN (Open, Reopened, 'In Progress')
        AND issueFunction IN parentsOf(
            "project = $project AND status NOT IN (Open, Reopened, 'In Progress') AND key IN ($keys)"
        )
JQL;
    //@finishSkipCommitHooks
    $jqlList[] = array(
        'message' => $message,
        'jql'     => $jql,
        'type'    => JiraJql::TYPE_WITHOUT_OPEN_FOR_IN_PROGRESS_VERSION,
    );
} else {
    //no affected version
    $message = 'Has no affected version.';

    //@startSkipCommitHooks
    $jql = <<<JQL
        project = $project
        AND (
            (affectedVersion != $requiredFixVersion OR affectedVersion is EMPTY)
            AND status IN (Open, Reopened, 'In Progress')
        )
        AND type NOT IN ('Sub-Task Task', 'Sub-Task Question')
        AND key IN ($keys)
JQL;
    //@finishSkipCommitHooks
    $jqlList[] = array(
        'message' => $message,
        'jql'     => $jql,
        'type'    => JiraJql::TYPE_WITHOUT_AFFECTED_VERSION,
    );
}

/**
 * Fields list
 *
 *
 */

$hasNoSprint = array();
foreach ($jqlList as $item) {
    $jql = $item['jql'];
    $showKeys = array();
    /** @var \chobie\Jira\Api\Result $result */
    $result = $api->search($jql);

    if (!$result->getTotal()) {
        continue;
    }

    $added = false;
    $toOutput = array();
    /** @var \chobie\Jira\Issue $issue */
    foreach ($result->getIssues() as $issue) {
        $fields = $issue->getFields();

        //status
        $status = $issue->getStatus();

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
                if (!in_array($matches[2][0], $activeSprintIds)) {
                    //an issue has wrong sprint
                    $hasNoSprint[] = $issue->getKey();
                }
            } else {
                //an issue has no sprint
                $hasNoSprint[] = $issue->getKey();
                $sprint .= '0';
            }
            if ($matches[1]) {
                //get sprint name
                $sprint .= ', ' . $matches[1][0];
            }
        } else {
            //an issue has no sprint
            $hasNoSprint[] = $issue->getKey();
        }

        //check fix version right?
        if ($item['type'] === JiraJql::TYPE_WITHOUT_FIX_VERSION) {
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

        $showKeys[] = $issue->getKey();

        $toOutput[] = <<<ISSUE
-------
Key:              {$issue->getKey()}
Summary:          {$issue->getSummary()}
Status:           {$status['name']}
FixVersion:       {$fixVersionsString}
AffectedVersions: {$affectedVersionsString}
Sprint:           {$sprint}
ISSUE;
        $added = true;
    }

    if (!$added) {
        continue;
    }

    $output[] = '============= ' . $item['message'] . ' =============';

    $output[] = 'Keys: ' . (implode(',', $showKeys));

    $output = array_merge_recursive($output, $toOutput);
}
if ($hasNoSprint) {
    $output[] = '============= Issues did not add to active sprint =============';
    $output[] = 'Keys: ' . (implode(',', $hasNoSprint));
}

echo implode(PHP_EOL, $output);
echo PHP_EOL;
