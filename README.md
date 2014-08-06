jigit-sync
=============
# JiGIT Sync
## JIRA & GIT Synchronization Tool
This project designed to make synchronization between code under GIT and JIRA issues.

## Installation
### Requirements
- GIT commit messages must have related issue key.

Fixed PRJ-123: Some issue summary
- This application requires jira-api-restclient 
(https://github.com/chobie/jira-api-restclient.git).

### Build Notes in Issue
If you have build notes issue please use following format in summary: Build %required_fix_version%

### Important
Windows only.
Application works with Windows (it used unique Windows ID as a key to encrypt a password by simple script).

### Fast Install

    git clone https://github.com/chobie/jira-api-restclient.git
    git clone https://github.com/andkirby/jigit-sync.git
    cd jigit-sync
    echo "yourpassword" >> jira.password
    vim jira.password
    cp jira-common.php.dist jira-common.php
    vim jira-common.php
    php jira.php
