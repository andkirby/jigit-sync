<?php
namespace JigitTest\Response;

use JigitTest\Object;

/**
 * Class Issue
 *
 * @package JigitTest\Response
 */
class Issue extends Object
{
    /**
     * Constructor. Set data example
     */
    public function __construct()
    {
        $this->_data = array (
            'expand' => 'editmeta,renderedFields,transitions,changelog,operations',
            'id' => '225833',
            'self' => 'http://jira.example.com/rest/api/2/issue/225833',
            'key' => 'JIG-4345',
            'fields' =>
                array (
                    'summary' => 'Some issue summary',
                    'issuetype' =>
                        array (
                            'self' => 'http://jira.example.com/rest/api/2/issuetype/1',
                            'id' => '1',
                            'description' => 'A problem which impairs or prevents the functions of the product.',
                            'iconUrl' => 'http://jira.example.com/images/icons/issuetypes/bug.png',
                            'name' => 'Bug',
                            'subtask' => false,
                        ),
                    'timespent' => 1800,
                    'reporter' =>
                        array (
                            'self' => 'http://jira.example.com/rest/api/2/user?username=test.er',
                            'name' => 'test.er',
                            'emailAddress' => 'test.er@example.com',
                            'avatarUrls' =>
                                array (
                                    '16x16' => 'http://jira.example.com/secure/useravatar?size=xsmall&ownerId=test.er&avatarId=19509',
                                    '24x24' => 'http://jira.example.com/secure/useravatar?size=small&ownerId=test.er&avatarId=19509',
                                    '32x32' => 'http://jira.example.com/secure/useravatar?size=medium&ownerId=test.er&avatarId=19509',
                                    '48x48' => 'http://jira.example.com/secure/useravatar?ownerId=test.er&avatarId=19509',
                                ),
                            'displayName' => 'Test Er',
                            'active' => true,
                        ),
                    'created' => '2014-10-20T01:45:23.000-0700',
                    'project' =>
                        array (
                            'self' => 'http://jira.example.com/rest/api/2/project/14102',
                            'id' => '14102',
                            'key' => 'JIG',
                            'name' => 'JigitTestPrj',
                            'avatarUrls' =>
                                array (
                                    '16x16' => 'http://jira.example.com/secure/projectavatar?size=xsmall&pid=14102&avatarId=18004',
                                    '24x24' => 'http://jira.example.com/secure/projectavatar?size=small&pid=14102&avatarId=18004',
                                    '32x32' => 'http://jira.example.com/secure/projectavatar?size=medium&pid=14102&avatarId=18004',
                                    '48x48' => 'http://jira.example.com/secure/projectavatar?pid=14102&avatarId=18004',
                                ),
                            'projectCategory' =>
                                array (
                                    'self' => 'http://jira.example.com/rest/api/2/projectCategory/10703',
                                    'id' => '10703',
                                    'description' => 'Category for PHP Projects',
                                    'name' => 'Magento',
                                ),
                        ),
                    'lastViewed' => NULL,
                    'components' =>
                        array (
                        ),
                    'timeoriginalestimate' => 9000,
                    'votes' =>
                        array (
                            'self' => 'http://jira.example.com/rest/api/2/issue/JIG-4345/votes',
                            'votes' => 0,
                            'hasVoted' => false,
                        ),
                    'resolutiondate' => '2014-10-21T08:04:37.000-0700',
                    'duedate' => '2014-11-21',
                    'watches' =>
                        array (
                            'self' => 'http://jira.example.com/rest/api/2/issue/JIG-4345/watchers',
                            'watchCount' => 6,
                            'isWatching' => false,
                        ),
                    'timeestimate' => 0,
                    'progress' =>
                        array (
                            'progress' => 1800,
                            'total' => 1800,
                            'percent' => 100,
                        ),
                    'updated' => '2014-10-24T06:27:22.000-0700',
                    'priority' =>
                        array (
                            'self' => 'http://jira.example.com/rest/api/2/priority/4',
                            'iconUrl' => 'http://jira.example.com/images/icons/priorities/minor.png',
                            'name' => 'Minor',
                            'id' => '4',
                        ),
                    'description' => 'Some
Expanded
Description.
',
                    'issuelinks' =>
                        array (
                        ),
                    'subtasks' =>
                        array (
                        ),
                    'status' =>
                        array (
                            'self' => 'http://jira.example.com/rest/api/2/status/4',
                            'description' => 'This issue wasonce resolved, but the resolution was deemed incorrect. From here issues are either marked assigned or resolved.',
                            'iconUrl' => 'http://jira.example.com/images/icons/statuses/reopened.png',
                            'name' => 'Reopened',
                            'id' => '4',
                            'statusCategory' =>
                                array (
                                    'self' => 'http://jira.example.com/rest/api/2/statuscategory/2',
                                    'id' => 2,
                                    'key' => 'new',
                                    'colorName' => 'blue-gray',
                                    'name' => 'New',
                                ),
                        ),
                    'labels' =>
                        array (
                            0 => 'CR',
                            1 => 'FE',
                            2 => 'PROD',
                            3 => 'PhaseI',
                        ),
                    'workratio' => 20,
                    'environment' => NULL,
                    'aggregateprogress' =>
                        array (
                            'progress' => 1800,
                            'total' => 1800,
                            'percent' => 100,
                        ),
                    'fixVersions' =>
                        array (
                            0 =>
                                array (
                                    'self' => 'http://jira.example.com/rest/api/2/version/23506',
                                    'id' => '23506',
                                    'name' => 'v1.0.40',
                                    'archived' => false,
                                    'released' => true,
                                    'releaseDate' => '2014-10-24',
                                ),
                        ),
                    'resolution' =>
                        array (
                            'self' => 'http://jira.example.com/rest/api/2/resolution/1',
                            'id' => '1',
                            'description' => 'Afix for this issue is checked into the tree and tested.',
                            'name' => 'Fixed',
                        ),
                    'creator' =>
                        array (
                            'self' => 'http://jira.example.com/rest/api/2/user?username=test.er',
                            'name' => 'test.er',
                            'emailAddress' => 'lara.coppes@hastens.se',
                            'avatarUrls' =>
                                array (
                                    '16x16' => 'http://jira.example.com/secure/useravatar?size=xsmall&ownerId=test.er&avatarId=19509',
                                    '24x24' => 'http://jira.example.com/secure/useravatar?size=small&ownerId=test.er&avatarId=19509',
                                    '32x32' => 'http://jira.example.com/secure/useravatar?size=medium&ownerId=test.er&avatarId=19509',
                                    '48x48' => 'http://jira.example.com/secure/useravatar?ownerId=test.er&avatarId=19509',
                                ),
                            'displayName' => 'Test Er',
                            'active' => true,
                        ),
                    'aggregatetimeoriginalestimate' => 9000,
                    'assignee' =>
                        array (
                            'self' => 'http://jira.example.com/rest/api/2/user?username=test.er',
                            'name' => 'test.er',
                            'emailAddress' => 'lara.coppes@example.com',
                            'avatarUrls' =>
                                array (
                                    '16x16' => 'http://jira.example.com/secure/useravatar?size=xsmall&ownerId=test.er&avatarId=19509',
                                    '24x24' => 'http://jira.example.com/secure/useravatar?size=small&ownerId=test.er&avatarId=19509',
                                    '32x32' => 'http://jira.example.com/secure/useravatar?size=medium&ownerId=test.er&avatarId=19509',
                                    '48x48' => 'http://jira.example.com/secure/useravatar?ownerId=test.er&avatarId=19509',
                                ),
                            'displayName' => 'Test Er',
                            'active' => true,
                        ),
                    'aggregatetimeestimate' => 0,
                    'versions' =>
                        array (
                            0 =>
                                array (
                                    'self' => 'http://jira.example.com/rest/api/2/version/23411',
                                    'id' => '23411',
                                    'name' => 'v1.0.39',
                                    'archived' => false,
                                    'released' => true,
                                    'releaseDate' => '2014-10-22',
                                ),
                        ),
                    'aggregatetimespent' => 1800,
                ),
            'changelog' =>
                array (
                    'startAt' => 0,
                    'maxResults' => 20,
                    'total' => 20,
                    'histories' =>
                        array (
                                array (
                                    'id' => '2314516',
                                    'author' =>
                                        array (
                                            'self' => 'http://jira.example.com/rest/api/2/user?username=test.er',
                                            'name' => 'test.er',
                                            'emailAddress' => 'lara.coppes@hastens.se',
                                            'avatarUrls' =>
                                                array (
                                                    '16x16' => 'http://jira.example.com/secure/useravatar?size=xsmall&ownerId=test.er&avatarId=19509',
                                                    '24x24' => 'http://jira.example.com/secure/useravatar?size=small&ownerId=test.er&avatarId=19509',
                                                    '32x32' => 'http://jira.example.com/secure/useravatar?size=medium&ownerId=test.er&avatarId=19509',
                                                    '48x48' => 'http://jira.example.com/secure/useravatar?ownerId=test.er&avatarId=19509',
                                                ),
                                            'displayName' => 'Test Er',
                                            'active' => true,
                                        ),
                                    'created' => '2014-10-20T01:45:24.000-0700',
                                    'items' =>
                                        array (
                                            0 =>
                                                array (
                                                    'field' => 'priority',
                                                    'fieldtype' => 'jira',
                                                    'from' => NULL,
                                                    'fromString' => NULL,
                                                    'to' => '4',
                                                    'toString' => 'Minor',
                                                ),
                                            1 =>
                                                array (
                                                    'field' => 'duedate',
                                                    'fieldtype' => 'jira',
                                                    'from' => NULL,
                                                    'fromString' => NULL,
                                                    'to' => '2014-11-19',
                                                    'toString' => '2014-11-19 01:45:24.28',
                                                ),
                                            2 =>
                                                array (
                                                    'field' => 'Portal Key',
                                                    'fieldtype' => 'custom',
                                                    'from' => NULL,
                                                    'fromString' => NULL,
                                                    'to' => NULL,
                                                    'toString' => 'HASCP-1116',
                                                ),
                                        ),
                                ),
                                array (
                                    'id' => '2314793',
                                    'author' =>
                                        array (
                                            'self' => 'http://jira.example.com/rest/api/2/user?username=testo.bar',
                                            'name' => 'testo.bar',
                                            'emailAddress' => 'testo.bar@example.com',
                                            'avatarUrls' =>
                                                array (
                                                    '16x16' => 'http://jira.example.com/secure/useravatar?size=xsmall&ownerId=testo.bar&avatarId=14606',
                                                    '24x24' => 'http://jira.example.com/secure/useravatar?size=small&ownerId=testo.bar&avatarId=14606',
                                                    '32x32' => 'http://jira.example.com/secure/useravatar?size=medium&ownerId=testo.bar&avatarId=14606',
                                                    '48x48' => 'http://jira.example.com/secure/useravatar?ownerId=testo.bar&avatarId=14606',
                                                ),
                                            'displayName' => 'Testo Bar',
                                            'active' => true,
                                        ),
                                    'created' => '2014-10-20T02:16:56.000-0700',
                                    'items' =>
                                        array (
                                            0 =>
                                                array (
                                                    'field' => 'labels',
                                                    'fieldtype' => 'jira',
                                                    'from' => NULL,
                                                    'fromString' => NULL,
                                                    'to' => NULL,
                                                    'toString' => 'CR FE PhaseI',
                                                ),
                                        ),
                                ),
                                array (
                                    'id' => '2314798',
                                    'author' =>
                                        array (
                                            'self' => 'http://jira.example.com/rest/api/2/user?username=testo.bar',
                                            'name' => 'testo.bar',
                                            'emailAddress' => 'testo.bar@example.com',
                                            'avatarUrls' =>
                                                array (
                                                    '16x16' => 'http://jira.example.com/secure/useravatar?size=xsmall&ownerId=testo.bar&avatarId=14606',
                                                    '24x24' => 'http://jira.example.com/secure/useravatar?size=small&ownerId=testo.bar&avatarId=14606',
                                                    '32x32' => 'http://jira.example.com/secure/useravatar?size=medium&ownerId=testo.bar&avatarId=14606',
                                                    '48x48' => 'http://jira.example.com/secure/useravatar?ownerId=testo.bar&avatarId=14606',
                                                ),
                                            'displayName' => 'Testo Bar',
                                            'active' => true,
                                        ),
                                    'created' => '2014-10-20T02:17:12.000-0700',
                                    'items' =>
                                        array (
                                            0 =>
                                                array (
                                                    'field' => 'Sprint',
                                                    'fieldtype' => 'custom',
                                                    'from' => NULL,
                                                    'fromString' => NULL,
                                                    'to' => '1095',
                                                    'toString' => 'Sprint 1 (ph1)',
                                                ),
                                            1 =>
                                                array (
                                                    'field' => 'assignee',
                                                    'fieldtype' => 'jira',
                                                    'from' => 'testo.bar',
                                                    'fromString' => 'Testo Bar',
                                                    'to' => 'test.er22',
                                                    'toString' => 'Test Er22',
                                                ),
                                        ),
                                ),
                                array (
                                    'id' => '2314857',
                                    'author' =>
                                        array (
                                            'self' => 'http://jira.example.com/rest/api/2/user?username=testo.bar',
                                            'name' => 'testo.bar',
                                            'emailAddress' => 'testo.bar@example.com',
                                            'avatarUrls' =>
                                                array (
                                                    '16x16' => 'http://jira.example.com/secure/useravatar?size=xsmall&ownerId=testo.bar&avatarId=14606',
                                                    '24x24' => 'http://jira.example.com/secure/useravatar?size=small&ownerId=testo.bar&avatarId=14606',
                                                    '32x32' => 'http://jira.example.com/secure/useravatar?size=medium&ownerId=testo.bar&avatarId=14606',
                                                    '48x48' => 'http://jira.example.com/secure/useravatar?ownerId=testo.bar&avatarId=14606',
                                                ),
                                            'displayName' => 'Testo Bar',
                                            'active' => true,
                                        ),
                                    'created' => '2014-10-20T02:25:12.000-0700',
                                    'items' =>
                                        array (
                                            0 =>
                                                array (
                                                    'field' => 'assignee',
                                                    'fieldtype' => 'jira',
                                                    'from' => 'test.er22',
                                                    'fromString' => 'Test Er22',
                                                    'to' => 'test.er',
                                                    'toString' => 'Test Er',
                                                ),
                                        ),
                                ),
                                array (
                                    'id' => '2317093',
                                    'author' =>
                                        array (
                                            'self' => 'http://jira.example.com/rest/api/2/user?username=testo.bar',
                                            'name' => 'testo.bar',
                                            'emailAddress' => 'testo.bar@example.com',
                                            'avatarUrls' =>
                                                array (
                                                    '16x16' => 'http://jira.example.com/secure/useravatar?size=xsmall&ownerId=testo.bar&avatarId=14606',
                                                    '24x24' => 'http://jira.example.com/secure/useravatar?size=small&ownerId=testo.bar&avatarId=14606',
                                                    '32x32' => 'http://jira.example.com/secure/useravatar?size=medium&ownerId=testo.bar&avatarId=14606',
                                                    '48x48' => 'http://jira.example.com/secure/useravatar?ownerId=testo.bar&avatarId=14606',
                                                ),
                                            'displayName' => 'Testo Bar',
                                            'active' => true,
                                        ),
                                    'created' => '2014-10-20T07:50:23.000-0700',
                                    'items' =>
                                        array (
                                            0 =>
                                                array (
                                                    'field' => 'assignee',
                                                    'fieldtype' => 'jira',
                                                    'from' => 'test.er',
                                                    'fromString' => 'Test Er',
                                                    'to' => 'test.er22',
                                                    'toString' => 'Test Er22',
                                                ),
                                        ),
                                ),
                                array (
                                    'id' => '2327975',
                                    'author' =>
                                        array (
                                            'self' => 'http://jira.example.com/rest/api/2/user?username=test.er22',
                                            'name' => 'test.er22',
                                            'emailAddress' => 'test.er22@example.com',
                                            'avatarUrls' =>
                                                array (
                                                    '16x16' => 'http://jira.example.com/secure/useravatar?size=xsmall&ownerId=test.er22&avatarId=15300',
                                                    '24x24' => 'http://jira.example.com/secure/useravatar?size=small&ownerId=test.er22&avatarId=15300',
                                                    '32x32' => 'http://jira.example.com/secure/useravatar?size=medium&ownerId=test.er22&avatarId=15300',
                                                    '48x48' => 'http://jira.example.com/secure/useravatar?ownerId=test.er22&avatarId=15300',
                                                ),
                                            'displayName' => 'Test Er22',
                                            'active' => true,
                                        ),
                                    'created' => '2014-10-21T07:58:45.000-0700',
                                    'items' =>
                                        array (
                                            0 =>
                                                array (
                                                    'field' => 'timeoriginalestimate',
                                                    'fieldtype' => 'jira',
                                                    'from' => NULL,
                                                    'fromString' => NULL,
                                                    'to' => '9000',
                                                    'toString' => '9000',
                                                ),
                                        ),
                                ),
                                array (
                                    'id' => '2327977',
                                    'author' =>
                                        array (
                                            'self' => 'http://jira.example.com/rest/api/2/user?username=test.er22',
                                            'name' => 'test.er22',
                                            'emailAddress' => 'test.er22@example.com',
                                            'avatarUrls' =>
                                                array (
                                                    '16x16' => 'http://jira.example.com/secure/useravatar?size=xsmall&ownerId=test.er22&avatarId=15300',
                                                    '24x24' => 'http://jira.example.com/secure/useravatar?size=small&ownerId=test.er22&avatarId=15300',
                                                    '32x32' => 'http://jira.example.com/secure/useravatar?size=medium&ownerId=test.er22&avatarId=15300',
                                                    '48x48' => 'http://jira.example.com/secure/useravatar?ownerId=test.er22&avatarId=15300',
                                                ),
                                            'displayName' => 'Test Er22',
                                            'active' => true,
                                        ),
                                    'created' => '2014-10-21T07:58:46.000-0700',
                                    'items' =>
                                        array (
                                            0 =>
                                                array (
                                                    'field' => 'timespent',
                                                    'fieldtype' => 'jira',
                                                    'from' => NULL,
                                                    'fromString' => NULL,
                                                    'to' => '9000',
                                                    'toString' => '9000',
                                                ),
                                            1 =>
                                                array (
                                                    'field' => 'WorklogId',
                                                    'fieldtype' => 'jira',
                                                    'from' => '516010',
                                                    'fromString' => '516010',
                                                    'to' => NULL,
                                                    'toString' => NULL,
                                                ),
                                            2 =>
                                                array (
                                                    'field' => 'timeestimate',
                                                    'fieldtype' => 'jira',
                                                    'from' => NULL,
                                                    'fromString' => NULL,
                                                    'to' => '0',
                                                    'toString' => '0',
                                                ),
                                        ),
                                ),
                                array (
                                    'id' => '2328096',
                                    'author' =>
                                        array (
                                            'self' => 'http://jira.example.com/rest/api/2/user?username=test.er22',
                                            'name' => 'test.er22',
                                            'emailAddress' => 'test.er22@example.com',
                                            'avatarUrls' =>
                                                array (
                                                    '16x16' => 'http://jira.example.com/secure/useravatar?size=xsmall&ownerId=test.er22&avatarId=15300',
                                                    '24x24' => 'http://jira.example.com/secure/useravatar?size=small&ownerId=test.er22&avatarId=15300',
                                                    '32x32' => 'http://jira.example.com/secure/useravatar?size=medium&ownerId=test.er22&avatarId=15300',
                                                    '48x48' => 'http://jira.example.com/secure/useravatar?ownerId=test.er22&avatarId=15300',
                                                ),
                                            'displayName' => 'Test Er22',
                                            'active' => true,
                                        ),
                                    'created' => '2014-10-21T08:04:37.000-0700',
                                    'items' =>
                                        array (
                                            0 =>
                                                array (
                                                    'field' => 'status',
                                                    'fieldtype' => 'jira',
                                                    'from' => '1',
                                                    'fromString' => 'Open',
                                                    'to' => '5',
                                                    'toString' => 'Resolved',
                                                ),
                                            1 =>
                                                array (
                                                    'field' => 'labels',
                                                    'fieldtype' => 'jira',
                                                    'from' => NULL,
                                                    'fromString' => 'CR FE PhaseI',
                                                    'to' => NULL,
                                                    'toString' => 'CR FEPROD PhaseI',
                                                ),
                                            2 =>
                                                array (
                                                    'field' => 'assignee',
                                                    'fieldtype' => 'jira',
                                                    'from' => 'test.er22',
                                                    'fromString' => 'Test Er22',
                                                    'to' => 'nobody-magento',
                                                    'toString' => 'Nobody Magento',
                                                ),
                                            3 =>
                                                array (
                                                    'field' => 'Fix Version',
                                                    'fieldtype' => 'jira',
                                                    'from' => NULL,
                                                    'fromString' => NULL,
                                                    'to' => '23411',
                                                    'toString' => 'v1.0.39',
                                                ),
                                            4 =>
                                                array (
                                                    'field' => 'resolution',
                                                    'fieldtype' => 'jira',
                                                    'from' => NULL,
                                                    'fromString' => NULL,
                                                    'to' => '1',
                                                    'toString' => 'Fixed',
                                                ),
                                        ),
                                ),
                                array (
                                    'id' => '2328173',
                                    'author' =>
                                        array (
                                            'self' => 'http://jira.example.com/rest/api/2/user?username=test.er22',
                                            'name' => 'test.er22',
                                            'emailAddress' => 'test.er22@example.com',
                                            'avatarUrls' =>
                                                array (
                                                    '16x16' => 'http://jira.example.com/secure/useravatar?size=xsmall&ownerId=test.er22&avatarId=15300',
                                                    '24x24' => 'http://jira.example.com/secure/useravatar?size=small&ownerId=test.er22&avatarId=15300',
                                                    '32x32' => 'http://jira.example.com/secure/useravatar?size=medium&ownerId=test.er22&avatarId=15300',
                                                    '48x48' => 'http://jira.example.com/secure/useravatar?ownerId=test.er22&avatarId=15300',
                                                ),
                                            'displayName' => 'Test Er22',
                                            'active' => true,
                                        ),
                                    'created' => '2014-10-21T08:06:03.000-0700',
                                    'items' =>
                                        array (
                                            0 =>
                                                array (
                                                    'field' => 'status',
                                                    'fieldtype' => 'jira',
                                                    'from' => '5',
                                                    'fromString' => 'Resolved',
                                                    'to' => '10065',
                                                    'toString' => 'Reviewed',
                                                ),
                                        ),
                                ),
                                array (
                                    'id' => '2328179',
                                    'author' =>
                                        array (
                                            'self' => 'http://jira.example.com/rest/api/2/user?username=test.er22',
                                            'name' => 'test.er22',
                                            'emailAddress' => 'test.er22@example.com',
                                            'avatarUrls' =>
                                                array (
                                                    '16x16' => 'http://jira.example.com/secure/useravatar?size=xsmall&ownerId=test.er22&avatarId=15300',
                                                    '24x24' => 'http://jira.example.com/secure/useravatar?size=small&ownerId=test.er22&avatarId=15300',
                                                    '32x32' => 'http://jira.example.com/secure/useravatar?size=medium&ownerId=test.er22&avatarId=15300',
                                                    '48x48' => 'http://jira.example.com/secure/useravatar?ownerId=test.er22&avatarId=15300',
                                                ),
                                            'displayName' => 'Test Er22',
                                            'active' => true,
                                        ),
                                    'created' => '2014-10-21T08:06:15.000-0700',
                                    'items' =>
                                        array (
                                            0 =>
                                                array (
                                                    'field' => 'status',
                                                    'fieldtype' => 'jira',
                                                    'from' => '10065',
                                                    'fromString' => 'Reviewed',
                                                    'to' => '10066',
                                                    'toString' => 'Ready for Verification',
                                                ),
                                        ),
                                ),
                                array (
                                    'id' => '2330092',
                                    'author' =>
                                        array (
                                            'self' => 'http://jira.example.com/rest/api/2/user?username=test.er',
                                            'name' => 'test.er',
                                            'emailAddress' => 'lara.coppes@hastens.se',
                                            'avatarUrls' =>
                                                array (
                                                    '16x16' => 'http://jira.example.com/secure/useravatar?size=xsmall&ownerId=test.er&avatarId=19509',
                                                    '24x24' => 'http://jira.example.com/secure/useravatar?size=small&ownerId=test.er&avatarId=19509',
                                                    '32x32' => 'http://jira.example.com/secure/useravatar?size=medium&ownerId=test.er&avatarId=19509',
                                                    '48x48' => 'http://jira.example.com/secure/useravatar?ownerId=test.er&avatarId=19509',
                                                ),
                                            'displayName' => 'Test Er',
                                            'active' => true,
                                        ),
                                    'created' => '2014-10-22T00:01:10.000-0700',
                                    'items' =>
                                        array (
                                            0 =>
                                                array (
                                                    'field' => 'duedate',
                                                    'fieldtype' => 'jira',
                                                    'from' => '2014-11-19',
                                                    'fromString' => '2014-11-19 01:45:24.0',
                                                    'to' => '2014-11-21',
                                                    'toString' => '2014-11-21 00:01:10.255',
                                                ),
                                        ),
                                ),
                                array (
                                    'id' => '2330104',
                                    'author' =>
                                        array (
                                            'self' => 'http://jira.example.com/rest/api/2/user?username=test.er',
                                            'name' => 'test.er',
                                            'emailAddress' => 'lara.coppes@hastens.se',
                                            'avatarUrls' =>
                                                array (
                                                    '16x16' => 'http://jira.example.com/secure/useravatar?size=xsmall&ownerId=test.er&avatarId=19509',
                                                    '24x24' => 'http://jira.example.com/secure/useravatar?size=small&ownerId=test.er&avatarId=19509',
                                                    '32x32' => 'http://jira.example.com/secure/useravatar?size=medium&ownerId=test.er&avatarId=19509',
                                                    '48x48' => 'http://jira.example.com/secure/useravatar?ownerId=test.er&avatarId=19509',
                                                ),
                                            'displayName' => 'Test Er',
                                            'active' => true,
                                        ),
                                    'created' => '2014-10-22T00:06:38.000-0700',
                                    'items' =>
                                        array (
                                            0 =>
                                                array (
                                                    'field' => 'duedate',
                                                    'fieldtype' => 'jira',
                                                    'from' => '2014-11-19',
                                                    'fromString' => '2014-11-19 01:45:24.0',
                                                    'to' => '2014-11-21',
                                                    'toString' => '2014-11-21 00:06:38.844',
                                                ),
                                        ),
                                ),
                                array (
                                    'id' => '2330106',
                                    'author' =>
                                        array (
                                            'self' => 'http://jira.example.com/rest/api/2/user?username=test.er',
                                            'name' => 'test.er',
                                            'emailAddress' => 'lara.coppes@hastens.se',
                                            'avatarUrls' =>
                                                array (
                                                    '16x16' => 'http://jira.example.com/secure/useravatar?size=xsmall&ownerId=test.er&avatarId=19509',
                                                    '24x24' => 'http://jira.example.com/secure/useravatar?size=small&ownerId=test.er&avatarId=19509',
                                                    '32x32' => 'http://jira.example.com/secure/useravatar?size=medium&ownerId=test.er&avatarId=19509',
                                                    '48x48' => 'http://jira.example.com/secure/useravatar?ownerId=test.er&avatarId=19509',
                                                ),
                                            'displayName' => 'Test Er',
                                            'active' => true,
                                        ),
                                    'created' => '2014-10-22T00:06:51.000-0700',
                                    'items' =>
                                        array (
                                            0 =>
                                                array (
                                                    'field' => 'duedate',
                                                    'fieldtype' => 'jira',
                                                    'from' => '2014-11-19',
                                                    'fromString' => '2014-11-19 01:45:24.0',
                                                    'to' => '2014-11-21',
                                                    'toString' => '2014-11-21 00:06:51.014',
                                                ),
                                        ),
                                ),
                                array (
                                    'id' => '2334269',
                                    'author' =>
                                        array (
                                            'self' => 'http://jira.example.com/rest/api/2/user?username=boan.lain',
                                            'name' => 'boan.lain',
                                            'emailAddress' => 'boan.lain@example.com',
                                            'avatarUrls' =>
                                                array (
                                                    '16x16' => 'http://jira.example.com/secure/useravatar?size=xsmall&ownerId=boan.lain&avatarId=13102',
                                                    '24x24' => 'http://jira.example.com/secure/useravatar?size=small&ownerId=boan.lain&avatarId=13102',
                                                    '32x32' => 'http://jira.example.com/secure/useravatar?size=medium&ownerId=boan.lain&avatarId=13102',
                                                    '48x48' => 'http://jira.example.com/secure/useravatar?ownerId=boan.lain&avatarId=13102',
                                                ),
                                            'displayName' => 'Boan Lain',
                                            'active' => true,
                                        ),
                                    'created' => '2014-10-22T05:32:47.000-0700',
                                    'items' =>
                                        array (
                                            0 =>
                                                array (
                                                    'field' => 'status',
                                                    'fieldtype' => 'jira',
                                                    'from' => '10066',
                                                    'fromString' => 'Ready for Verification',
                                                    'to' => '10024',
                                                    'toString' => 'Verified on QA',
                                                ),
                                        ),
                                ),
                                array (
                                    'id' => '2334272',
                                    'author' =>
                                        array (
                                            'self' => 'http://jira.example.com/rest/api/2/user?username=boan.lain',
                                            'name' => 'boan.lain',
                                            'emailAddress' => 'boan.lain@example.com',
                                            'avatarUrls' =>
                                                array (
                                                    '16x16' => 'http://jira.example.com/secure/useravatar?size=xsmall&ownerId=boan.lain&avatarId=13102',
                                                    '24x24' => 'http://jira.example.com/secure/useravatar?size=small&ownerId=boan.lain&avatarId=13102',
                                                    '32x32' => 'http://jira.example.com/secure/useravatar?size=medium&ownerId=boan.lain&avatarId=13102',
                                                    '48x48' => 'http://jira.example.com/secure/useravatar?ownerId=boan.lain&avatarId=13102',
                                                ),
                                            'displayName' => 'Boan Lain',
                                            'active' => true,
                                        ),
                                    'created' => '2014-10-22T05:32:53.000-0700',
                                    'items' =>
                                        array (
                                            0 =>
                                                array (
                                                    'field' => 'status',
                                                    'fieldtype' => 'jira',
                                                    'from' => '10024',
                                                    'fromString' => 'Verified on QA',
                                                    'to' => '10025',
                                                    'toString' => 'Verified on STG',
                                                ),
                                        ),
                                ),
                                array (
                                    'id' => '2337657',
                                    'author' =>
                                        array (
                                            'self' => 'http://jira.example.com/rest/api/2/user?username=test.er22',
                                            'name' => 'test.er22',
                                            'emailAddress' => 'test.er22@example.com',
                                            'avatarUrls' =>
                                                array (
                                                    '16x16' => 'http://jira.example.com/secure/useravatar?size=xsmall&ownerId=test.er22&avatarId=15300',
                                                    '24x24' => 'http://jira.example.com/secure/useravatar?size=small&ownerId=test.er22&avatarId=15300',
                                                    '32x32' => 'http://jira.example.com/secure/useravatar?size=medium&ownerId=test.er22&avatarId=15300',
                                                    '48x48' => 'http://jira.example.com/secure/useravatar?ownerId=test.er22&avatarId=15300',
                                                ),
                                            'displayName' => 'Test Er22',
                                            'active' => true,
                                        ),
                                    'created' => '2014-10-22T09:39:43.000-0700',
                                    'items' =>
                                        array (
                                            0 =>
                                                array (
                                                    'field' => 'timeestimate',
                                                    'fieldtype' => 'jira',
                                                    'from' => NULL,
                                                    'fromString' => NULL,
                                                    'to' => '0',
                                                    'toString' => '0',
                                                ),
                                            1 =>
                                                array (
                                                    'field' => 'timespent',
                                                    'fieldtype' => 'jira',
                                                    'from' => NULL,
                                                    'fromString' => NULL,
                                                    'to' => '1800',
                                                    'toString' => '1800',
                                                ),
                                            2 =>
                                                array (
                                                    'field' => 'WorklogId',
                                                    'fieldtype' => 'jira',
                                                    'from' => '517126',
                                                    'fromString' => '517126',
                                                    'to' => NULL,
                                                    'toString' => NULL,
                                                ),
                                        ),
                                ),
                                array (
                                    'id' => '2340641',
                                    'author' =>
                                        array (
                                            'self' => 'http://jira.example.com/rest/api/2/user?username=testo.bar',
                                            'name' => 'testo.bar',
                                            'emailAddress' => 'testo.bar@example.com',
                                            'avatarUrls' =>
                                                array (
                                                    '16x16' => 'http://jira.example.com/secure/useravatar?size=xsmall&ownerId=testo.bar&avatarId=14606',
                                                    '24x24' => 'http://jira.example.com/secure/useravatar?size=small&ownerId=testo.bar&avatarId=14606',
                                                    '32x32' => 'http://jira.example.com/secure/useravatar?size=medium&ownerId=testo.bar&avatarId=14606',
                                                    '48x48' => 'http://jira.example.com/secure/useravatar?ownerId=testo.bar&avatarId=14606',
                                                ),
                                            'displayName' => 'Testo Bar',
                                            'active' => true,
                                        ),
                                    'created' => '2014-10-23T03:49:16.000-0700',
                                    'items' =>
                                        array (
                                            0 =>
                                                array (
                                                    'field' => 'status',
                                                    'fieldtype' => 'jira',
                                                    'from' => '10025',
                                                    'fromString' => 'Verified on STG',
                                                    'to' => '6',
                                                    'toString' => 'Closed',
                                                ),
                                        ),
                                ),
                                array (
                                    'id' => '2349410',
                                    'author' =>
                                        array (
                                            'self' => 'http://jira.example.com/rest/api/2/user?username=agat.tester',
                                            'name' => 'agat.tester',
                                            'emailAddress' => 'agat.tester@example.com',
                                            'avatarUrls' =>
                                                array (
                                                    '16x16' => 'http://jira.example.com/secure/useravatar?size=xsmall&ownerId=agat.tester&avatarId=17204',
                                                    '24x24' => 'http://jira.example.com/secure/useravatar?size=small&ownerId=agat.tester&avatarId=17204',
                                                    '32x32' => 'http://jira.example.com/secure/useravatar?size=medium&ownerId=agat.tester&avatarId=17204',
                                                    '48x48' => 'http://jira.example.com/secure/useravatar?ownerId=agat.tester&avatarId=17204',
                                                ),
                                            'displayName' => 'Agat Tester',
                                            'active' => true,
                                        ),
                                    'created' => '2014-10-24T06:27:08.000-0700',
                                    'items' =>
                                        array (
                                            0 =>
                                                array (
                                                    'field' => 'status',
                                                    'fieldtype' => 'jira',
                                                    'from' => '6',
                                                    'fromString' => 'Closed',
                                                    'to' => '4',
                                                    'toString' => 'Reopened',
                                                ),
                                            1 =>
                                                array (
                                                    'field' => 'status',
                                                    'fieldtype' => 'jira',
                                                    'from' => '4',
                                                    'fromString' => 'Reopened',
                                                    'to' => '4',
                                                    'toString' => 'Reopened',
                                                ),
                                            2 =>
                                                array (
                                                    'field' => 'status',
                                                    'fieldtype' => 'jira',
                                                    'from' => '4',
                                                    'fromString' => 'Reopened',
                                                    'to' => '4',
                                                    'toString' => 'Reopened',
                                                ),
                                        ),
                                ),
                                array (
                                    'id' => '2349413',
                                    'author' =>
                                        array (
                                            'self' => 'http://jira.example.com/rest/api/2/user?username=agat.tester',
                                            'name' => 'agat.tester',
                                            'emailAddress' => 'agat.tester@example.com',
                                            'avatarUrls' =>
                                                array (
                                                    '16x16' => 'http://jira.example.com/secure/useravatar?size=xsmall&ownerId=agat.tester&avatarId=17204',
                                                    '24x24' => 'http://jira.example.com/secure/useravatar?size=small&ownerId=agat.tester&avatarId=17204',
                                                    '32x32' => 'http://jira.example.com/secure/useravatar?size=medium&ownerId=agat.tester&avatarId=17204',
                                                    '48x48' => 'http://jira.example.com/secure/useravatar?ownerId=agat.tester&avatarId=17204',
                                                ),
                                            'displayName' => 'Agat Tester',
                                            'active' => true,
                                        ),
                                    'created' => '2014-10-24T06:27:18.000-0700',
                                    'items' =>
                                        array (
                                            0 =>
                                                array (
                                                    'field' => 'Version',
                                                    'fieldtype' => 'jira',
                                                    'from' => NULL,
                                                    'fromString' => NULL,
                                                    'to' => '23411',
                                                    'toString' => 'v1.0.39',
                                                ),
                                        ),
                                ),
                                array (
                                    'id' => '2349416',
                                    'author' =>
                                        array (
                                            'self' => 'http://jira.example.com/rest/api/2/user?username=agat.tester',
                                            'name' => 'agat.tester',
                                            'emailAddress' => 'agat.tester@example.com',
                                            'avatarUrls' =>
                                                array (
                                                    '16x16' => 'http://jira.example.com/secure/useravatar?size=xsmall&ownerId=agat.tester&avatarId=17204',
                                                    '24x24' => 'http://jira.example.com/secure/useravatar?size=small&ownerId=agat.tester&avatarId=17204',
                                                    '32x32' => 'http://jira.example.com/secure/useravatar?size=medium&ownerId=agat.tester&avatarId=17204',
                                                    '48x48' => 'http://jira.example.com/secure/useravatar?ownerId=agat.tester&avatarId=17204',
                                                ),
                                            'displayName' => 'Agat Tester',
                                            'active' => true,
                                        ),
                                    'created' => '2014-10-24T06:27:22.000-0700',
                                    'items' =>
                                        array (
                                            0 =>
                                                array (
                                                    'field' => 'Fix Version',
                                                    'fieldtype' => 'jira',
                                                    'from' => NULL,
                                                    'fromString' => NULL,
                                                    'to' => '23506',
                                                    'toString' => 'v1.0.40',
                                                ),
                                            1 =>
                                                array (
                                                    'field' => 'Fix Version',
                                                    'fieldtype' => 'jira',
                                                    'from' => '23411',
                                                    'fromString' => 'v1.0.39',
                                                    'to' => NULL,
                                                    'toString' => NULL,
                                                ),
                                        ),
                                ),
                        ),
                ),
        );
        parent::__construct();
    }

