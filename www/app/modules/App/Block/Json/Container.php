<?php
namespace App\Block\Json;

use App\Block\Json;

/**
 * Class Container
 *
 * Get JSON data from child blocks
 *
 * @package App\Block
 */
class Container extends Json
{
    /**
     * Get child JSON data
     *
     * @return string
     */
    public function getDataToJson()
    {
        $data = array();
        foreach ($this->getChildren() as $name => $block) {
            if ($block instanceof JsonChildInterface) {
                $data[$name] = array('data' => $block->getDataToJson());
            } else {
                $data[$name] = array('html' => $block->toHtml());
            }
        }
        return $data;
    }
}
