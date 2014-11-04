<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 8/15/2014
 * Time: 3:11 AM
 */

namespace Jigit;

use chobie\Jira as Jira;
use Jigit\Config;
use Jigit\Dispatcher\InterfaceDispatcher;
use Jigit\Jira as JigitJira;

/**
 * Class Report
 *
 * @package Jigit
 */
class Report
{
    /**
     * Helpers
     *
     * @var JigitJira\Jql\Helper\DefaultHelper[]
     */
    protected $_helpers;

    /**
     * Dispatcher
     *
     * @var InterfaceDispatcher
     */
    protected $_dispatcher;

    /**
     * JQL item info
     *
     * @var Git
     */
    protected $_vcs;

    /**
     * JIRA API model
     *
     * @var Jira\Api
     */
    protected $_api;

    /**
     * Get VCS
     *
     * @return Git
     */
    public function getVcs()
    {
        return $this->_vcs;
    }

    /**
     * Set VCS
     *
     * @param Git $vcs
     * @return $this
     */
    public function setVcs($vcs)
    {
        $this->_vcs = $vcs;
        return $this;
    }

    /**
     * Get JIRA API model
     *
     * @return Jira\Api
     */
    public function getApi()
    {
        return $this->_api;
    }

    /**
     * Set JIRA API model
     *
     * @param Jira\Api $api
     * @return $this
     */
    public function setApi($api)
    {
        $this->_api = $api;
        return $this;
    }

    /**
     * Make report
     *
     * @param array            $jqlList
     */
    public function make(array $jqlList)
    {
        /**
         * Show found issues
         */
        foreach ($jqlList as $type => $jqlItem) {
            $jqlItem['type'] = $type;
            $this->_debugJqlItem($jqlItem);
            $helper = $this->_getJqlHelper($jqlItem);
            if ($helper->canProcessJql($type)) {
                continue;
            }
            $helper->process($type);
        }

        $found = false;
        foreach ($this->_helpers as $n =>  $helper) {
            if ($helper->hasFound()) {
                $found = true;
                $helper->addOutput($this->_getOutput());
            }
        }
        if (!$found) {
            $this->_getOutput()->enableDecorator();
            $this->_getOutput()->add('SUCCESS! Everything is OK');
            $this->_getOutput()->disableDecorator();
        }
    }

    /**
     * Make report of issues which should get some fixVersion.
     *
     * @param array    $jqlList
     */
    public function makePushReport(array $jqlList)
    {
        Config::addDebug('Found tags: ' . PHP_EOL . implode(', ', $this->_getVcsTags()));
        foreach ($jqlList as $type => $jqlItem) {
            $jqlItem['type'] = $type;
            $this->_getOutput()->enableDecorator();
            $this->_getOutput()->add($jqlItem['message']);
            $this->_getOutput()->disableDecorator();
            $this->_debugJqlItem($jqlItem);

            $result = $this->_queryPushTasksJql($jqlItem);
            $issues = $this->_getIssues($result);

            $issueKeys = array_keys($issues);
            if ($issueKeys) {
                $keys = implode(', ', $issueKeys);
                $this->_getOutput()->add('Found JIRA issues: ' . PHP_EOL . $keys);
            } else {
                $this->_getOutput()->add('No JIRA issues found.');
                continue;
            }

            $vcsIssues = $this->_findIssuesInVcs($issueKeys, $keys);

            $issuesNotFound = $issues;
            $issueKeyIdsInTags = array();
            foreach ($vcsIssues as $tag => $ids) {
                foreach ($ids as $id) {
                    $issueKeyIdsInTags[$id][] = $tag;
                    unset($issuesNotFound[$id]);
                }
            }

            $issueHelper = new JigitJira\IssueHelper();

            if ($issueKeyIdsInTags) {
                $this->_getOutput()->addDelimiter();
                $issueResults = array();
                //check exists fixVersion
                foreach ($issueKeyIdsInTags as $id => $versions) {
                    $affectsVersions = $issueHelper->getIssueAffectsVersions($issues[$id]);
                    $fixVersions = $issueHelper->getIssueAffectsVersions($issues[$id]);
                    foreach ($versions as $v => $version) {
                        if (in_array($version, $affectsVersions)
                            || in_array($version, $fixVersions)
                        ) {
                            //TODO check versions from parent
                            unset($versions[$v]);
                        }
                    }
                    if ($versions) {
                        $issueResults[] = "$id: " . implode(', ', $versions);
                    } else {
                        unset($issuesNotFound[$id]);
                    }
                }
                //add report
                if ($issueResults) {
                    $this->_getOutput()->add('Following issues should get version(s):');
                    foreach ($issueResults as $output) {
                        $this->_getOutput()->add($output);
                    }
                }
            }

            //add report about not exists issues
            if ($issuesNotFound) {
                $this->_getOutput()->addDelimiter();
                $this->_getOutput()->add('Following issues were not found in VCS:');
                $keys = implode(', ', array_keys($issuesNotFound));
                $this->_getOutput()->add($keys);
            }
        }
    }