    public function getGenerateCode()
    {
        foreach ($this->_data['fields'] as $node => $data) {
            $description = str_replace('_', '', $node);
            $nodeCamel = str_replace('_', '', preg_replace('/_([a-z])/e', 'strtoupper("$0")', $node));
            $nodeCamelFunc = ucfirst($nodeCamel);
            $type = gettype($data);

            echo <<<NNN

    /**
     * Set field "$description"
     *
     * @param $type \$$nodeCamel
     * @return \$this
     */
    public function setField$nodeCamelFunc(\$$nodeCamel)
    {
        \$this->_data['fields']['$node'] = $$nodeCamel;
        return \$this;
    }

    /**
     * Get field "$description"
     *
     * @return $type
     */
    public function getField$nodeCamelFunc()
    {
        return \$this->_data['fields']['$node'];
    }

NNN;

        }
    }

    //region Main attributes
    /**
     * Set expand
     *
     * @param string $expand
     * @return $this
     */
    public function setExpand($expand)
    {
        $this->_data['expand'] = $expand;
        return $this;
    }

    /**
     * Get expand
     *
     * @return string
     */
    public function getExpand()
    {
        return $this->_data['expand'];
    }

    /**
     * Set id
     *
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->_data['id'] = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->_data['id'];
    }

    /**
     * Set self
     *
     * @param string $self
     * @return $this
     */
    public function setSelf($self)
    {
        $this->_data['self'] = $self;
        return $this;
    }

