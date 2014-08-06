<?php
$systemId = md5(`wmic csproduct get uuid`); //windows only
$jiraPasswordContent = @file_get_contents('jira.password');
if (!$jiraPasswordContent) {
    echo 'Please use your JIRA password in the file "jira.password".' . PHP_EOL;
    exit(1);
} elseif (mb_strlen($jiraPasswordContent) < 30) { //let's agree that password won't more than 30 symbols
    $jiraPasswordHash = base64_encode($systemId . trim($jiraPasswordContent));
    //encrypt hash
    for ($i = 0; $i <= 30; $i++) {
        $jiraPasswordHash = str_replace($systemId[$i], "|$i|", $jiraPasswordHash);
    }
    $passId = md5($systemId);
    $jiraPasswordContent = "$passId $jiraPasswordHash";
    file_put_contents('jira.password', $jiraPasswordContent);
}

@list($passId, $jiraPasswordHash) = explode(' ', $jiraPasswordContent);

if ($passId != md5($systemId)) {
    echo 'Your system ID is not matched. Please set your JIRA password in the file "jira.password".' . PHP_EOL;
    exit(1);
}

//decrypt hash
for ($i = 30; $i >= 0; $i--) {
    $jiraPasswordHash = str_replace("|$i|", $systemId[$i], $jiraPasswordHash);
}
$jiraPassword = str_replace($systemId, '', base64_decode($jiraPasswordHash));
