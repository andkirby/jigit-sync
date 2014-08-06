<?php
require_once 'jira-common.php'; //request settings
require_once 'jira-password.php'; //get jiraPassword
require_once 'jira-header.php'; //get header

$includePaths = array(
    get_include_path(),
    realpath(__DIR__ . '/../jira-api-restclient/src'),
);
set_include_path(implode(PATH_SEPARATOR, $includePaths));

spl_autoload_register('JiraAutoload::mageAutoload');

/**
 * Class JiraAutoload
 */
class JiraAutoload
{
    /**
     * Autoload
     *
     * @param string $class
     * @return void
     */
    static public function mageAutoload($class)
    {
        $file = str_replace('_', '/', $class) . '.php';
        $file = str_replace('\\', '/', $file);
        $file = str_replace('chobie/', '/', $file);
        require_once $file;
    }
}