    /**
     * Get self
     *
     * @return string
     */
    public function getSelf()
    {
        return $this->_data['self'];
    }

    /**
     * Set key
     *
     * @param string $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->_data['key'] = $key;
        return $this;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->_data['key'];
    }

    /**
     * Set fields
     *
     * @param array $fields
     * @return $this
     */
    public function setFields($fields)
    {
        $this->_data['fields'] = $fields;
        return $this;
    }

    /**
     * Get fields
     *
     * @return array
     */
    public function getFields()
    {
        return $this->_data['fields'];
    }

    /**
     * Set changelog
     *
     * @param array $changelog
     * @return $this
     */
    public function setChangelog($changelog)
    {
        $this->_data['changelog'] = $changelog;
        return $this;
    }

    /**
     * Get changelog
     *
     * @return array
     */
    public function getChangelog()
    {
        return $this->_data['changelog'];
    }
    //endregion

    //region Field methods
    /**
     * Set field "summary"
     *
     * @param string $summary
     * @return $this
     */
    public function setFieldSummary($summary)
    {
        $this->_data['fields']['summary'] = $summary;
        return $this;
    }

    /**
     * Get field "summary"
     *
     * @return string
     */
    public function getFieldSummary()
    {
        return $this->_data['fields']['summary'];
    }

