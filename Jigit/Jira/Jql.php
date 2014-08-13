<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 12.08.2014
 * Time: 1:47
 */
namespace Jigit\Jira;

use \Jigit\Config\User as ConfigUser;

/**
 * Class Jql
 *
 * @package Jigit\Jira
 */
class Jql
{
    /**#@+
     * Query types
     */
    const TYPE_NOT_AFFECTS_CODE                     = 'notAffectsCodeWithFixVersion';
    const TYPE_WITHOUT_FIX_VERSION                  = 'inBranchWithoutFixVersion';
    const TYPE_WITHOUT_AFFECTED_VERSION             = 'inBranchWithoutAffectedVersion';
    const TYPE_OPEN_FOR_IN_PROGRESS_VERSION         = 'inBranchWithoutFixVersionNotDone';
    const TYPE_OPEN_TOP                             = 'openTopForInProgressVersion';
    const TYPE_OPEN_VERSION                         = 'openWithFixVersion';
    const TYPE_PARENT_HAS_COMMIT                    = 'parentIssueHasCommit';
    /**#@-*/

    /**
     * @var array
     */
    protected $_settings = null;

    public function getJqls()
    {
        $csv = new Jql\Reader\Csv();
        $jqls = $csv->toArray(JIGIT_ROOT . '/jqls.csv');
        foreach ($jqls as &$item) {
            $jql = &$item['jql'];
            $jql = str_replace('%project%', ConfigUser::getJiraProject(), $jql);
            $jql = str_replace(
                '%notAffectsCodeResolutions%',
                $this->_getJqlSettings('notAffectsCodeResolutions'), $jql
            );
            $jql = str_replace('%notAffectsCodeLabels%', $this->_getJqlSettings('notAffectsCodeLabels'), $jql);
            $jql = str_replace('%keys%', ConfigUser::getJiraConfig('git_keys'), $jql);
            $jql = str_replace('%targetFixVersion%', ConfigUser::getJiraTargetFixVersion(), $jql);
            $jql = str_replace('%activeSprints%', implode(',', ConfigUser::getJiraActiveSprints()), $jql);
            $defaultJql = $this->_getJqlSettings('default_jql');
            if ($defaultJql) {
                $jql .= ' AND (' . $defaultJql . ')';
            }
        }
        return $jqls;
    }

    /**
     * Get JQL settings
     *
     * @param string $key
     * @return string|null
     */
    protected function _getJqlSettings($key)
    {
        $csv = new Jql\Reader\Csv();
        if (null === $this->_settings) {
            $this->_settings = $csv->toAssocArray(JIGIT_ROOT . '/jqls-settings.csv');
            $custom          = $csv->toAssocArray(JIGIT_ROOT . '/jqls-settings-local.csv');
            $this->_settings = array_merge_recursive($this->_settings, $custom);
        }
        return isset($this->_settings[$key]) ? $this->_settings[$key] : null;
    }
}
