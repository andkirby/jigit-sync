<?php
$inProgress = $requiredFixVersionInProgress ? 'YES' : 'NO';
$activeSprintIdString = implode(', ', $activeSprintIds);
echo <<<HEADER
JiGIT - JIRA GIT Synchronization Tool v0.2.0
https://github.com/andkirby/jigit-sync
=================================================
Project:             $project
Compare:             $branchTop -> $branchLow
Required FixVersion: $requiredFixVersion
Version in progress: $inProgress
Sprint:              $activeSprintIdString

HEADER;
