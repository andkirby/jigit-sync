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
    realpath(JIGIT_ROOT . '/'),
    realpath(JIGIT_ROOT . '/libs/'),
    realpath(JIGIT_ROOT . '/app/'),
);
set_include_path(implode(PATH_SEPARATOR, $includePaths));

/**
 * Initialize autoloader
 */
require_once JIGIT_ROOT . '/libs/Lib/Autoloader.php';
\Lib\Autoloader::register();

/**
 * Add namespace because not directory "chobie"
 */
\Lib\Autoloader::addNamespace('chobie/Jira', 'JiraRestApi/src/Jira');

/**
 * Initialize ErrorHandler
 */
\Lib\ErrorHandler::register();
