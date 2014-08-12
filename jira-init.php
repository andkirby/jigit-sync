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
    realpath(__DIR__ . '/../jira-api-restclient/src'),
);
set_include_path(implode(PATH_SEPARATOR, $includePaths));

/**
 * Initialize autoloader
 */
require_once JIGIT_ROOT . '/Jigit/Autoloader.php';
spl_autoload_register('\Jigit\Autoloader::autoload');

/**
 * Init config
 */
\Jigit\Config::getInstance();

/**
 * Get user data
 */
require_once JIGIT_ROOT . '/jira-common.php'; //request settings

use \Jigit\Config\User as ConfigUser;

ConfigUser::setJiraUsername($jiraUser);
ConfigUser::setJiraUrl($jiraUrl);
ConfigUser::setJiraProject($project);
ConfigUser::setGitBranchTop($branchTop);
ConfigUser::setGitBranchLow($branchLow);
ConfigUser::setJiraTargetFixVersion($requiredFixVersion);
ConfigUser::setJiraTargetFixVersionInProgress($requiredFixVersionInProgress);
ConfigUser::setJiraActiveSprints($activeSprintIds);
ConfigUser::setProjectGitRoot($gitRoot);