    /**
     * Set field "issuetype"
     *
     * @param array $issueType
     * @return $this
     */
    public function setFieldIssueType($issueType)
    {
        $this->_data['fields']['issuetype'] = $issueType;
        return $this;
    }

    /**
     * Get field "issuetype"
     *
     * @return array
     */
    public function getFieldIssueType()
    {
        return $this->_data['fields']['issuetype'];
    }

    /**
     * Set field "timespent"
     *
     * @param integer $timeSpent
     * @return $this
     */
    public function setFieldTimeSpent($timeSpent)
    {
        $this->_data['fields']['timespent'] = $timeSpent;
        return $this;
    }

    /**
     * Get field "timespent"
     *
     * @return integer
     */
    public function getFieldTimeSpent()
    {
        return $this->_data['fields']['timespent'];
    }

    /**
     * Set field "reporter"
     *
     * @param array $reporter
     * @return $this
     */
    public function setFieldReporter($reporter)
    {
        $this->_data['fields']['reporter'] = $reporter;
        return $this;
    }

    /**
     * Get field "reporter"
     *
     * @return array
     */
    public function getFieldReporter()
    {
        return $this->_data['fields']['reporter'];
    }

    /**
     * Set field "created"
     *
     * @param string $created
     * @return $this
     */
    public function setFieldCreated($created)
    {
        $this->_data['fields']['created'] = $created;
        return $this;
    }

