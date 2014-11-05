#!/bin/sh
git clone https://github.com/chobie/jira-api-restclient.git
git clone https://github.com/andkirby/jigit-sync.git
cd jigit-sync
echo "yourpassword" >> config/jira.password
vim config/jira.password
cp config/local.yml.dist config/local.yml
vim config/local.yml
php jira.php h
