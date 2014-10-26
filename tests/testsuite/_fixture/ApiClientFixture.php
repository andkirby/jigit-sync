<?php
/**
 * Created by PhpStorm.
 * User: a.roslik
 * Date: 10/26/2014
 * Time: 11:13 PM
 */

namespace _fixture;

use chobie\Jira\Api\Authentication\AuthenticationInterface;
use chobie\Jira\Api\Client\ClientInterface;

class ApiClientFixture implements ClientInterface
{
    /**
     * send request to the api server
     *
     * @param       $method
     * @param       $url
     * @param array $data
     * @param       $endpoint
     * @param       $credential
     * @return array|string
     *
     * @throws \Exception
     */
    public function sendRequest(
        $method, $url, $data = array(), $endpoint, AuthenticationInterface $credential, $isFile = false, $debug = false
    ) {
        // TODO: Implement sendRequest() method.
    }
}
