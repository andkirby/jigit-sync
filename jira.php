<?php
require_once 'jira-init.php'; //initialize

use \Jigit\Output as Output;
use \Jigit\Jira as JigitJira;
use \Jigit\Config\User as ConfigUser;
use \Jigit\Config;
use \chobie\Jira as Jira;
use Jigit\UserException;

$output = new Output();
try {
    require_once 'jira-header.php'; //get header content
    require_once 'jira-request.php'; //get request content

    $gitKeys       = require_once 'git-keys.php';
    $gitKeysString = implode(',', array_keys($gitKeys));
    $output->add('Found issues in GIT:');
    $output->add(JigitJira\KeysFormatter::format($gitKeysString, 7));

    Config::getInstance()->setData('jira_git_keys', $gitKeysString);

    /**
     * Connect to JIRA
     */
    $api = new Jira\Api(
        ConfigUser::getJiraUrl(),
        new Jira\Api\Authentication\Basic(
            ConfigUser::getJiraUsername(), ConfigUser::getPassword()
        )
    );

    $jqls    = new JigitJira\Jql();
    $jqlList = $jqls->getJqls();

    require_once 'jira-render-issues.php';

} catch (UserException $e) {
    $output->add('ERROR: ' . $e->getMessage());
} catch (Exception $e) {
    echo $e;
    exit(1);
}
echo $output->getOutputString();
