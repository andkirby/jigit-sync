<?php
namespace Jigit\Jira\Api;

use chobie\Jira as JiraLib;
use Jigit\Jira\Issue;

/**
 * Class Result
 *
 * @package Jigit\Jira\Api
 */
class Result extends JiraLib\Api\Result
{
    /**
     * Get issues
     *
     * It has been rewritten to use self Issue class
     *
     * @return array
     */
    public function getIssues()
    {
        $result = array();
        if (isset($this->result['issues'])) {
            $result = array();
            foreach ($this->result['issues'] as $issue) {
                $result[] = new Issue($issue);
            }
        }
        return $result;
    }
}
