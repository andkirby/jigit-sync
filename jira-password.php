<?php
/**
 * Get JIRA password
 */
use \Jigit\Jira\Password as Password;
$password = new Password();
$jiraPassword = $password->getPassword();
