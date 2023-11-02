<?php
declare(strict_types=1);

namespace Ssch\Typo3rectorIssueGenerator\Contract;

use Ssch\Typo3rectorIssueGenerator\Dto\GithubIssue;
use Ssch\Typo3rectorIssueGenerator\ValueObject\GithubIssueId;

interface GithubIssueRepositoryInterface
{
    public function save(GithubIssue $githubIssue): GithubIssueId;
}