    /**
     * Get field "created"
     *
     * @return string
     */
    public function getFieldCreated()
    {
        return $this->_data['fields']['created'];
    }

    /**
     * Set field "project"
     *
     * @param array $project
     * @return $this
     */
    public function setFieldProject($project)
    {
        $this->_data['fields']['project'] = $project;
        return $this;
    }

    /**
     * Get field "project"
     *
     * @return array
     */
    public function getFieldProject()
    {
        return $this->_data['fields']['project'];
    }

    /**
     * Set field "lastViewed"
     *
     * @param NULL $lastViewed
     * @return $this
     */
    public function setFieldLastViewed($lastViewed)
    {
        $this->_data['fields']['lastViewed'] = $lastViewed;
        return $this;
    }

    /**
     * Get field "lastViewed"
     *
     * @return NULL
     */
    public function getFieldLastViewed()
    {
        return $this->_data['fields']['lastViewed'];
    }

    /**
     * Set field "components"
     *
     * @param array $components
     * @return $this
     */
    public function setFieldComponents($components)
    {
        $this->_data['fields']['components'] = $components;
        return $this;
    }

    /**
     * Get field "components"
     *
     * @return array
     */
    public function getFieldComponents()
    {
        return $this->_data['fields']['components'];
    }

    /**
     * Set field "timeoriginalestimate"
     *
     * @param integer $timeOriginalEstimate
     * @return $this
     */
    public function setFieldTimeOriginalEstimate($timeOriginalEstimate)
    {
        $this->_data['fields']['timeoriginalestimate'] = $timeOriginalEstimate;
        return $this;
    }

