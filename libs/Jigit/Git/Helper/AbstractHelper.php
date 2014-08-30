<?php
/**
 * Created by PhpStorm.
 * User: a.roslik
 * Date: 8/30/2014
 * Time: 11:24 PM
 */

namespace Jigit\Git\Helper;

use Jigit\Git;

/**
 * Class AbstractHelper
 *
 * @package Jigit\Git\Helper
 */
abstract class AbstractHelper
{
    /**
     * VCS engine
     *
     * @var Git
     */
    protected $_engine;

    /**
     * Get VCS engine
     *
     * @return Git
     */
    public function getEngine()
    {
        return $this->_engine;
    }

    /**
     * Set VCS engine
     *
     * @param Git $engine
     * @return $this
     */
    public function setEngine(Git $engine)
    {
        $this->_engine = $engine;
        return $this;
    }

    /**
     * Process help request
     *
     * @return mixed
     */
    abstract public function process();
}
