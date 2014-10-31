<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 12.08.2014
 * Time: 1:47
 */
namespace Jigit\Jira;

use Jigit\Config;
use \Jigit\Config\Project as ConfigProject;
use Jigit\Jira\Jql\Filter;

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
     * Source file for JQLs list
     *
     * @var string
     */
    protected $_namespace;

    /**
     * Jira settings
     *
     * @var array
     */
    protected $_settings = null;

    /**
     * Set JQLs source file
     *
     * @param string|null $namespace
     */
    public function __construct($namespace)
    {
        $this->_namespace = $namespace;
    }

    /**
     * Get JQLs
     *
     * @param array $gitKeys
     * @return array
     */
    public function getJqls($gitKeys)
    {
        if (is_array($gitKeys)) {
            implode(', ', $gitKeys);
        }
        $jqls = $this->_getDraftJqls();
        $filter = $this->_getFilter();
        $this->_setGitKeys($gitKeys);
        $filterData = $this->_getJqlSetting();
        $filterData['git_keys'] = $gitKeys;
        $filterData['project'] = ConfigProject::getJiraProject();;
        foreach ($jqls as &$item) {
            $jql = $this->_getDraftJqlString($item);
            $item['jql'] = $filter->filter($jql, $filterData);
        }
        return $jqls;
    }

    /**
     * Get JQL settings
     *
     * @param string $key
     * @return string|null|array
     */
    protected function _getJqlSetting($key = null)
    {
        if (null === $this->_settings) {
            $this->_settings = ConfigProject::getJiraJqlAliases();
            foreach ($this->_settings as $name => $value) {
                $this->_settings[$name . '_quoted'] = addslashes($value);
            }
        }
        if ($key) {
            return isset($this->_settings[$key]) ? $this->_settings[$key] : null;
        } else {
            return $this->_settings;
        }
    }

    /**
     * Set GIT keys
     *
     * @param array $gitKeys
     * @return $this
     */
    protected function _setGitKeys($gitKeys)
    {
        Config::getInstance()->setData('git_keys', $gitKeys);
        return $this;
    }

    /**
     * Get draft JQLs
     *
     * @return array
     */
    protected function _getDraftJqls()
    {
        return Config::getInstance()->getData('app/jira/jql/action/' . $this->_namespace);
    }

    /**
     * Get filter
     *
     * @return Filter
     */
    protected function _getFilter()
    {
        return new Filter();
    }

    /**
     * Get draft JQL string
     *
     * @param array $item
     * @return string
     */
    protected function _getDraftJqlString($item)
    {
        return $item['jql'] . ' %default%';
    }

    /**
     * Get JIRA JQLs config
     *
     * @return array
     */
    protected function _getJiraJqlConfig()
    {
        return ConfigProject::getJiraJqlAliases();
    }
}
