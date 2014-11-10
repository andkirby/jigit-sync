<?php
namespace Jigit\Jira\Jql\Helper;

use chobie\Jira\Issue;
use Jigit\Jira\Jql;
use Jigit\Output;

/**
 * Class NotAffectsCode
 *
 * @package Jigit\Jira\Jql\Helper
 */
class NotAffectsCode extends DefaultHelper
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
        if ($this->_isIssueInAnotherBranch($issue)) {
            //TODO implement separate validator to identify such issues
            $this->_inDifferentBranch[$issue->getKey()] = $issue;
            return $this;
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
        return $this->_inDifferentBranch || parent::hasFound();
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

    /**
     * Add output
     *
     * @param Output $output
     * @return $this
     */
    public function addOutput($output)
    {
        parent::addOutput($output);
        $this->_addDifferentBranchIssuesOutput($output);
        return $this;
    }

    /**
     * Add different branch issues output
     *
     * @param Output $output
     * @return $this
     */
    protected function _addDifferentBranchIssuesOutput(Output $output)
    {
        if ($this->_inDifferentBranch) {
            $output->enableDecorator();
            $output->add('WARNING!!! Issues committed in a different branch.');
            $output->disableDecorator();
            $output->add('Keys: ' . implode(', ', array_keys($this->_inDifferentBranch)));
            $output->addDelimiter();

            foreach ($this->_inDifferentBranch as $issue) {
                $this->_addIssueIntoOutput($output, null, $issue);
            }
        }
        return $this;
    }
}
