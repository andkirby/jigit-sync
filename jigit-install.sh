#!/bin/sh
git clone https://github.com/chobie/jira-api-restclient.git
git clone https://github.com/andkirby/jigit-sync.git
cd jigit-sync
echo "yourpassword" >> jira.password
vim jira.password
cp jira-common.php.dist jira-common.php
vim jira-common.php
php jira.php
