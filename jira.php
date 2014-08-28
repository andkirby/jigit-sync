<?php
require_once 'jira-init.php'; //initialize

use \Jigit\Output as Output;
use \Jigit\Jira as JigitJira;
use \Jigit\Config;
use \chobie\Jira as Jira;
use Jigit\UserException;

$output = new Output();

try {
    //@startSkipCommitHooks
    parse_str(implode('&', array_slice($argv, 1)), $_GET);
    $project = isset($_GET['p']) ? $_GET['p'] : null;
    $runner = new App\Run();
    $runner->setOutput($output);
    $runner->setDebugMode(isset($_GET['debug']) ? $_GET['debug'] : false);
    $runner->run($project, $_GET);
    //@finishSkipCommitHooks
} catch (UserException $e) {
    if ($e->getCode() != 911) {
        //skip help exception
        $output->add('ERROR: ' . $e->getMessage());
    }
} catch (Exception $e) {
    $output->add('FATAL: ' . $e);
    echo $output->getOutputString();
    exit(1);
}
echo $output->getOutputString();
