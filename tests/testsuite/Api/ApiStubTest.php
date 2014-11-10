<?php
use Jigit\Jira as Jira;
use JigitTest\Response;
use JigitTest\Response\Issue;

require_once __DIR__ . '/../_fixture/ApiAuthenticationFixture.php';
require_once __DIR__ . '/../_fixture/ApiClientFixture.php';

/**
 * Class ApiStubTest
 */
class ApiStubTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test Api
     */
    public function testApi()
    {
        /**
         * Prepare JQL response
         */
        $response = new Response();
        $response->addIssue(new Issue());
        $response->addIssue(new Issue());
        $response->addIssue(new Issue());
        $response->addIssue(new Issue());

        /** @var Jira\Api\Result $result */
        $result = $this->_getJiraApiMock($response)->search('');
        $this->assertInstanceOf(
            'chobie\Jira\Api\Result', $result
        );

        $this->assertEquals(4, $response->getTotal());
        $this->assertEquals(4, $result->getTotal());
    }

    /**
     * Get JIRA API mock object
     *
     * @param Response $response
     * @return Jira\Api|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getJiraApiMock($response)
    {
        /**
         * Required Authentication mock
         */
        $auth = $this->getMock('_fixture\ApiAuthenticationFixture', array(), array(), '', false);

        /**
         * Required Client mock
         */
        $client = $this->getMock('_fixture\ApiClientFixture', array(), array(), '', false);
        $client->expects($this->once())->method('sendRequest')->willReturn($response->toJson());

        /** @var PHPUnit_Framework_MockObject_MockObject|Jira\Api $api */
        $constructorPrams = array('example.com', $auth, $client);

        //prevent strict errors on make mock object
        $error = error_reporting(E_ALL);
        $api   = $this->getMock('\chobie\Jira\Api', array('getFields'), $constructorPrams, '');
        error_reporting($error);

        /**
         * Return static fields list
         */
        $fields = include __DIR__ . '/../_fixture/fields.php';
        $api->expects($this->any())->method('getFields')->willReturn($fields);
        return $api;
    }
}
