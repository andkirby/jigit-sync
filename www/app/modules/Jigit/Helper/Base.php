<?php
/**
 * Created by PhpStorm.
 * User: a.roslik
 * Date: 11/13/2014
 * Time: 2:13 PM
 */

namespace Jigit\Helper;

use Jigit\Config;

/**
 * Class Base
 *
 * @package Jigit\Helper
 */
class Base
{
    /**
     * Reserved project name of example file
     */
    const PROJECT_EXAMPLE = 'EXAMPLE';

    /**
     * Check JIRA credentials and URL
     *
     * @return bool
     */
    public function isInstalled()
    {
        return Config\Jira::getJiraUrl() && Config\Jira::getPassword()
        && Config\Jira::getUsername();
    }

    /**
     * Get available projects list
     *
     * @return array
     */
    public function getProjects()
    {
        $projects    = array();
        $projectsDir = $this->_getProjectsDir();
        $handle      = opendir($projectsDir);
        if ($handle) {
            //@startSkipCommitHooks
            while (false !== ($entry = readdir($handle))) {
                $entry = pathinfo($entry, PATHINFO_FILENAME);
                if (!$entry || $entry == self::PROJECT_EXAMPLE || $entry == '.') {
                    continue;
                }
                $projects[] = $entry;
            }
            //@finishSkipCommitHooks
            closedir($handle);
        }
        return $projects;
    }

    /**
     * Get projects directory
     *
     * @return string
     * @throws \Jigit\Exception
     */
    protected function _getProjectsDir()
    {
        return JIGIT_ROOT . DIRECTORY_SEPARATOR
        . Config::getInstance()->getData('app/config_files/base_dir')
        . DIRECTORY_SEPARATOR . Config::getInstance()->getData('app/config_files/project_dir');
    }
}
