<?php
namespace Jigit\Jira\Jql\Helper;

use chobie\Jira\Issue;
use Jigit\Config;
use Jigit\Jira\Jql;

/**
 * Class WithoutFixVersion
 *
 * @package Jigit\Jira\Jql\Helper
 */
class WithoutFixVersion extends Standard
{
    /**
     * Issues in different branch
     *
     * @var array
     */
    protected $_inDifferentBranch = array();

    /**
     * Handle issue
     *
     * @param string $type
     * @param Issue  $issue
     * @return $this
     */
    public function handleIssue($type, Issue $issue)
    {
        //check fix version right?
//        if ($this->_isIssueFixVersionProper($issue)) {
//            return $this;
//        }
        return parent::handleIssue($type, $issue);
    }

    /**
     * Check fix version added properly
     *
     * @param Issue $issue
     * @return array
     */
    protected function _isIssueFixVersionProper($issue)
    {
        $prefix           = Config::getInstance()->getData('app/vcs/version/prefix');
        $targetFixVersion = Config\Project::getJiraTargetFixVersion();
        $fixVersions      = $this->_getIssueHelper()->getIssueFixVersions($issue);
        $affectedVersions = $this->_getIssueHelper()->getIssueAffectsVersions($issue);
        foreach ($affectedVersions as $affVer) {
            $affVer = ltrim($affVer, $prefix);
            foreach ($fixVersions as $fixVer) {
                $fixVer = ltrim($fixVer, $prefix);
                if ($affVer == trim($targetFixVersion, $prefix)
                    && version_compare($fixVer, $affVer, '>')
                ) {
                    return true;
                }
            }
        }
        return false;
    }
}
