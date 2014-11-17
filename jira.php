<?php
require_once 'jira-init.php'; //initialize

use chobie\Jira as Jira;
use Jigit\Config;
use Jigit\Jira as JigitJira;
use Jigit\Output as Output;
use Jigit\UserException;

$cli = new Output\Cli(
    new Output()
);

try {
    parse_str(implode('&', array_slice($argv, 1)), $params);
    reset($params);
    $action = key($params);
    unset($params[$action]);
    $runner = new Jigit\Run();
    $report = $runner->run($action, $params);
    $cli->process($report->getJqlHelpers(), $runner->getVcs());
} catch (UserException $e) {
    $cli->addException($e);
} catch (Exception $e) {
    $cli->addException($e);
}
echo $cli->getOutput()->getOutputString();
if (isset($e)) {
    exit(1);
}
