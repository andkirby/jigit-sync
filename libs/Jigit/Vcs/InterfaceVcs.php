<?php
/**
 * Created by PhpStorm.
 * User: a.roslik
 * Date: 8/30/2014
 * Time: 11:41 PM
 */

namespace Jigit\Vcs;
use \Jigit\Dispatcher\InterfaceDispatcher;
use \Jigit\UserException;

/**
 * Interface VCS
 *
 * @package Jigit\Vcs
 */
interface InterfaceVcs
{
    /**
     * Get dispatcher
     *
     * @return InterfaceDispatcher
     */
    public function getDispatcher();

    /**
     * Get dispatcher
     *
     * @param InterfaceDispatcher $dispatcher
     * @return $this
     */
    public function setDispatcher(InterfaceDispatcher $dispatcher);

    /**
     * Process VCS command
     *
     * @param string $command
     * @return $this
     */
    static public function run($command);

    /**
     * Validate branches
     *
     * @param string $gitRoot
     * @param string $branch
     * @return bool
     * @throws UserException
     */
    public function isBranchValid($gitRoot, $branch);

    /**
     * Get VCS helper
     *
     * @param string $name
     * @param array  $options
     * @return \Jigit\Git\Helper\AbstractHelper
     * @todo Make common helper interface
     */
    public function getHelper($name, array $options = array());

    /**
     * Get requested commits list grouped by ISSUE key
     *
     * @return array
     */
    public function getCommits();
}
