<?php
namespace Jigit\Block;

use Jigit\Block\Webix\Widget\Container\FormAbstract;
use Jigit\Config;
use Jigit\Git;
use Lib\Design\Renderer;

/**
 * Class Panel
 *
 * @package Jigit\Block
 */
class Panel extends FormAbstract
{
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
                'options'     => $this->getActions(),
            )
        );
        $this->_addElement(
            'project', 'combo', array(
                'placeholder' => 'Project',
                'options'     => $this->getProjects(),
            )
        );
        $project = Config\Project::getJiraProject();

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
            'submit', 'button', array(
                'value' => 'Send', 'type' => 'form'
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
        return array(
            array(
                'id'    => 'HAS',
                'value' => 'HAS',
            )
        );
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
                'id'    => 'report',
                'value' => 'Report',
            ),
            array(
                'id'    => 'push-tasks',
                'value' => 'Check version',
            ),
        );
    }

    /**
     * Get branches
     *
     * @return array
     */
    public function getBranches()
    {
        return $this->getVcs()->getBranches() + $this->getVcs()->getTags();
    }

    /**
     * Get VCS
     *
     * @return Git
     * @throws \Zend_Exception
     */
    public function getVcs()
    {
        return \Zend_Registry::get('vcs');
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
        return array();
    }
}
