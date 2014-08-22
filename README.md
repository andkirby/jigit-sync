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

### CLI keys
Example:

    php jira.php p=has low=v1.0.12 top=origin/hotfix/1.0.14 i=1 ver=v1.0.14
    p        - Project key.
    low      - VCS low branch/tag.
    top      - Target VCS branch/tag.
    i        - Version "In progress" status.
    v        - Version name.
    debug    - Version name.

### Fast Install

    git clone https://github.com/chobie/jira-api-restclient.git
    git clone https://github.com/andkirby/jigit-sync.git
    cd jigit-sync
    echo "yourpassword" >> jira.password
    vim jira.password
    cp jira-common.php.dist jira-common.php
    vim jira-common.php
    php jira.php
