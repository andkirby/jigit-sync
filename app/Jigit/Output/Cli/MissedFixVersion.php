<?php
namespace Jigit\Output\Cli;

/**
 * Class MissedFixVersion
 *
 * @package Jigit\Output\Cli
 * @method \Jigit\Jira\Jql\Helper\MissedFixVersion getHelper()
 * @property \Jigit\Jira\Jql\Helper\MissedFixVersion _helper
 */
class MissedFixVersion extends DefaultHelper
{
    /**
     * Get issue content block
     *
     * @param string            $jqlType
     * @param \Jigit\Jira\Issue $issue
     * @return array|string
     */
    protected function _getIssueContentBlock($jqlType, $issue)
    {
        $strIssue = parent::_getIssueContentBlock($jqlType, $issue);

        $versions = $this->getHelper()->getRequiredIssueVersions($jqlType, $issue);
        if ($this->_isLineSimpleView()) {
            $versions = array_merge($versions['fix'], $versions['affect']);
            $versions = implode(', ', $versions);
            //return "line" issue view
            $strIssue .= ": $versions";
        } else {
            $requiredFixVersions = implode(', ', $versions['fix']);
            $requiredAffectVersions = implode(', ', $versions['affect']);
            $strIssue .= PHP_EOL . "REQUIRED FixVersion/s:    {$requiredFixVersions}";
            $strIssue .= PHP_EOL . "REQUIRED AffectsVersion/s:{$requiredAffectVersions}";
        }
        return $strIssue;
    }
}
