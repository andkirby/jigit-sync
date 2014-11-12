<?php
$rootPath = __DIR__;
set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            get_include_path(),
            $rootPath . '/../libs/',
            $rootPath . '/app/',
            $rootPath . '/../app/',
        )
    )
);

require_once '../libs/Lib/Autoloader.php';
\Lib\Autoloader::register();
set_error_handler('\Lib\ErrorHandler::handleError');

$app = new \Lib\Dispatcher();
$app->run();
