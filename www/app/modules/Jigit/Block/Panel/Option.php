<?php
namespace Jigit\Block\Panel;

use App\Block\Json\JsonChildInterface;
use Jigit\Block\Panel;

/**
 * Class Option
 *
 * @package Jigit\Block\Panel
 */
class Option extends Panel implements JsonChildInterface
{
    /**
     * Get JSON data
     *
     * @return string
     */
    public function getDataToJson()
    {
        return array(
            'top' => $this->getBranches(),
            'low' => $this->getBranches(),
            'ver' => $this->getProjectVersions(),
        );
    }
}
