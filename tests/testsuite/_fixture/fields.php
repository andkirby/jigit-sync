<?php
return array (
    'issuekey' =>
        array (
            'id' => 'issuekey',
            'name' => 'Key',
            'custom' => false,
            'orderable' => false,
            'navigable' => true,
            'searchable' => false,
            'clauseNames' =>
                array (
                    'id',
                    'issue',
                    'issuekey',
                    'key',
                ),
        ),
    'created' =>
        array (
            'id' => 'created',
            'name' => 'Created',
            'custom' => false,
            'orderable' => false,
            'navigable' => true,
            'searchable' => true,
            'clauseNames' =>
                array (
                    'created',
                    'createdDate',
                ),
            'schema' =>
                array (
                    'type' => 'datetime',
                    'system' => 'created',
                ),
        ),
    'project' =>
        array (
            'id' => 'project',
            'name' => 'Project',
            'custom' => false,
            'orderable' => false,
            'navigable' => true,
            'searchable' => true,
            'clauseNames' =>
                array (
                    'project',
                ),
            'schema' =>
                array (
                    'type' => 'project',
                    'system' => 'project',
                ),
        ),
    'lastViewed' =>
        array (
            'id' => 'lastViewed',
            'name' => 'Last Viewed',
            'custom' => false,
            'orderable' => false,
            'navigable' => true,
            'searchable' => false,
            'clauseNames' =>
                array (
                    'lastViewed',
                ),
            'schema' =>
                array (
                    'type' => 'datetime',
                    'system' => 'lastViewed',
                ),
        ),
    'components' =>
        array (
            'id' => 'components',
            'name' => 'Component/s',
            'custom' => false,
            'orderable' => true,
            'navigable' => true,
            'searchable' => true,
            'clauseNames' =>
                array (
                    'component',
                ),
            'schema' =>
                array (
                    'type' => 'array',
                    'items' => 'component',
                    'system' => 'components',
                ),
        ),
    'resolutiondate' =>
        array (
            'id' => 'resolutiondate',
            'name' => 'Resolved',
            'custom' => false,
            'orderable' => false,
            'navigable' => true,
            'searchable' => true,
            'clauseNames' =>
                array (
                    'resolutiondate',
                    'resolved',
                ),
            'schema' =>
                array (
                    'type' => 'datetime',
                    'system' => 'resolutiondate',
                ),
        ),
    'timeestimate' =>
        array (
            'id' => 'timeestimate',
            'name' => 'Remaining Estimate',
            'custom' => false,
            'orderable' => false,
            'navigable' => true,
            'searchable' => false,
            'clauseNames' =>
                array (
                    'remainingEstimate',
                    'timeestimate',
                ),
            'schema' =>
                array (
                    'type' => 'number',
                    'system' => 'timeestimate',
                ),
        ),
    'updated' =>
        array (
            'id' => 'updated',
            'name' => 'Updated',
            'custom' => false,
            'orderable' => false,
            'navigable' => true,
            'searchable' => true,
            'clauseNames' =>
                array (
                    'updated',
                    'updatedDate',
                ),
            'schema' =>
                array (
                    'type' => 'datetime',
                    'system' => 'updated',
                ),
        ),
    'priority' =>
        array (
            'id' => 'priority',
            'name' => 'Priority',
            'custom' => false,
            'orderable' => true,
            'navigable' => true,
            'searchable' => true,
            'clauseNames' =>
                array (
                    'priority',
                ),
            'schema' =>
                array (
                    'type' => 'priority',
                    'system' => 'priority',
                ),
        ),
    'description' =>
        array (
            'id' => 'description',
            'name' => 'Description',
            'custom' => false,
            'orderable' => true,
            'navigable' => true,
            'searchable' => true,
            'clauseNames' =>
                array (
                    'description',
                ),
            'schema' =>
                array (
                    'type' => 'string',
                    'system' => 'description',
                ),
        ),
    'issuelinks' =>
        array (
            'id' => 'issuelinks',
            'name' => 'Linked Issues',
            'custom' => false,
            'orderable' => true,
            'navigable' => true,
            'searchable' => true,
            'clauseNames' =>
                array (
                ),
            'schema' =>
                array (
                    'type' => 'array',
                    'items' => 'issuelinks',
                    'system' => 'issuelinks',
                ),
        ),
    'creator' =>
        array (
            'id' => 'creator',
            'name' => 'Creator',
            'custom' => false,
            'orderable' => false,
            'navigable' => true,
            'searchable' => true,
            'clauseNames' =>
                array (
                    'creator',
                ),
            'schema' =>
                array (
                    'type' => 'user',
                    'system' => 'creator',
                ),
        ),
    'aggregatetimeoriginalestimate' =>
        array (
            'id' => 'aggregatetimeoriginalestimate',
            'name' => '╬г Original Estimate',
            'custom' => false,
            'orderable' => false,
            'navigable' => true,
            'searchable' => false,
            'clauseNames' =>
                array (
                ),
            'schema' =>
                array (
                    'type' => 'number',
                    'system' => 'aggregatetimeoriginalestimate',
                ),
        ),
    'assignee' =>
        array (
            'id' => 'assignee',
            'name' => 'Assignee',
            'custom' => false,
            'orderable' => true,
            'navigable' => true,
            'searchable' => true,
            'clauseNames' =>
                array (
                    'assignee',
                ),
            'schema' =>
                array (
                    'type' => 'user',
                    'system' => 'assignee',
                ),
        ),
    'aggregatetimespent' =>
        array (
            'id' => 'aggregatetimespent',
            'name' => '╬г Time Spent',
            'custom' => false,
            'orderable' => false,
            'navigable' => true,
            'searchable' => false,
            'clauseNames' =>
                array (
                ),
            'schema' =>
                array (
                    'type' => 'number',
                    'system' => 'aggregatetimespent',
                ),
        ),
    'timespent' =>
        array (
            'id' => 'timespent',
            'name' => 'Time Spent',
            'custom' => false,
            'orderable' => false,
            'navigable' => true,
            'searchable' => false,
            'clauseNames' =>
                array (
                    'timespent',
                ),
            'schema' =>
                array (
                    'type' => 'number',
                    'system' => 'timespent',
                ),
        ),
    'reporter' =>
        array (
            'id' => 'reporter',
            'name' => 'Reporter',
            'custom' => false,
            'orderable' => true,
            'navigable' => true,
            'searchable' => true,
            'clauseNames' =>
                array (
                    'reporter',
                ),
            'schema' =>
                array (
                    'type' => 'user',
                    'system' => 'reporter',
                ),
        ),
    'comment' =>
        array (
            'id' => 'comment',
            'name' => 'Comment',
            'custom' => false,
            'orderable' => true,
            'navigable' => false,
            'searchable' => true,
            'clauseNames' =>
                array (
                    'comment',
                ),
            'schema' =>
                array (
                    'type' => 'array',
                    'items' => 'comment',
                    'system' => 'comment',
                ),
        ),
    'votes' =>
        array (
            'id' => 'votes',
            'name' => 'Votes',
            'custom' => false,
            'orderable' => false,
            'navigable' => true,
            'searchable' => false,
            'clauseNames' =>
                array (
                    'votes',
                ),
            'schema' =>
                array (
                    'type' => 'array',
                    'items' => 'votes',
                    'system' => 'votes',
                ),
        ),
    'duedate' =>
        array (
            'id' => 'duedate',
            'name' => 'Due Date',
            'custom' => false,
            'orderable' => true,
            'navigable' => true,
            'searchable' => true,
            'clauseNames' =>
                array (
                    'due',
                    'duedate',
                ),
            'schema' =>
                array (
                    'type' => 'date',
                    'system' => 'duedate',
                ),
        ),
    'timetracking' =>
        array (
            'id' => 'timetracking',
            'name' => 'Time Tracking',
            'custom' => false,
            'orderable' => true,
            'navigable' => false,
            'searchable' => true,
            'clauseNames' =>
                array (
                ),
            'schema' =>
                array (
                    'type' => 'timetracking',
                    'system' => 'timetracking',
                ),
        ),
    'security' =>
        array (
            'id' => 'security',
            'name' => 'Security Level',
            'custom' => false,
            'orderable' => true,
            'navigable' => true,
            'searchable' => true,
            'clauseNames' =>
                array (
                    'level',
                ),
            'schema' =>
                array (
                    'type' => 'securitylevel',
                    'system' => 'security',
                ),
        ),
    'resolution' =>
        array (
            'id' => 'resolution',
            'name' => 'Resolution',
            'custom' => false,
            'orderable' => true,
            'navigable' => true,
            'searchable' => true,
            'clauseNames' =>
                array (
                    'resolution',
                ),
            'schema' =>
                array (
                    'type' => 'resolution',
                    'system' => 'resolution',
                ),
        ),
    'summary' =>
        array (
            'id' => 'summary',
            'name' => 'Summary',
            'custom' => false,
            'orderable' => true,
            'navigable' => true,
            'searchable' => true,
            'clauseNames' =>
                array (
                    'summary',
                ),
            'schema' =>
                array (
                    'type' => 'string',
                    'system' => 'summary',
                ),
        ),
    'issuetype' =>
        array (
            'id' => 'issuetype',
            'name' => 'Issue Type',
            'custom' => false,
            'orderable' => true,
            'navigable' => true,
            'searchable' => true,
            'clauseNames' =>
                array (
                    'issuetype',
                    'type',
                ),
            'schema' =>
                array (
                    'type' => 'issuetype',
                    'system' => 'issuetype',
                ),
        ),
    'progress' =>
        array (
            'id' => 'progress',
            'name' => 'Progress',
            'custom' => false,
            'orderable' => false,
            'navigable' => true,
            'searchable' => false,
            'clauseNames' =>
                array (
                    'progress',
                ),
            'schema' =>
                array (
                    'type' => 'progress',
                    'system' => 'progress',
                ),
        ),
    'labels' =>
        array (
            'id' => 'labels',
            'name' => 'Labels',
            'custom' => false,
            'orderable' => true,
            'navigable' => true,
            'searchable' => true,
            'clauseNames' =>
                array (
                    'labels',
                ),
            'schema' =>
                array (
                    'type' => 'array',
                    'items' => 'string',
                    'system' => 'labels',
                ),
        ),
    'fixVersions' =>
        array (
            'id' => 'fixVersions',
            'name' => 'Fix Version/s',
            'custom' => false,
            'orderable' => true,
            'navigable' => true,
            'searchable' => true,
            'clauseNames' =>
                array (
                    'fixVersion',
                ),
            'schema' =>
                array (
                    'type' => 'array',
                    'items' => 'version',
                    'system' => 'fixVersions',
                ),
        ),
    'attachment' =>
        array (
            'id' => 'attachment',
            'name' => 'Attachment',
            'custom' => false,
            'orderable' => true,
            'navigable' => false,
            'searchable' => true,
            'clauseNames' =>
                array (
                    'attachments',
                ),
            'schema' =>
                array (
                    'type' => 'array',
                    'items' => 'attachment',
                    'system' => 'attachment',
                ),
        ),
    'aggregatetimeestimate' =>
        array (
            'id' => 'aggregatetimeestimate',
            'name' => '╬г Remaining Estimate',
            'custom' => false,
            'orderable' => false,
            'navigable' => true,
            'searchable' => false,
            'clauseNames' =>
                array (
                ),
            'schema' =>
                array (
                    'type' => 'number',
                    'system' => 'aggregatetimeestimate',
                ),
        ),
    'timeoriginalestimate' =>
        array (
            'id' => 'timeoriginalestimate',
            'name' => 'Original Estimate',
            'custom' => false,
            'orderable' => false,
            'navigable' => true,
            'searchable' => false,
            'clauseNames' =>
                array (
                    'originalEstimate',
                    'timeoriginalestimate',
                ),
            'schema' =>
                array (
                    'type' => 'number',
                    'system' => 'timeoriginalestimate',
                ),
        ),
    'watches' =>
        array (
            'id' => 'watches',
            'name' => 'Watchers',
            'custom' => false,
            'orderable' => false,
            'navigable' => true,
            'searchable' => false,
            'clauseNames' =>
                array (
                    'watchers',
                ),
            'schema' =>
                array (
                    'type' => 'array',
                    'items' => 'watches',
                    'system' => 'watches',
                ),
        ),
    'worklog' =>
        array (
            'id' => 'worklog',
            'name' => 'Log Work',
            'custom' => false,
            'orderable' => true,
            'navigable' => false,
            'searchable' => true,
            'clauseNames' =>
                array (
                ),
            'schema' =>
                array (
                    'type' => 'array',
                    'items' => 'worklog',
                    'system' => 'worklog',
                ),
        ),
    'subtasks' =>
        array (
            'id' => 'subtasks',
            'name' => 'Sub-Tasks',
            'custom' => false,
            'orderable' => false,
            'navigable' => true,
            'searchable' => false,
            'clauseNames' =>
                array (
                    'subtasks',
                ),
            'schema' =>
                array (
                    'type' => 'array',
                    'items' => 'issuelinks',
                    'system' => 'subtasks',
                ),
        ),
    'status' =>
        array (
            'id' => 'status',
            'name' => 'Status',
            'custom' => false,
            'orderable' => false,
            'navigable' => true,
            'searchable' => true,
            'clauseNames' =>
                array (
                    'status',
                ),
            'schema' =>
                array (
                    'type' => 'status',
                    'system' => 'status',
                ),
        ),
    'workratio' =>
        array (
            'id' => 'workratio',
            'name' => 'Work Ratio',
            'custom' => false,
            'orderable' => false,
            'navigable' => true,
            'searchable' => true,
            'clauseNames' =>
                array (
                    'workratio',
                ),
            'schema' =>
                array (
                    'type' => 'number',
                    'system' => 'workratio',
                ),
        ),
    'environment' =>
        array (
            'id' => 'environment',
            'name' => 'Environment',
            'custom' => false,
            'orderable' => true,
            'navigable' => true,
            'searchable' => true,
            'clauseNames' =>
                array (
                    'environment',
                ),
            'schema' =>
                array (
                    'type' => 'string',
                    'system' => 'environment',
                ),
        ),
    'thumbnail' =>
        array (
            'id' => 'thumbnail',
            'name' => 'Images',
            'custom' => false,
            'orderable' => false,
            'navigable' => true,
            'searchable' => false,
            'clauseNames' =>
                array (
                ),
        ),
    'aggregateprogress' =>
        array (
            'id' => 'aggregateprogress',
            'name' => 'In Progress',
            'custom' => false,
            'orderable' => false,
            'navigable' => true,
            'searchable' => false,
            'clauseNames' =>
                array (
                ),
            'schema' =>
                array (
                    'type' => 'progress',
                    'system' => 'aggregateprogress',
                ),
        ),
    'versions' =>
        array (
            'id' => 'versions',
            'name' => 'Affects Version/s',
            'custom' => false,
            'orderable' => true,
            'navigable' => true,
            'searchable' => true,
            'clauseNames' =>
                array (
                    'affectedVersion',
                ),
            'schema' =>
                array (
                    'type' => 'array',
                    'items' => 'version',
                    'system' => 'versions',
                ),
        ),
);