    /**
     * Get field "timeoriginalestimate"
     *
     * @return integer
     */
    public function getFieldTimeOriginalEstimate()
    {
        return $this->_data['fields']['timeoriginalestimate'];
    }

    /**
     * Set field "votes"
     *
     * @param array $votes
     * @return $this
     */
    public function setFieldVotes($votes)
    {
        $this->_data['fields']['votes'] = $votes;
        return $this;
    }

    /**
     * Get field "votes"
     *
     * @return array
     */
    public function getFieldVotes()
    {
        return $this->_data['fields']['votes'];
    }

    /**
     * Set field "resolutiondate"
     *
     * @param string $resolutionDate
     * @return $this
     */
    public function setFieldResolutionDate($resolutionDate)
    {
        $this->_data['fields']['resolutiondate'] = $resolutionDate;
        return $this;
    }

    /**
     * Get field "resolutiondate"
     *
     * @return string
     */
    public function getFieldResolutionDate()
    {
        return $this->_data['fields']['resolutiondate'];
    }

    /**
     * Set field "duedate"
     *
     * @param string $dueDate
     * @return $this
     */
    public function setFieldDueDate($dueDate)
    {
        $this->_data['fields']['duedate'] = $dueDate;
        return $this;
    }

    /**
     * Get field "duedate"
     *
     * @return string
     */
    public function getFieldDueDate()
    {
        return $this->_data['fields']['duedate'];
    }

