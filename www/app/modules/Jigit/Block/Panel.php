<?php
namespace Jigit\Block;

use Jigit\Block\Webix\Widget\Container\FormAbstract;
use Jigit\Config;
use Jigit\Git;
use Jigit\Helper\Base;
use Jigit\Run;
use Lib\Design\Renderer;

/**
 * Class Panel
 *
 * @package Jigit\Block
 */
class Panel extends FormAbstract
{
    /**
     * Helper
     *
     * @var Base
     */
    protected $_helper;

    /**
     * Prepare elements
     *
     * @return $this
     */
    protected function _prepareElements()
    {
        $this->_addElement(
            'action', 'combo', array(
                'placeholder' => 'Action',
                'required'    => true,
                'options'     => $this->getActions(),
            )
        );

        $project = Config\Project::getJiraProject();
        $this->_addElement(
            'project', 'combo', array(
                'placeholder' => 'Project',
                'required'    => true,
                'options'     => $this->getProjects(),
                'value'       => $project,
            )
        );

        $this->_addElement(
            'ver', 'combo', array(
                'placeholder' => 'Version',
                'disabled'    => !$project,
                'options'     => $this->getProjectVersions()
            )
        );
        $branches = $project ? $this->getBranches() : array();
        $this->_addElement(
            'low', 'combo', array(
                'placeholder' => 'Compare VCS Branch',
                'disabled'    => !$project,
                'options'     => $branches,
            )
        );
        $this->_addElement(
            'top', 'combo', array(
                'placeholder' => 'Target VCS Branch',
                'disabled'    => !$project,
                'options'     => $branches,
            )
        );
        $this->_addElement(
            'button_submit', 'button', array(
                'value' => 'Send',
                'type'  => 'form',
                'width' => 100,
            )
        );
        $this->_addElement(
            'fetch_remote', 'button', array(
                'value'    => 'Fetch Remote',
                'type'     => 'form',
                'disabled' => true,
                'width'    => 130,
            )
        );
        return $this;
    }

    /**
     * Get projects
     *
     * @return array
     */
    public function getProjects()
    {
        $helper   = $this->_getHelper();
        $projects = array();
        foreach ($helper->getProjects() as $project) {
            $projects[] = array(
                'id'    => $project,
                'value' => $project,
            );
        }
        return $projects;
    }

    /**
     * Get actions
     *
     * @return array
     */
    public function getActions()
    {
        return array(
            array(
                'id'       => 'report',
                'value'    => 'Report',
            ),
            array(
                'id'    => 'push-tasks',
                'value' => 'Push Tasks',
            )
        );
    }

    /**
     * Get branches
     *
     * @return array
     */
    public function getBranches()
    {
        return array_merge($this->getVcs()->getBranches(), $this->getVcs()->getTags());
    }

    /**
     * Get branches options
     *
     * @return array
     */
    public function getBranchesOptions()
    {
        $options = array();
        foreach ($this->getBranches() as $name) {
            $options[] = array(
                'id'    => $name,
                'value' => $name,
            );
        }
        return $options;
    }

    /**
     * Get form ID
     *
     * @return string
     */
    public function getId()
    {
        return 'jigit_panel';
    }

    /**
     * Get VCS
     *
     * @return Git
     * @throws \Zend_Exception
     */
    public function getVcs()
    {
        return $this->getRunner()->getVcs();
    }

    /**
     * Get runner
     *
     * @return Run
     * @throws \Zend_Exception
     */
    public function getRunner()
    {
        return \Zend_Registry::get('runner');
    }

    /**
     * Get items group type
     *
     * @return string
     */
    public function getGroupType()
    {
        return 'cols';
    }

    /**
     * Get project versions
     *
     * @return array
     */
    public function getProjectVersions()
    {
        $versions = array();
        $project  = Config\Project::getJiraProject();
        if ($project) {
            $result = $this->getRunner()->getApi()->getVersions($project);
            foreach ($result->getResult() as $item) {
                $group      = (bool)$item['released'] ? 'Released' : 'Unreleased';
                $versions[] = $item['name'];
            }
            //@startSkipCommitHooks
            $callFunction = function ($a, $b) {
                return -1 * version_compare($a, $b);
            };
            //@finishSkipCommitHooks
            usort($versions, $callFunction);
            foreach ($versions as $key => $item) {
                $versions[$key] = array(
                    'id'    => $item,
                    'value' => $item,
                );
            }
        }

        return $versions;
    }

    /**
     * Get helper
     *
     * @return Base
     */
    protected function _getHelper()
    {
        if (null === $this->_helper) {
            $this->_helper = new Base();
        }
        return $this->_helper;
    }
}
