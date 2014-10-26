<?php
/**
 * Created by PhpStorm.
 * User: a.roslik
 * Date: 10/26/2014
 * Time: 3:20 PM
 */

namespace JigitTest;

use JigitTest\Response\Collection;
use JigitTest\Response\Issue;

class Response extends Collection
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('issues');
    }

    /**
     * Set issues
     *
     * @param array $issues
     * @return $this
     */
    public function setIssues($issues)
    {
        $this->setItems($issues);
        return $this;
    }

    /**
     * Set issues
     *
     * @param Issue $issue
     * @return $this
     */
    public function addIssue($issue)
    {
        $this->addItem($issue);
        return $this;
    }

    /**
     * Get issues
     *
     * @return array
     */
    public function getIssues()
    {
        return $this->getItems();
    }
}
