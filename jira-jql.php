<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 12.08.2014
 * Time: 3:44
 */

use \Jigit\Jira as JigitJira;
use \Jigit\Config\User as ConfigUser;
use \chobie\Jira as Jira;

$project = ConfigUser::getJiraProject();
$requiredFixVersion = ConfigUser::getJiraTargetFixVersion();

$keys = JigitJira\KeysFormatter::format(implode(', ', array_keys($gitKeys)));
$output->add('Found issues in GIT: ' . $keys);

$notAffectsCodeResolutions = "Cancelled, 'Cannot Reproduce', Declined, Duplicate, 'Not a Bug', 'Not Actual'";
$notAffectsCodeLabels = implode(', ', ConfigUser::getJiraNonAffectsCodeLabels());

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
//@finishSkipCommitHooks
$jqlList[] = array(
    'message' => $message,
    'jql'     => $jql,
    'type'    => JigitJira\Jql::TYPE_WITHOUT_FIX_VERSION,
);

//not affected code
$message = 'Issues which did not affect the code.';
//@startSkipCommitHooks
$jql = <<<JQL
project = $project
    AND fixVersion = $requiredFixVersion
    AND key NOT IN ($keys)
    AND resolution NOT IN ($notAffectsCodeResolutions)
    AND labels NOT IN ($notAffectsCodeLabels)
    AND type NOT IN ('Change Request', Story, Epic)
JQL;
//@finishSkipCommitHooks
$jqlList[] = array(
    'message' => $message,
    'jql'     => $jql,
    'type'    => JigitJira\Jql::TYPE_NOT_AFFECTS_CODE,
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
    'type'    => JigitJira\Jql::TYPE_PARENT_HAS_COMMIT,
);

if (ConfigUser::getJiraTargetFixVersionInProgress()) {
    //open issues for in progress version
    $message = 'Open issues for "in progress" fix version.';

    //@startSkipCommitHooks
    $jql = <<<JQL
        project = $project
        AND status IN (Open, Reopened, 'In Progress')
        AND (fixVersion = $requiredFixVersion)
JQL;
    //@finishSkipCommitHooks
    $jqlList[] = array(
        'message' => $message,
        'jql'     => $jql,
        'type'    => JigitJira\Jql::TYPE_OPEN_FOR_IN_PROGRESS_VERSION,
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
        'type'    => JigitJira\Jql::TYPE_OPEN_FOR_IN_PROGRESS_VERSION,
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
        'type'    => JigitJira\Jql::TYPE_WITHOUT_AFFECTED_VERSION,
    );
}

return $jqlList;