    /**
     * Set field "watches"
     *
     * @param array $watches
     * @return $this
     */
    public function setFieldWatches($watches)
    {
        $this->_data['fields']['watches'] = $watches;
        return $this;
    }

    /**
     * Get field "watches"
     *
     * @return array
     */
    public function getFieldWatches()
    {
        return $this->_data['fields']['watches'];
    }

    /**
     * Set field "timeestimate"
     *
     * @param integer $timeEstimate
     * @return $this
     */
    public function setFieldTimeEstimate($timeEstimate)
    {
        $this->_data['fields']['timeestimate'] = $timeEstimate;
        return $this;
    }

    /**
     * Get field "timeestimate"
     *
     * @return integer
     */
    public function getFieldTimeEstimate()
    {
        return $this->_data['fields']['timeestimate'];
    }

    /**
     * Set field "progress"
     *
     * @param array $progress
     * @return $this
     */
    public function setFieldProgress($progress)
    {
        $this->_data['fields']['progress'] = $progress;
        return $this;
    }

    /**
     * Get field "progress"
     *
     * @return array
     */
    public function getFieldProgress()
    {
        return $this->_data['fields']['progress'];
    }

    /**
     * Set field "updated"
     *
     * @param string $updated
     * @return $this
     */
    public function setFieldUpdated($updated)
    {
        $this->_data['fields']['updated'] = $updated;
        return $this;
    }

    /**
     * Get field "updated"
     *
     * @return string
     */
    public function getFieldUpdated()
    {
        return $this->_data['fields']['updated'];
    }

    /**
     * Set field "priority"
     *
     * @param array $priority
     * @return $this
     */
    public function setFieldPriority($priority)
    {
        $this->_data['fields']['priority'] = $priority;
        return $this;
    }

