<?php
@include_once 'func.php';

//try to load vendor/autoload.php
@include_once 'autoload.php';

$includePaths = array(
    get_include_path(),
    realpath(__DIR__ . '/..') . '/framework',
    realpath(__DIR__ . '/..') . '/testsuite',
    realpath(__DIR__ . '/../..') . '/libs',
    realpath(__DIR__ . '/../../..') . '/jira-api-restclient',
);
define('PROJECT_ROOT', realpath(__DIR__ . '/../..'));
set_include_path(implode(PATH_SEPARATOR, $includePaths));

require_once 'Autoloader.php';
\Autoloader::register();

/**
 * Add alias for class loading because chobie directory does not exist
 */
\Autoloader::addAlias('chobie/Jira', 'src/Jira');

//set default PHPUnit error handler
set_error_handler('\PHPUnit_Util_ErrorHandler::handleError');

