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
    realpath(APP_ROOT . '/'),
    realpath(APP_ROOT . '/libs/'),
    realpath(APP_ROOT . '/../jira-api-restclient/src'),
    realpath(APP_ROOT . '/app/'),
);
set_include_path(implode(PATH_SEPARATOR, $includePaths));

/**
 * Initialize autoloader
 */
require_once APP_ROOT . '/libs/Lib/Autoloader.php';
\Lib\Autoloader::register();

/**
 * Add namespace because not directory "chobie"
 */
\Lib\Autoloader::addNamespace('chobie/Jira', 'Jira');

/**
 * Initialize ErrorHandler
 */
\Lib\ErrorHandler::register();
