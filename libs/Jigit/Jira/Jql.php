<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 12.08.2014
 * Time: 1:47
 */
namespace Jigit\Jira;

use Jigit\Config;
use Jigit\Config\Project as ConfigProject;
use Jigit\Exception;
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
     * @param array $vcsIssueKeys
     * @return array
     */
    public function getJqls($vcsIssueKeys)
    {
        if (is_array($vcsIssueKeys)) {
            $vcsIssueKeys = implode(', ', $vcsIssueKeys);
        }
        $jqls = $this->_getDraftJqls();
        $this->_setAlias('vcs_keys', $vcsIssueKeys);
        $this->_setAlias('project', ConfigProject::getJiraProject());

        $jqls = $this->_filterJqls(
            $jqls,
            $this->_getJqlAlias(),
            $this->getJqlsWhiteList()
        );
        return $jqls;
    }

    /**
     * Get JQLs white list
     *
     * @return array
     * @throws \Jigit\Exception
     */
    public function getJqlsWhiteList()
    {
        $filterJqlTypes = Config::getInstance()->getData('app/jira/jql/filter_jql');
        return $filterJqlTypes ? explode(',', $filterJqlTypes) : array();
    }

    /**
     * Get JQL settings
     *
     * @param string $key
     * @return string|null|array
     */
    protected function _getJqlAlias($key = null)
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
     * Set JQL alias
     *
     * @param string $name
     * @param string $value
     * @throws \Jigit\Exception
     * @return $this
     */
    protected function _setAlias($name, $value)
    {
        Config::getInstance()->setData(Config\Project::PATH_JIRA_JQL_ALIAS . '/' . $name, $value);
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

    /**
     * Filter JQLs
     *
     * @param array $jqls
     * @param array $filterData
     * @param array $whiteList
     * @throws Exception
     * @return array
     */
    protected function _filterJqls($jqls, $filterData, $whiteList)
    {
        $filter     = $this->_getFilter();
        foreach ($jqls as $type => &$item) {
            if (!$whiteList || in_array($type, $whiteList)) {
                if (!$item['jql']) {
                    throw new Exception("Empty JQL of type '$type'.");
                }
                $jql         = $this->_getDraftJqlString($item);
                $item['jql'] = $filter->filter($jql, $filterData);
            }
        }
        return $jqls;
    }
}
