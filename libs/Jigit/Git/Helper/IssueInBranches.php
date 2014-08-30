<?php
/**
 * Created by PhpStorm.
 * User: a.roslik
 * Date: 8/30/2014
 * Time: 11:14 PM
 */

namespace Jigit\Git\Helper;

use Jigit\Config;
use Jigit\Git;
use Jigit\Exception;

/**
 * Class IssueInBranches
 *
 * @package Jigit\Git\Helper
 */
class IssueInBranches extends AbstractHelper
{
    /**
     * Find issue commits in all repository
     *
     * @internal string $issueKey
     * @return bool|mixed
     * @throws Exception
     */
    public function process()
    {
        $issueKey = func_get_arg(0);
        if (!$issueKey) {
            throw new Exception('Issue key is not set.');
        }

        $gitRoot = Config\Project::getProjectGitRoot();
        //@startSkipCommitHooks
        $log = Git::run(`git --git-dir $gitRoot/.git/ log --all --no-merges --grep="$issueKey"`);
        //@finishSkipCommitHooks
        return (bool) $log;
    }
}
