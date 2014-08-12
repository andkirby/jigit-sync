<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 12.08.2014
 * Time: 3:42
 */
$output->enableDecorator(true, true);
$output->add("Project:             $project");
$output->add("Compare:             $branchTop -> $branchLow");
$output->add("Required FixVersion: $requiredFixVersion");
$output->add("Version in progress: $inProgress");
$output->add("Sprint:              $activeSprintIdString");
$output->disableDecorator();
