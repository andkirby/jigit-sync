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
    reset($_GET);
    $action = key($_GET);
    unset($_GET[$action]);
    $runner = new App\Run();
    $runner->run($action, $_GET, $output);
    //@finishSkipCommitHooks
} catch (UserException $e) {
    if ($e->getCode() != 911) {
        //skip help exception
        $output->add('ERROR: ' . $e->getMessage());
    }
} catch (Exception $e) {
    $output->add('SYSTEM ERROR: ' . $e->getMessage());
    $output->add('TRACE: ' . PHP_EOL . $e->getTraceAsString());
    echo $output->getOutputString();
    exit(1);
}
echo $output->getOutputString();
