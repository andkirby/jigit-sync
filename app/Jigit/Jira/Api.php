<?php
namespace Jigit\Jira;

use chobie\Jira as JiraLib;

/**
 * Class Api
 *
 * @package Jigit\Jira
 */
class Api extends JiraLib\Api
{
    /**
     * Process errors flag
     *
     * @var bool
     */
    protected $_processErrors = true;

    /**
     * Make API request
     *
     * @param string $method
     * @param string $url
     * @param array  $data
     * @param bool   $asJson
     * @param bool   $isFile
     * @param bool   $debug
     * @return JiraLib\Api\Result
     */
    public function api(
        $method = Api::REQUEST_GET,
        $url = '',
        $data = array(),
        $asJson = false,
        $isFile = false,
        $debug = false)
    {
        $result = (array)parent::api($method, $url, $data, true, $isFile, $debug);
        if ($result) {
            $result = new JiraLib\Api\Result($result);
            $this->_processErrors($result);
        }
        return $result;
    }

    /**
     * Process JIRA API errors
     *
     * @param JiraLib\Api\Result $result
     * @throws JiraLib\Api\Exception
     * @return $this
     */
    protected function _processErrors($result)
    {
        $apiResult = $result->getResult();
        if (!empty($apiResult['errorMessages'])) {
            throw new JiraLib\Api\Exception(
                'API errors: ' . PHP_EOL
                . implode(PHP_EOL, $apiResult['errorMessages'])
            );
        }
        return $this;
    }
}
