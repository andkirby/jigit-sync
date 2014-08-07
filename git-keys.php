<?php
/**
 * Get issues between different code versions
 */
$gitError = false;
$branchFound = (bool) `git --git-dir $gitRoot/.git/ branch -a --list $branchLow`;
if (!$branchFound) {
    $branchFound = (bool) `git --git-dir $gitRoot/.git/ tag --list $branchLow`;
    if (!$branchFound) {
        $output->add("ERROR: Branch or tag $branchLow not found.");
        $gitError = true;
    }
}
$branchFound = (bool) `git --git-dir $gitRoot/.git/ branch -a --list $branchTop`;
if (!$branchFound) {
    $branchFound = (bool) `git --git-dir $gitRoot/.git/ tag --list $branchTop`;
    if (!$branchFound) {
        $output->add("ERROR: Branch or tag $branchTop not found.");
        $gitError = true;
    }
}

if ($gitError) {
    echo $output->getOutputString();
    return;
}

$delimiter = '|@|';
$logDelimiter = '|@||';
$format = "%h$delimiter%s$logDelimiter";
$log = `git --git-dir $gitRoot/.git/ log $branchLow..$branchTop --pretty=format:"$format" --no-merges`;
$log = trim($log, $logDelimiter);
$logs = explode($logDelimiter, $log);
foreach ($logs as $log) {
    preg_match('/' . $project . '-[0-9]+/', $log, $matches);
    list($hash) = explode('|@|', trim($log));
    $keys[$matches[0]][] = $hash;
}

return $keys;
