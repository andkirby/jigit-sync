<?php
$version = 'v0.5.1';
$inProgress = $requiredFixVersionInProgress ? 'YES' : 'NO';
$activeSprintIdString = implode(', ', $activeSprintIds);
$output->enableDecorator(true);
$output->add("JiGIT $version - JIRA GIT Synchronization Tool");
$output->add('GitHUB: https://github.com/andkirby/jigit-sync');
$output->disableDecorator();