    /**
     * Get field "priority"
     *
     * @return array
     */
    public function getFieldPriority()
    {
        return $this->_data['fields']['priority'];
    }

    /**
     * Set field "description"
     *
     * @param string $description
     * @return $this
     */
    public function setFieldDescription($description)
    {
        $this->_data['fields']['description'] = $description;
        return $this;
    }

    /**
     * Get field "description"
     *
     * @return string
     */
    public function getFieldDescription()
    {
        return $this->_data['fields']['description'];
    }

    /**
     * Set field "issuelinks"
     *
     * @param array $issueLinks
     * @return $this
     */
    public function setFieldIssueLinks($issueLinks)
    {
        $this->_data['fields']['issuelinks'] = $issueLinks;
        return $this;
    }

    /**
     * Get field "issuelinks"
     *
     * @return array
     */
    public function getFieldIssueLinks()
    {
        return $this->_data['fields']['issuelinks'];
    }

    /**
     * Set field "subtasks"
     *
     * @param array $subtasks
     * @return $this
     */
    public function setFieldSubtasks($subtasks)
    {
        $this->_data['fields']['subtasks'] = $subtasks;
        return $this;
    }

    /**
     * Get field "subtasks"
     *
     * @return array
     */
    public function getFieldSubtasks()
    {
        return $this->_data['fields']['subtasks'];
    }

    /**
     * Set field "status"
     *
     * @param array $status
     * @return $this
     */
    public function setFieldStatus($status)
    {
        $this->_data['fields']['status'] = $status;
        return $this;
    }

    /**
     * Get field "status"
     *
     * @return array
     */
    public function getFieldStatus()
    {
        return $this->_data['fields']['status'];
    }

    /**
     * Set field "labels"
     *
     * @param array $labels
     * @return $this
     */
    public function setFieldLabels($labels)
    {
        $this->_data['fields']['labels'] = $labels;
        return $this;
    }

    /**
     * Get field "labels"
     *
     * @return array
     */
    public function getFieldLabels()
    {
        return $this->_data['fields']['labels'];
    }

    /**
     * Set field "workratio"
     *
     * @param integer $workRatio
     * @return $this
     */
    public function setFieldWorkRatio($workRatio)
    {
        $this->_data['fields']['workratio'] = $workRatio;
        return $this;
    }

    /**
     * Get field "workratio"
     *
     * @return integer
     */
    public function getFieldWorkRatio()
    {
        return $this->_data['fields']['workratio'];
    }

    /**
     * Set field "environment"
     *
     * @param NULL $environment
     * @return $this
     */
    public function setFieldEnvironment($environment)
    {
        $this->_data['fields']['environment'] = $environment;
        return $this;
    }

    /**
     * Get field "environment"
     *
     * @return NULL
     */
    public function getFieldEnvironment()
    {
        return $this->_data['fields']['environment'];
    }

    /**
     * Set field "aggregateprogress"
     *
     * @param array $aggregateProgress
     * @return $this
     */
    public function setFieldAggregateProgress($aggregateProgress)
    {
        $this->_data['fields']['aggregateprogress'] = $aggregateProgress;
        return $this;
    }

    /**
     * Get field "aggregateprogress"
     *
     * @return array
     */
    public function getFieldAggregateProgress()
    {
        return $this->_data['fields']['aggregateprogress'];
    }

    /**
     * Set field "fixVersions"
     *
     * @param array $fixVersions
     * @return $this
     */
    public function setFieldFixVersions($fixVersions)
    {
        $this->_data['fields']['fixVersions'] = $fixVersions;
        return $this;
    }

    /**
     * Get field "fixVersions"
     *
     * @return array
     */
    public function getFieldFixVersions()
    {
        return $this->_data['fields']['fixVersions'];
    }

    /**
     * Set field "resolution"
     *
     * @param array $resolution
     * @return $this
     */
    public function setFieldResolution($resolution)
    {
        $this->_data['fields']['resolution'] = $resolution;
        return $this;
    }

    /**
     * Get field "resolution"
     *
     * @return array
     */
    public function getFieldResolution()
    {
        return $this->_data['fields']['resolution'];
    }

    /**
     * Set field "creator"
     *
     * @param array $creator
     * @return $this
     */
    public function setFieldCreator($creator)
    {
        $this->_data['fields']['creator'] = $creator;
        return $this;
    }

    /**
     * Get field "creator"
     *
     * @return array
     */
    public function getFieldCreator()
    {
        return $this->_data['fields']['creator'];
    }

    /**
     * Set field "aggregatetimeoriginalestimate"
     *
     * @param integer $aggregateTimeOriginalEstimate
     * @return $this
     */
    public function setFieldAggregateTimeOriginalEstimate($aggregateTimeOriginalEstimate)
    {
        $this->_data['fields']['aggregatetimeoriginalestimate'] = $aggregateTimeOriginalEstimate;
        return $this;
    }

    /**
     * Get field "aggregatetimeoriginalestimate"
     *
     * @return integer
     */
    public function getFieldAggregateTimeOriginalEstimate()
    {
        return $this->_data['fields']['aggregatetimeoriginalestimate'];
    }

    /**
     * Set field "assignee"
     *
     * @param array $assignee
     * @return $this
     */
    public function setFieldAssignee($assignee)
    {
        $this->_data['fields']['assignee'] = $assignee;
        return $this;
    }

    /**
     * Get field "assignee"
     *
     * @return array
     */
    public function getFieldAssignee()
    {
        return $this->_data['fields']['assignee'];
    }

    /**
     * Set field "aggregatetimeestimate"
     *
     * @param integer $aggregateTimeEstimate
     * @return $this
     */
    public function setFieldAggregateTimeEstimate($aggregateTimeEstimate)
    {
        $this->_data['fields']['aggregatetimeestimate'] = $aggregateTimeEstimate;
        return $this;
    }

    /**
     * Get field "aggregatetimeestimate"
     *
     * @return integer
     */
    public function getFieldAggregateTimeEstimate()
    {
        return $this->_data['fields']['aggregatetimeestimate'];
    }

    /**
     * Set field "versions"
     *
     * @param array $versions
     * @return $this
     */
    public function setFieldVersions($versions)
    {
        $this->_data['fields']['versions'] = $versions;
        return $this;
    }

    /**
     * Get field "versions"
     *
     * @return array
     */
    public function getFieldVersions()
    {
        return $this->_data['fields']['versions'];
    }

    /**
     * Set field "aggregatetimespent"
     *
     * @param integer $aggregateTimeSpent
     * @return $this
     */
    public function setFieldAggregateTimeSpent($aggregateTimeSpent)
    {
        $this->_data['fields']['aggregatetimespent'] = $aggregateTimeSpent;
        return $this;
    }

    /**
     * Get field "aggregatetimespent"
     *
     * @return integer
     */
    public function getFieldAggregateTimeSpent()
    {
        return $this->_data['fields']['aggregatetimespent'];
    }
    //endregion
}
