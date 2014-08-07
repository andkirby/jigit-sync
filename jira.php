<?php

require_once 'jira-init.php'; //initialize

$output = new JigitOutput();

require_once 'jira-header.php'; //get header content

$output->enableDecorator(true, true);
$output->add("Project:             $project");
$output->add("Compare:             $branchTop -> $branchLow");
$output->add("Required FixVersion: $requiredFixVersion");
$output->add("Version in progress: $inProgress");
$output->add("Sprint:              $activeSprintIdString");
$output->disableDecorator();

$gitKeys = require_once 'git-keys.php';

$keys = JiraKeysFormatter::format(implode(', ', array_keys($gitKeys)));
$output->add('Found issues in GIT: ' . $keys);

/**
 * Connect to JIRA
 */
$api = new \chobie\Jira\Api(
    $jiraUrl,
    new \chobie\Jira\Api\Authentication\Basic($jiraUser, $jiraPassword)
);

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
    AND (
        (key IN ($keys) AND type NOT IN ('Sub-Task Task'))
        OR
        (issueFunction IN parentsOf("project = $project AND key IN ($keys)"))
    )
JQL;
echo $jql . PHP_EOL;
//@finishSkipCommitHooks
$jqlList[] = array(
    'message' => $message,
    'jql'     => $jql,
    'type'    => JiraJql::TYPE_WITHOUT_FIX_VERSION,
);

//not affected code
$message = 'Issues which did not affect the code.';
//@startSkipCommitHooks
$jql = <<<JQL
project = $project
    AND fixVersion = $requiredFixVersion
    AND key NOT IN ($keys)
    AND (type IN ('Change Request', Story, Epic))
JQL;
//@finishSkipCommitHooks
$jqlList[] = array(
    'message' => $message,
    'jql'     => $jql,
    'type'    => JiraJql::TYPE_NOT_AFFECTS_CODE,
);

//parent issue has commit
$message = 'Parent issue has commit.';
//@startSkipCommitHooks
$jql = <<<JQL
project = $project
    AND fixVersion = $requiredFixVersion
    AND key IN ($keys)
    AND (type IN ('Change Request', Story, Epic) OR and issueFunction IN hasSubtasks())
JQL;
//@finishSkipCommitHooks
$jqlList[] = array(
    'message' => $message,
    'jql'     => $jql,
    'type'    => JiraJql::TYPE_PARENT_HAS_COMMIT,
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
 * Show found issues
 */
$hasNoSprint = array();
$inDifferentBranch = array();
$found = false;
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
        //Skip build notes issue
        if (false !== strpos($issue->getSummary(), 'Build ' . $requiredFixVersion)) {
            continue;
        }

        /**
         * Identify issues which not in required branch
         */
        if (JiraJql::TYPE_NOT_AFFECTS_CODE == $item['type']) {
            $key = $issue->getKey();
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

        $status = $issue->getStatus();
        $type = $issue->getIssueType();

        $toOutput[] = <<<ISSUE
Key:               {$issue->getKey()}: {$issue->getSummary()}
Type:              {$type['name']}
Status:            {$status['name']}
AffectedVersion/s: {$affectedVersionsString}
FixVersion/s:      {$fixVersionsString}
Sprint:            {$sprint}
ISSUE;
        $added = true;
    }

    if (!$added) {
        continue;
    }
    $output->enableDecorator();
    $output->add($item['message']);
    $output->disableDecorator();
    $keys = JiraKeysFormatter::format(implode(', ', $showKeys));
    $output->add('Keys: ' . $keys);

    foreach ($toOutput as $outputItem) {
        $output->addDelimiter();
        $output->add($outputItem);
    }
    $found = true;
}
if ($hasNoSprint) {
    $output->enableDecorator();
    $output->add('NOTICE: Issues did not add to active sprint');
    $output->disableDecorator();
    $keys = JiraKeysFormatter::format(implode(', ', $hasNoSprint));
    $output->add('Keys: ' . $keys);
    $found = true;
}
if ($inDifferentBranch) {
    $output->enableDecorator();
    $output->add('WARNING!!! Issues committed in a different branch');
    $output->disableDecorator();
    $keys = JiraKeysFormatter::format(implode(', ', $inDifferentBranch));
    $output->add('Keys: ' . $keys);
    $found = true;
}

if (!$found) {
    $output->enableDecorator();
    $output->add('SUCCESS! Everything is OK');
    $output->disableDecorator();
}

echo $output->getOutputString();
