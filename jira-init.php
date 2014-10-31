<?php
/**
 * Define app root
 */
!defined('APP_ROOT') && define('APP_ROOT', __DIR__);

/**
 * Define include paths
 */
$includePaths = array(
    get_include_path(),
    realpath(__DIR__ . '/'),
    realpath(__DIR__ . '/libs/'),
    realpath(__DIR__ . '/../jira-api-restclient/src'),
);
set_include_path(implode(PATH_SEPARATOR, $includePaths));

/**
 * Initialize autoloader
 */
require_once APP_ROOT . '/libs/Lib/Autoloader.php';
\Lib\Autoloader::register();

/**
 * Initialize ErrorHandler
 */
\Lib\ErrorHandler::register();
