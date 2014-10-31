<?php
/**
 * Define app root
 */
!defined('JIGIT_ROOT') && define('JIGIT_ROOT', __DIR__);

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
require_once JIGIT_ROOT . '/libs/Jigit/Autoloader.php';
spl_autoload_register('\Jigit\Autoloader::autoload');

/**
 * Register error handler
 */
set_error_handler('\Lib\ErrorHandler::handleError');
