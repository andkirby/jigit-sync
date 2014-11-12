<?php
$rootPath = __DIR__;
set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            get_include_path(),
            $rootPath . '/../libs/',
            $rootPath . '/app/modules/',
            $rootPath . '/../app/',
        )
    )
);

!defined('APP_ROOT') && define('APP_ROOT', __DIR__);

require_once __DIR__ . '/../libs/Lib/Autoloader.php';
\Lib\Autoloader::register();
/**
 * Add namespace because not directory "chobie"
 */
\Lib\Autoloader::addNamespace('chobie', 'JiraRestApi/src');

set_error_handler('\Lib\ErrorHandler::handleError');

$app = new \Lib\Dispatcher();
$app->setInitDb(false);
$app->run();
