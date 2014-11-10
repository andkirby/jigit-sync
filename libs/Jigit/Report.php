<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 8/15/2014
 * Time: 3:11 AM
 */

namespace Jigit;

use Jigit\Config;
use Jigit\Dispatcher\InterfaceDispatcher;

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
     * @var Jira\Jql\Helper\DefaultHelper[]
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
            if (!$helper->canProcessJql($type)) {
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
     * Get JQL helper
     *
     * @param array $jql
     * @return Jira\Jql\Helper\DefaultHelper
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
