<?php
/**
 * Created by PhpStorm.
 * User: a.roslik
 * Date: 8/30/2014
 * Time: 11:41 PM
 */

namespace Jigit\Dispatcher;
use Jigit\Output;
use Jigit\Vcs\InterfaceVcs;

/**
 * Interface Dispatcher
 *
 * @package Jigit\Dispatcher
 */
interface InterfaceDispatcher
{
    /**
     * Get VCS model
     *
     * @return InterfaceVcs
     */
    public function getVcs();

    /**
     * Run making report
     *
     * @param string $action
     * @param array  $params
     * @return $this
     */
    public function run($action, array $params);
}
