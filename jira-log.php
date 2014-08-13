<?php
//IN PROGRESS
require_once 'jira-init.php'; //initialize

$output = new JigitOutput();


$output->enableDecorator(true);
$version = 'v0.3.1';
$inProgress = $requiredFixVersionInProgress ? 'YES' : 'NO';
$activeSprintIdString = implode(', ', $activeSprintIds);
$output->add("JiGIT $version - JIRA GIT Synchronization Tool");
$output->add('GitHUB: https://github.com/andkirby/jigit-sync');
$output->disableDecorator();

$date = date('Y-m-d', strtotime('-1 day', time()));
print('Next Date ' . $date);
$log = `git --git-dir $gitRoot/.git/ log --pretty=format:"%h %cd %cn %s" --branches --after="$date 11:50:00"`;
print_r($log);
die;
echo $output->getOutputString();
