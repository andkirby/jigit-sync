<?php
namespace Jigit\Jira\Jql\Helper;

use chobie\Jira\Issue;
use Jigit\Jira\Jql;

/**
 * Class NotAffectsCode
 *
 * @package Jigit\Jira\Jql\Helper
 */
class NotAffectsCode extends DefaultHelper
{
    /**
     * Extra type for issues list which affects another branches
     */
    const TYPE_IN_DIFFERENT_BRANCH = 'inDifferentBranch';

    /**
     * Add extra type
     */
    public function __construct()
    {
        parent::__construct();
        $this->setJql(
            array(
                'type'          => self::TYPE_IN_DIFFERENT_BRANCH,
                'message'       => 'WARNING!!! Issues committed in a different branch.',
                'jql'           => ' ',
                'in_progress'   => '',
            )
        );
    }

    /**
     * Handle issue
     *
     * @param string $type
     * @param Issue  $issue
     * @return $this
     */
    public function handleIssue($type, Issue $issue)
    {
        if ($this->_isIssueInAnotherBranch($issue)) {
            //add issue into another type
            $type = self::TYPE_IN_DIFFERENT_BRANCH;
        }
        return parent::handleIssue($type, $issue);
    }

    /**
     * Check found issues
     *
     * Added checking non-branch issues
     *
     * @return bool
     */
    public function hasFound()
    {
        return parent::hasFound();
    }

    /**
     * Try to find issue in another branch
     *
     * @param Issue $issue
     * @return array
     */
    protected function _isIssueInAnotherBranch($issue)
    {
        $helper = $this->getVcs()->getHelper('IssueInBranches');
        return $helper->process(
            $issue->getKey()
        );
    }
}