    /**
     * Get output model
     *
     * @return Output
     */
    protected function _getOutput()
    {
        return Config::getInstance()->getData('output');
    }

    /**
     * Show JQL info on debug mode
     *
     * @param array $jqlItem
     * @return $this
     */
    protected function _debugJqlItem(array $jqlItem)
    {
        Config::addDebug("JQL: {$jqlItem['type']}: \n{$jqlItem['jql']}");
        return $this;
    }

    /**
     * Get Dispatcher
     *
     * @return InterfaceDispatcher
     */
    public function getDispatcher()
    {
        return $this->_dispatcher;
    }

    /**
     * Set Dispatcher
     *
     * @param InterfaceDispatcher $dispatcher
     * @return $this
     */
    public function setDispatcher(InterfaceDispatcher $dispatcher)
    {
        $this->_dispatcher = $dispatcher;
        return $this;
    }

    /**
     * Get issue keys
     *
     * @param Jira\Api\Result $result
     * @return array
     */
    protected function _getIssues($result)
    {
        $issueKeys = array();
        if ($result->getIssuesCount()) {
            /** @var Jira\Issue $issue */
            foreach ($result->getIssues() as $issue) {
                $issueKeys[$issue->getKey()] = $issue;
            }
        }
        return $issueKeys;
    }

    /**
     * Get VCS tags
     *
     * @return array
     */
    protected function _getVcsTags()
    {
        $tags   = $this->getVcs()->getTags();
        //add default branches to identify tasks there
        $tags[] = 'master';
        $tags[] = 'develop';
        return $tags;
    }

    /**
     * Find issues in VCS
     *
     * @param array $issueKeys
     * @param array $keys
     * @return array
     */
    protected function _findIssuesInVcs($issueKeys, $keys)
    {
        $vcsIssues         = array();
        $tags              = $this->_getVcsTags();
        $prevTag           = array_shift($tags);
        $issueKeyIdsString = implode('|', $issueKeys);
        $project           = Config\Project::getJiraProject();
        foreach ($tags as $tag) {
            Config::addDebug("Find issues between: $prevTag..$tag");
            $result = $this->getVcs()->runInProjectDir(
                "log $prevTag..$tag --no-merges --reverse --oneline | grep -E \"($issueKeyIdsString)\""
            );
            if ($result) {
                preg_match_all('/\s(' . $project . '-\d+)/', $result, $keys);
                $vcsIssues[$tag] = array_unique($keys[1]);
                Config::addDebug(
                    "Result GIT searching for tag '$tag': "
                    . PHP_EOL . implode(', ', $vcsIssues[$tag])
                );
            }
            $prevTag = $tag;
        }
        return $vcsIssues;
    }

    /**
     * Query push tasks
     *
     * @param array    $jqlItem
     * @return Jira\Api\Result
     */
    protected function _queryPushTasksJql($jqlItem)
    {
        /** @var Jira\Api\Result $result */
        $result = $this->getApi()->search($jqlItem['jql'], 0, 300/*, 'issuekey,ixVersion,affectedVersion'*/);
        return $result;
    }

    /**
     * Get JQL helper
     *
     * @param array $jql
     * @return JigitJira\Jql\Helper\DefaultHelper
     */
    protected function _getJqlHelper($jql)
    {
        $helperName = isset($jql['helper']) ? ucfirst($jql['helper']) : 'DefaultHelper';
        if (!isset($this->_helpers[$helperName])) {
            $className = '\Jigit\Jira\Jql\Helper\\' . $helperName;
            $this->_helpers[$helperName] = new $className();
            $this->_helpers[$helperName]
                ->setApi($this->_api)
                ->setVcs($this->_vcs);
        }
        $this->_helpers[$helperName]->setJql($jql);
        return $this->_helpers[$helperName];
    }
}
