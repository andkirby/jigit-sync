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

### CLI Manual

      action [parameter [parameter=value]]

      [action] - First verb in command.
      Available actions:
        report     - make report to identify problems.
        push-tasks - pushed tasks to done which added to a given version.

      Parameters
        p, project            - Project key.
        top                   - Target VCS branch/tag to compare with low branch.
        low                   - VCS low branch/tag to compare with fix version branch.
        ver                   - Target FixVersion name.
        i, in-progress        - Releasing status of the given FixVersion.
        debug                 - Debug mode.
        r, force-remote       - Force using remote branches.
                                It doesn't work with tags.
                                Please run "git fetch --all" in GIT.
        f, filter             - JQL types white list.
                                Format: type1,type2,[...],typeN
        c, bad-commit         - Status of checking bad commits
        s, simple-view        - Show simple issue info block in report
                                0     - Show Full available info
                                1     - Show only key and summary
                                line  - Show in one line for report "push-tasks"
                                        in format: "IssueKey: ver1, ver2"

      Examples:
        Check issues w/o FixVersion:

          php jira.php push-tasks p=prj

        Add required FixVersion into issues: (not implemented yet)

          php jira.php push-tasks p=prj update

        Add report of issues in the given FixVersion:

          php jira.php report p=prj ver=1.0.43 low=master top=hotfix/1.0.43

### Fast Install

    #not finished yet...
    git clone https://github.com/chobie/jigit-sync
    cd jigit-sync
    git submodule init
    git submodule update
    vim config/jira.password
    php jira.php h
