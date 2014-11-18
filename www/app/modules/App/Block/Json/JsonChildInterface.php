<?php
/**
 * Created by PhpStorm.
 * User: a.roslik
 * Date: 11/18/2014
 * Time: 2:26 AM
 */

namespace App\Block\Json;

/**
 * Interface JsonChildInterface
 *
 * @package App\Block\Json
 */
interface JsonChildInterface
{
    /**
     * Get JSON data
     *
     * @return string
     */
    public function getDataToJson();
}
